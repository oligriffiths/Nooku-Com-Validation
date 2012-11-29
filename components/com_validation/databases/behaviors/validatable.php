<?php
/**
 * User: Oli Griffiths
 * Date: 02/10/2012
 * Time: 20:29
 */

class ComValidationDatabaseBehaviorValidatable extends KDatabaseBehaviorAbstract
{
	/**
	 * @var array
	 */
	protected $_constraints_db;

	/**
	 * @var array
	 */
	protected $_constraints_table;


	protected $_constraints;

	/**
	 * @var array
	 */
	protected $_errors = array();

	public function __construct(KConfig $config = null)
	{
		$config->append(array(
			'constraints' => array()
		));
		parent::__construct($config);

		$this->_constraints_table = $config->constraints->toArray();
		$this->loadConstraints();
	}


	/**
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
	 * @param KCommandContext $context
	 * @return bool
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		return $this->_beforeTableUpdate($context);
	}


	/**
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


    /**
     * Loads a rows data in the session
     * @param null|KDatabaseRowInterface $row
     * @return ComValidationDatabaseBehaviorValidatable
     */
    public function loadFromSession($row = null)
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
                KRequest::set('session.data.'.$identifier, null);
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

        //Retrieve the data in the session to pre-populate the row
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

		//Clear any previous errors
		$hash = spl_object_hash($mixer);
		$this->_errors[$hash] = array();

        //Get the validation get and pass in constraints
		$identifier = clone $this->getIdentifier();
		$identifier->path = 'validator';
		$identifier->name = 'set';
		$set = $this->getService($identifier, array('constraints' => $this->getConstraints()));

        //Validate the data
        $data = $mixer->getData();
        $data = $mixer instanceof KDatabaseRowAbstract && $mixer->getTable() ? $mixer->getTable()->filter($data, true) : $data;
		$result = $set->validate($data);
		$errors = $set->getErrors();

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
			$return = isset($errors[$key]) ? $errors[$key] : array();

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
     * @param $column
     * @param $constraint
     * @param array $options
     * @return ComValidationDatabaseBehaviorValidatable
     * @throws KDatabaseException
     */
    public function addConstraint($column, $constraint, $options = array())
    {
        $mixer = $this->getMixer();
        if(!$mixer instanceof KDatabaseRowAbstract)
        {
            throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
        }

        if(!isset($this->_constraints[$column])){
            $this->_constraints[$column] = array();
        }

        if($constraint instanceof ComValidationConstraintDefault){
            $this->_constraints[$column][] = $constraint;
        }else{
            $this->_constraints[$column][$constraint] = $options;
        }

        return $this;
    }


	/**
	 * Returns the constraints
	 * @return array
	 */
	public function getConstraints()
	{
		return $this->_constraints;
	}


    /**
	 * @return mixed
	 * @throws KDatabaseException
	 */
	protected function loadConstraints()
	{
		$this->loadConstraintsFromDB();
		$this->_constraints = array_merge($this->_constraints_db, $this->_constraints_table);

		return $this->_constraints;
	}


	/**
	 * @return mixed
	 * @throws KDatabaseException
	 */
	protected function loadConstraintsFromDB()
	{
		if(!isset($this->_constraints_db))
		{
			$mixer = $this->getMixer();
			$constraints = array();
			$columns = $mixer->getColumns();
			foreach($columns AS $id => $column)
			{
				if($column->primary) continue;

				$constraint_set = array();

                $required_type = 'notblank';
				if($column->name == 'email' || $column->name == 'email_address') $constraint_set[] = 'email';
				if($column->name == 'ip' || $column->name == 'ip_address') $constraint_set[] = 'ip';
				if($column->name == 'image') $constraint_set[] = 'image';

				switch($column->type)
				{
					case 'date': $constraint_set[] = array('type' => 'date', 'allow_nulldate' => true); break;
					case 'datetime': $constraint_set[] = array('type' => 'datetime', 'allow_nulldate' => true); break;
					case 'time': $constraint_set[] = 'time'; break;

					case 'int':
					case 'integer':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
					case 'float':
					case 'double':
                    case 'real':
                    case 'double':
                    case 'double precision':
                    case 'bit': // this needed here until the query object handles booleans and the database adapter
                                // handles bit columns - Oli Oct 2012
                        $required_type = 'notnull'; //integers can be 0, notblank fails on this
                        if($column->length == 1 && ($column->type == 'tinyint' || $column->type == 'bit')) $constraint_set['type'] = array('type' => 'boolean', 'convert_bool' => true, 'convert_string' => true);
                        else $constraint_set['type'] = array('type' => 'numeric', 'convert_string' => true);
                    break;

                    case 'bool':
                    case 'boolean':
                        $required_type = 'notnull'; //integers can be 0, notblank fails on this
						$constraint_set['type'] = array('type' => 'boolean', 'convert_bool' => true);
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
                        $constraint_set['type'] = array('type' => 'string');
						break;
				}

                if($column->required) $constraint_set[] = $required_type;

				if($column->length) $constraint_set['maxlength'] = array('limit' => $column->length);

				if(!empty($constraint_set)) $constraints[$id] = $constraint_set;
			}

			$this->_constraints_db = $constraints;
		}

		//Set the mixers constraints
		return $this->_constraints_db;
	}
}