<?php
/**
 * User: Oli Griffiths
 * Date: 02/10/2012
 * Time: 20:29
 */

class ComValidationDatabaseBehaviorValidatable extends KDatabaseBehaviorAbstract
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
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		//Load DB constraints
		if($config->load_constraints){
			$this->loadConstraintsFromDB($config->replace ? array_keys($config->constraints->toArray()) : array());
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
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'load_constraints' => true,
			'replace' => false,
			'constraints' => array()
		));

		parent::_initialize($config);
	}


	/**
	 * Mixin the behvior to the row, and load previous data from the session if set
	 *
	 * @param KCommandContext $context
	 */
	protected function _afterTableSelect(KCommandContext $context)
	{
		$data = $context->data;
		if($data instanceof KDatabaseRowAbstract || $data instanceof KDatabaseRowsetAbstract)
		{
			if($data->isValidatable())
			{
				$identifier = clone $this->getIdentifier();
				$identifier->path = null;
				$identifier->name = null;

				if($data instanceof KDatabaseRowAbstract) $data = array($data);

				//Get the rows primary key columns ot build identifier
				$identifier = clone $context->data->getIdentifier();
				$identifier->path = array('database','row');
				$identifier->name = KInflector::singularize($identifier->name);
				$identifier = (string) $identifier;

				//Check if there is session data for this identifier root
				if($hasSessionData = KRequest::has('session.data.'.$identifier,'raw')){
					foreach($data AS $row)
					{
						//Ensure behavior is mixed in
						if($row->isValidatable())
						{
							$this->loadFromSession($row);
						}
					}
				}
			}
		}
	}


	/**
	 * Before inserting data, validate it first
	 *
	 * @param KCommandContext $context
	 * @return bool
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		return $this->_beforeTableUpdate($context);
	}


	/**
	 * Before updating data, validate it first
	 *
	 * @param KCommandContext $context
	 * @return bool
	 */
	protected function _beforeTableUpdate(KCommandContext $context)
	{
		if($context->data instanceof KDatabaseRowAbstract)
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
	}


	/***********
	 * Mixins
	 ***********/

	/**
	 * @param $column
	 * @param $constraint
	 * @param array $options
	 * @return ComValidationDatabaseBehaviorValidatable
	 * @throws KDatabaseException
	 */
	public function addConstraint($column, $constraint, $options = array())
	{
		if(!isset($this->_constraints[$column])){
			$this->_constraints[$column] = $this->getService('com://site/validation.constraint.set');
		}

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
	 * @throws KDatabaseException
	 * @throws KException
	 */
	public function validate($store = true)
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KObjectArray){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KObjectArray');
		}

		//Check if the item is already validated
		if(null !== $valid = $this->isValid(null, false)) return $valid;

		//Clear any previous errors
		$hash = spl_object_hash($mixer);
		$this->_errors[$hash] = array();

        //Validate the data
        $data = $mixer->getData();

        if($mixer instanceof KDatabaseRowAbstract && $table = $mixer->getTable()){

            // Filter data based on column type
            foreach($data as $key => $value){
                if($column = $table->getColumn($key)) {
                    $data[$key] = $column->filter->sanitize($value);
                }
            }
        }


		//Get the validation get and pass in constraints
		$identifier = clone $this->getIdentifier();
		$identifier->path = 'validator';
		$identifier->name = 'set';
		$set = $this->getService($identifier, array('constraints' => $this->getConstraints()));

		//Validate the data
		$result = $set->validate($data);
		$errors = $set->getErrors();

		//Store the result
		$this->isValid($result);

		//Store the errors
		$this->_errors[$hash] = $errors;
		$mixer->setData(array('errors' => $errors), false);

		if($result === false){

			//Store the data in the session
			if($store)
			{
				$this->storeToSession($mixer);
			}

			$text = '';
			foreach($errors AS $column => $error)
			{
				foreach($error AS $e)
				{
					$text .= 'Validation error: ('.$column.') - '.$e."<br />\n";
				}
			}

			throw new KException($text);
		}else{
			$this->removeFromSession($mixer);
		}

		return $result;
	}

	/**
	 * Loads a rows data from the session
	 *
	 * @param null|KDatabaseRowInterface $row
	 * @return ComValidationDatabaseBehaviorValidatable
	 */
	public function loadFromSession($row = null, $clear = true)
	{
		$row = $row ? $row : $this->getMixer();

		//Get the rows primary key columns ot build identifier
		$identifier = (string) $row->getIdentifier();

		//Compile primary keys
		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);

		//Retrieve the data in the session to pre-populate the row
		if($prev_data = KRequest::get('session.data.'.$identifier, 'raw'))
		{
			$row_data = $row->getData();
			if(array_intersect_key($row_data, $prev_data) == $row_data)
			{
				$row->setData($prev_data);

				//Clear session data
				if($clear) KRequest::set('session.data.'.$identifier, null);
			}
		}

		return $this;
	}


	/**
	 * Stores a rows data in the session
	 * @param null|KDatabaseRowInterface $row
	 */
	public function storeToSession($row = null)
	{
		$row = $row ? $row : $this->getMixer();

		//Construct object identifier
		$identifier = (string) $row->getIdentifier();

		//Add the rows identifers
		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);

		//Casting as a kconfig and to array will convert and sub kconfigs back to arrays
		$data = new KConfig($row->getData());
		$data = $data->toArray();

		//Remove any objects
		array_walk_recursive($data, array($this, 'removeKObjects'));

		KRequest::set('session.data.'.$identifier, $data);
	}


	/**
	 * Removes objects from an array
	 * @param $item
	 * @param $key
	 */
	protected function removeKObjects(&$item, $key)
	{
		if($item instanceof KObject) $item = null;
	}


	/**
	 * Stores a rows data in the session
	 * @param null|KDatabaseRowInterface $row
	 */
	public function removeFromSession($row = null)
	{
		$row = $row ? $row : $this->getMixer();

		//Construct object identifier
		$identifier = (string) $row->getIdentifier();

		//Add the rows identifers
		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);

		KRequest::set('session.data.'.$identifier, null);
	}


	/**
	 * Checks if a mixer is previously set as valid
	 * A hash of the object and the data is used incase the data has changed
	 * @param null $valid
	 * @return null|object
	 */
	public function isValid($valid = null, $doValidation = true)
	{
		$mixer = $this->getMixer();
		$data = array_intersect_key($mixer->getData(), $this->getConstraints());

		ksort($data);
		unset($data['errors']);
		$hash = md5(spl_object_hash($mixer) . serialize($data));

		//If valid is not null, we're setting
		if($valid !== null){
			$this->_isValid[$hash] = (bool) $valid;
			return $mixer;
		}

		//Check if this item has been validated before
		if(isset($this->_isValid[$hash])) return $this->_isValid[$hash];

		//If validation is required, run and return result
		if($doValidation){
			try{
				return $this->validate();
			}catch(KException $e){
				return false;
			}
		}

		return null;
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
		if(!$constraints) return false;

		if($constraint == 'required'){

			return $constraints->hasConstraint('required') || $constraints->hasConstraint('notblank') || $constraints->hasConstraint('notnull');
		}
		return $constraints->hasConstraint($constraint);
	}


    /**
	 * @return array
	 * @throws KDatabaseException
	 */
	public function getValidationErrors($key = null, $clear = false)
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KObjectArray){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KObjectArray');
		}

		$errors = (array) $mixer->errors;

		if($key){
			$return = isset($errors[$key]) ? (array) $errors[$key] : array();

			if($clear){
				unset($errors[$key]);
				$mixer->setData(array('errors' => $errors), false);
			}

			return $return;
		}
		else{
			if($clear) $mixer->setData(array('errors' => array()), false);
			return $errors;
		}
	}


	/**
	 * Load constraints from the database schema.
	 *
	 * Certain column name define specific constraints, eg, email, ip/ip_address
	 * The column type is used to define the validation type and length sets a max constraint
	 *
	 * @return mixed
	 * @throws KDatabaseException
	 */
	protected function loadConstraintsFromDB($exclude = array())
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
				case 'date':        $constraint_set['date'] = array(); break;
				case 'datetime':    $constraint_set['timestamp'] = array(); break;
				case 'time':        $constraint_set['time'] = array(); break;

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

			if($column->length) $constraint_set['max'] = array('max' => $column->length);

			foreach($constraint_set AS $constraint => $options){
				$this->addConstraint($id, $constraint, $options);
			}
		}

		return $this;
	}
}