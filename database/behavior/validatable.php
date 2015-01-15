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
    protected $_validators = array();

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

        //Load DB validator
        if($config->load_db_schema){
            $this->loadFromSchema(array_keys($config->validators->toArray()));
        }

        //Load passed validators
        foreach($config->validators->toArray() AS $column => $validators)
        {
            foreach($validators AS $key => $validator){
                $options = array();
                if(is_array($key)){
                    $options = $validator;
                    $validator = $key;
                }
                $this->addValidator($column, $validator, $options);
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
            'load_db_schema' => true,
            'table_filter' => true,
            'validators' => array(),
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
     * @param $column - the column name
     * @param $$validator - the validator name or identifier
     * @param array $options
     * @return DatabaseBehaviorValidatable
     */
    public function addValidator($column, $validator, $options = array())
    {
        if(!isset($this->_validators[$column])){
            $this->_validators[$column] = $this->getObject('com:validation.validator.set');
        }

        if(!isset($options['message_target'])) $options['message_target'] = ucfirst($column);

        $this->_validators[$column]->addValidator($validator, $options);

        return $this;
    }


    /**
     * Returns the validators
     *
     * @param null $column - specific column
     * @return array
     */
    public function getValidators($column = null)
    {
        return $column ? (isset($this->_validators[$column]) ? $this->_validators[$column] : array()) : $this->_validators;
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

        //Get the validator set
        $set = $this->getObject('com://oligriffiths/validation.validator.set', array('validators' => $this->getValidators()));

        //Filter the data
        $data = $entity->toArray();

        //Filter the entity data
        if($this->getConfig()->table_filter && $entity instanceof Library\DatabaseRowInterface){

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
     * Checks if the required validator is set
     * @param $column
     * @return bool
     */
    public function isRequired($column)
    {
        return $this->hasValidator('required',$column);
    }


    /**
     * Checks if a validator is set
     * @param $validator
     * @param $column
     * @return bool
     */
    public function hasValidator($validator, $column)
    {
        $validators = $this->getValidator($column);
        if($validator == 'required'){
            return isset($validators['required']) || isset($validators['notblank']) || isset($validators['notnull']);
        }
        return isset($validators[$validator]);
    }


    /**
     * Load validators from the database schema.
     *
     * Certain column name define specific validators, eg, email, ip/ip_address
     * The column type is used to define the validation type and length sets a max constraint
     *
     * @return mixed
     */
    protected function loadFromSchema($exclude = array())
    {
        $mixer = $this->getMixer();
        $columns = $mixer->getColumns();
        foreach($columns AS $name => $column)
        {
            if($column->primary || in_array($name, $exclude)) continue;

            $validator_set = array();

            $required_type = 'required';
            if($column->name == 'email' || $column->name == 'email_address') $validator_set['email'] = array();
            if($column->name == 'ip' || $column->name == 'ip_address') $validator_set['ip'] = array();

            switch($column->type)
            {
                case 'date':        $validator_set['date'] = array('allow_zeros' => !$column->required); break;
                case 'datetime':    $validator_set['timestamp'] = array('allow_zeros' => !$column->required); break;
                case 'time':        $validator_set['time'] = array('allow_zeros' => !$column->required); break;

                case 'int':
                case 'integer':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                    if($column->type == 'tinyint' && $column->length == 1) $validator_set['boolean'] = array();
                    else $validator_set['int'] = array();
                    break;

                case 'float':
                case 'double':
                case 'real':
                case 'double':
                case 'double precision':
                    $validator_set['float'] = array();
                    break;

                case 'bit':
                case 'bool':
                case 'boolean':
                    $required_type = 'notnull'; //booleans can be 0, notblank fails on this
                    $validator_set['boolean'] = array();
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
                    $validator_set['string'] = array();
                    break;
            }

            if($column->required) $validator_set[$required_type] = array();

            if($column->length) $validator_set['length'] = array('max' => $column->length);

            foreach($validator_set AS $validator => $options){
                $this->addValidator($name, $validator, $options);
            }
        }

        return $this;
    }
}