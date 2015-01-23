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
    /**
     * @var FilterSet
     */
    protected $_filter_set;

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

        //Store filter set instance
        if(!$config->filter_set instanceof FilterSet){
            $this->_filter_set = $this->getObject('com://oligriffiths/validation.filter.set');
        }else{
            $this->_filter_set = $config->filter_set;
        }

        $columns = $config->columns->toArray();
        $filters = $config->filters->toArray();

        //Load DB validator
        if($config->load_db_schema || count($columns)){
            $this->loadFromSchema($columns, array_keys($filters));
        }

        //Load passed validators
        foreach($filters AS $column => $column_filters)
        {
            foreach($column_filters AS $key => $filter){
                $options = array();
                if(is_array($key)){
                    $options = $filter;
                    $filter = $key;
                }
                $this->addFilter($column, $filter, $options);
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
            'columns' => array(),
            'filters' => array(),
            'filter_set' => null,
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
                //Produce error message
                $text = '';
                foreach($this->getValidationErrors() AS $errors) {
                    foreach($errors AS $e) {
                        $text .= 'Validation error: '.$e."<br />\n";
                    }
                }

                //Set failed state
                $context->data->setStatus(Library\ModelEntityInterface::STATUS_FAILED);

                throw new \RuntimeException($text);
            }

            return true;
        }
    }


    /***********
     * Mixins
     ***********/

    /**
     * Adds a filter to the specified column
     *
     * @param $column - the column name
     * @param $validator - the validator name or identifier
     * @param array $options
     * @return DatabaseBehaviorValidatable
     */
    public function addFilter($column, $filter, $options = array())
    {
        if(!isset($options['message_target'])) $options['message_target'] = ucfirst($column);

        $this->_filter_set->addFilter($column, $filter, $options);

        return $this;
    }

    /**
     * Adds an array of filters to the specified column
     *
     * @param $column - the column name
     * @param $$alidator - the validator name or identifier
     * @param array $options
     * @return DatabaseBehaviorValidatable
     */
    public function addFilters($column, array $filters)
    {
        foreach($filters AS $key => $filter){
            $params = array();
            if(is_array($filter)){
                $params = $filter;
                $filter = $key;
            }

            $this->addFilter($column, $filter, $params);
        }

        return $this;
    }

    /**
     * Returns the validators
     *
     * @param null $column - specific column
     * @return FilterChain
     */
    public function getFilters($column = null)
    {
        return $column ? (isset($this->_filter_set[$column]) ? $this->_filter_set[$column] : null) : $this->_filter_set;
    }

    /**
     * @return mixed
     */
    public function validate(FilterSet $filter_set = null)
    {
        $entity = $this->getMixer();
        $hash = spl_object_hash($entity);

        //Define the filter set being used
        $filter_set = $filter_set ?: $this->_filter_set;

        //Initialize the errors holder
        $this->_errors[$hash] = array();

        //Grab the data that's to be validated
        $data = $entity->toArray();
        $data = array_intersect_key($data, $filter_set->toArray());

        //Filter to remove null data non-required data and convert objects to strings
        $data = array_filter($data, function(&$value, $key){

            //If required is not set and value is null, skip
            if(!$this->isRequired($key) && $value === null) return false;

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        //Validate the data
        if($filter_set->validate($data)) return true;

        //Store the errors
        $this->_errors[$hash] = $errors = $filter_set->getErrors();

        return false;
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
        return $this->hasFilter($column, 'required');
    }

    /**
     * Checks if a validator is set
     * @param $validator
     * @param $column
     * @return bool
     */
    public function hasFilter($column, $filter)
    {
        $chain = $this->getFilters($column);

        return $chain && $chain->hasFilter($filter);
    }

    /**
     * Load validators from the database schema.
     *
     * Certain column name define specific validators, eg, email, ip/ip_address
     * The column type is used to define the validation type and length sets a max constraint
     *
     * @return mixed
     */
    protected function loadFromSchema($include = array(), $exclude = array())
    {
        $mixer = $this->getMixer();
        $columns = $mixer->getColumns();

        //Eliminate unneeded columns
        if(count($include)) $columns = array_intersect_key($columns, array_flip($include));
        if(count($exclude)) $columns = array_diff_key($columns, array_flip($exclude));

        foreach($columns AS $name => $column) {

            if($column->primary) continue;

            $filters = array();

            if($column->name == 'email' || $column->name == 'email_address') $filters['email'] = array();
            if($column->name == 'ip' || $column->name == 'ip_address') $filters['ip'] = array();

            switch($column->type)
            {
                case 'date':        $filters['date'] = array('allow_zeros' => !$column->required); break;
                case 'datetime':    $filters['timestamp'] = array('allow_zeros' => !$column->required); break;
                case 'time':        $filters['time'] = array('allow_zeros' => !$column->required); break;

                case 'int':
                case 'integer':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                    if($column->type == 'tinyint' && $column->length == 1) $filters['boolean'] = array();
                    else $filters['int'] = array();
                    break;

                case 'float':
                case 'double':
                case 'real':
                case 'double':
                case 'double precision':
                    $filters['float'] = array();
                    break;

                case 'bit':
                case 'bool':
                case 'boolean':
                    $filters['boolean'] = array();
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
                    $filters['string'] = array();
                    break;
            }

            if($column->length) $filters['length'] = array('max' => $column->length);

            if($column->required) $filters['required'] = array();

            if(count($filters)) $this->addFilters($name, $filters);
        }

        return $this;
    }
}