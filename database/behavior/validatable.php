<?php
/**
 * User: Oli Griffiths
 * Date: 02/10/2012
 * Time: 20:29
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class DatabaseBehaviorValidatable extends Library\DatabaseBehaviorAbstract
{
    protected $_constraints;

    protected $_isValid = array();

    /**
     * @var array
     */
    protected $_errors = array();


    /**
     * Constructor.
     *
     * @param 	object 	An optional Library\ObjectConfig object with configuration options
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        //Load DB constraints
        if($config->load_constraints){
            $this->loadConstraintsFromSchema(array_keys($config->constraints->toArray()));
        }

        //Load passed constraints
        foreach($config->constraints->toArray() AS $column => $constraints)
        {
            foreach($constraints AS $key => $constraint){
                $options = array();
                if(is_array($key)){
                    $options = $constraint;
                    $constraint = $key;
                }
                $this->addConstraint($column, $constraint, $options);
            }
        }
    }


    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional Library\ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'load_constraints' => true,
            'constraints' => array(),
            'priority'   => self::PRIORITY_LOWEST //Ensure this runs last so all other behaviors run that might affect the object data
        ));

        parent::_initialize($config);
    }


    /**
     * Only instances of ObjectArray are supported
     *
     * @return bool
     */
    public function isSupported()
    {
        $entity = $this->getMixer();
        return $entity instanceof Library\DatabaseTableInterface || $entity instanceof Library\ObjectArray;
    }


    /**
     * Before inserting data, validate it first
     *
     * @param Library\DatabaseContext $context
     * @return bool
     */
    protected function _beforeInsert(Library\DatabaseContext $context)
    {
        return $this->_beforeUpdate($context);
    }


    /**
     * Before updating data, validate it first
     *
     * @param Library\DatabaseContext $context
     * @return bool
     */
    protected function _beforeUpdate(Library\DatabaseContext $context)
    {
        if($context->data->isValidatable())
        {
            if(!$context->data->validate())
            {
                return false;
            }

            return true;
        }
    }


    /***********
     * Mixins
     ***********/

    /**
     * @param $column
     * @param $constraint
     * @param array $options
     * @return DatabaseBehaviorValidatable
     */
    public function addConstraint($column, $constraint, $options = array())
    {
        if(!isset($this->_constraints[$column])){
            $this->_constraints[$column] = $this->getObject('com:validation.constraint.set');
        }

        $options['message_target'] = ucfirst($column);
        $this->_constraints[$column]->addConstraint($constraint, $options);

        return $this;
    }


    /**
     * Returns the constraints
     * @return array
     */
    public function getConstraints($column = null)
    {
        return $column ? (isset($this->_constraints[$column]) ? $this->_constraints[$column] : array()) : $this->_constraints;
    }


    /**
     * @return mixed
     */
    public function validate()
    {
        $entity = $this->getMixer();

        $hash = spl_object_hash($entity);

        //Initialize the errors holder
        $this->_errors[$hash] = array();

        //Get the validation set and pass in constraints
        $identifier = $this->getIdentifier()->toArray();
        $identifier['path'] = array('validator');
        $identifier['name'] = 'set';
        if(!Library\ObjectManager::getInstance()->getClass($identifier)) $identifier['package'] = 'validation';

        //Get the constraint set
        $set = $this->getObject($identifier, array('constraints' => $this->getConstraints()));

        //Filter the data
        $data = $entity->toArray();

        if($entity instanceof Library\DatabaseRowInterface){

            $table = $entity->getTable();

            // Filter data based on column type
            foreach($data as $key => $value){

                //Filter column
                if($column = $table->getColumn($key)) {
                    if ($column->filter) {
                        $data[$key] = $this->getObject('filter.factory')->createChain($column->filter)->sanitize($value);
                    }
                }

                //Do string conversion for any object that has a toString
                if(is_object($data[$key]) && method_exists($data[$key], '__toString')) $data[$key] = (string) $data[$key];
            }
        }

        //Validate the data
        $result = $set->validate($data);
        $errors = $set->getErrors();

        //Store the errors
        $this->_errors[$hash] = $errors;

        //If success, return
        if($result) return true;

        $text = '';
        foreach($errors AS $error)
        {
            foreach($error AS $e)
            {
                $text .= 'Validation error: '.$e."<br />\n";
            }
        }

        throw new \RuntimeException($text);
    }


    /**
     * Returns the validation errors for a specific key, or all errors if no key suppied
     * @param null $key
     * @param bool $clear
     * @return array
     */
    public function getValidationErrors($key = null)
    {
        $entity = $this->getMixer();
        $hash = spl_object_hash($entity);
        if(!isset($this->_errors[$hash])) return array();

        if(!$key) return $this->_errors[$hash];

        return isset($this->_errors[$hash][$key]) ? (array) $this->_errors[$hash][$key] : array();
    }


    /**
     * Clears any cached validation messages for the object
     * @param null $key - clear the errors for a specific key or null to clear all
     * @return $this
     * @throws \UnexpectedValueException
     */
    public function clearValidationErrors($key = null)
    {
        $entity = $this->getMixer();
        $hash = spl_object_hash($entity);
        if(!isset($this->_errors[$hash])) return $this;

        if(!$key) $this->_errors[$hash] = array();
        unset($this->_errors[$hash][$key]);

        return $this;
    }


    /**
     * Checks if the required constraint is set
     * @param $column
     * @return bool
     */
    public function isRequired($column)
    {
        return $this->hasConstraint('required',$column);
    }


    /**
     * Checks if a constraint is set
     * @param $constraint
     * @param $column
     * @return bool
     */
    public function hasConstraint($constraint, $column)
    {
        $constraints = $this->getConstraints($column);
        if($constraint == 'required'){
            return isset($constraints['required']) || isset($constraints['notblank']) || isset($constraints['notnull']);
        }
        return isset($constraints[$constraint]);
    }


    /**
     * Load constraints from the database schema.
     *
     * Certain column name define specific constraints, eg, email, ip/ip_address
     * The column type is used to define the validation type and length sets a max constraint
     *
     * @return mixed
     */
    protected function loadConstraintsFromSchema($exclude = array())
    {
        $mixer = $this->getMixer();
        $columns = $mixer->getColumns();
        foreach($columns AS $id => $column)
        {
            if($column->primary || in_array($id, $exclude)) continue;

            $constraint_set = array();

            $required_type = 'required';
            if($column->name == 'email' || $column->name == 'email_address') $constraint_set['email'] = array();
            if($column->name == 'ip' || $column->name == 'ip_address') $constraint_set['ip'] = array();

            switch($column->type)
            {
                case 'date':        $constraint_set['date'] = array('allow_zeros' => !$column->required); break;
                case 'datetime':    $constraint_set['timestamp'] = array('allow_zeros' => !$column->required); break;
                case 'time':        $constraint_set['time'] = array('allow_zeros' => !$column->required); break;

                case 'int':
                case 'integer':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                    if($column->type == 'tinyint' && $column->length == 1) $constraint_set['boolean'] = array();
                    else $constraint_set['int'] = array();
                    break;

                case 'float':
                case 'double':
                case 'real':
                case 'double':
                case 'double precision':
                    $constraint_set['float'] = array();
                    break;

                case 'bit':
                case 'bool':
                case 'boolean':
                    $required_type = 'notnull'; //booleans can be 0, notblank fails on this
                    $constraint_set['boolean'] = array();
                    break;

                case 'varchar':
                case 'text':
                case 'tinytext':
                case 'mediumtext':
                case 'longtext':
                case 'blob':
                case 'tinyblob':
                case 'smallblob':
                case 'longblob':
                    $constraint_set['string'] = array();
                    break;
            }

            if($column->required) $constraint_set[$required_type] = array();

            if($column->length) $constraint_set['length'] = array('max' => $column->length);

            foreach($constraint_set AS $constraint => $options){
                $this->addConstraint($id, $constraint, $options);
            }
        }

        return $this;
    }
}