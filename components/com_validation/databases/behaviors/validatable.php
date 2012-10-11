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
	protected $_constraints_db = array();
	/**
	 * @var array
	 */
	protected $_constraints = array();
	/**
	 * @var array
	 */
	protected $_errors = array();


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
				$identifier = $context->data->getIdentifier();
				$identifier->path = array('database','row');
				$identifier->name = KInflector::singularize($identifier->name);
				$identifier = (string) $identifier;
				$pks = array();

				//Check if there is session data for this identifier root
				if(KRequest::get('session.data.'.$identifier,'raw'))
				{
					//Compile primary keys
					foreach($context->data->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $pks[] = $column_id;
				}

				foreach($data AS $row)
				{
					//Ensure behavior is mixed in
					if($row->isValidatable())
					{
						//Construct object identifier
						$id = $identifier;
						foreach($pks AS $pk) $id .= '.'.$row->get($pk);

						//Retrieve the data in the session to pre-populate the row
						if($prev_data = KRequest::get('session.data.'.$id, 'raw'))
						{
							$row->setData($prev_data, false);

							//Clear session data
							KRequest::set('session.data.'.$id, null);
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
	 * @param $mixer
	 */
	public function setMixer($mixer)
	{
		static $loaded = array();

		parent::setMixer($mixer);

		if($mixer instanceof KDatabaseRowAbstract && $mixer->isValidatable()){
			$hash = spl_object_hash($mixer);
			if(!isset($loaded[$hash])){
				$loaded[$hash] = true;
				$this->getConstraints();
			}
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

		$hash = spl_object_hash($mixer);

		if(!isset($this->_constraints[$hash])){
			$this->_constraints[$hash] = array();
		}

		if(!isset($this->_constraints[$hash][$column])){
			$this->_constraints[$hash][$column] = array();
		}

		if($constraint instanceof ComValidationConstraintDefault){
			$this->_constraints[$hash][$column][] = $constraint;
		}else{
			$this->_constraints[$hash][$column][$constraint] = $options;
		}

		return $this;
	}


	/**
	 * @return mixed
	 * @throws KDatabaseException
	 * @throws KException
	 */
	public function validate($store = true)
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		//Clear any previous errors
		$hash = spl_object_hash($mixer);
		$this->_errors[$hash] = array();

		$set = $this->getService('com://site/validation.validator.set', array('constraints' => $mixer->getConstraints()));

		$result = $set->validate($mixer->getData());

		if(!$result){

			//Store the data in the session
			if(!$store)
			{
				//Construct object identifier
				$id = (string) $mixer->getIdentifier();

				//Add the rows identifers
				foreach($mixer->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $id .= '.'.$mixer->get($column_id);
				
				//Retrieve the data in the session to pre-populate the row
				KRequest::set('session.data.'.$id, $mixer->getData();
			}

			$errors = $set->getErrors();

			//Store the errors
			$this->_errors[$hash] = $errors;

			$text = '';
			foreach($errors AS $column => $error)
			{
				foreach($error AS $e)
				{
					$text .= 'Validation error: ('.$column.') - '.$e."<br />\n";
				}
			}

			throw new KException($text);
		}

		return $result;
	}


	/**
	 * @return array
	 * @throws KDatabaseException
	 */
	public function getValidationErrors()
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		$hash = spl_object_hash($mixer);
		return isset($this->_errors[$hash]) ? $this->_errors[$hash] : array();
	}


	/**
	 * @return array
	 */
	public function loadConstraints()
	{
		return array();
	}


	/**
	 * @return mixed
	 * @throws KDatabaseException
	 */
	public function getConstraints()
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		$hash = spl_object_hash($mixer);
		if(!isset($this->_constraints[$hash])){

			$db_constraints             = $this->getConstraintsFromDB();
			$constraints                = $mixer->loadConstraints();
			$this->_constraints[$hash]  = array_merge($db_constraints, $constraints);
		}
		return $this->_constraints[$hash];
	}


	/**
	 * @return mixed
	 * @throws KDatabaseException
	 */
	protected function getConstraintsFromDB()
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract){
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		$identifier = (string) $mixer->getTable()->getIdentifier();
		if(!isset($this->_constraints[$identifier]))
		{
			$constraints = array();
			$columns = $mixer->getTable()->getColumns();
			foreach($columns AS $id => $column)
			{
				if($column->primary) continue;

				$constraint_set = array();

				if($column->required) $constraint_set[] = 'notblank';
				if($column->name == 'email') $constraint_set[] = 'email';
				if($column->name == 'ip' || $column->name == 'ip_address') $constraint_set[] = 'ip';
				if($column->name == 'image') $constraint_set[] = 'image';

				switch($column->type)
				{
					case 'date': $constraint_set[] = 'date'; break;
					case 'datetime': $constraint_set[] = 'datetime'; break;
					case 'time': $constraint_set[] = 'time'; break;

					case 'int':
					case 'integer':
					case 'bool':
					case 'boolean':
					case 'float':
					case 'double':
						$constraint_set['type'] = array('type' => $column->type);
						break;

					case 'tinyint':
					case 'smallint':
					case 'mediumint':
					case 'bigint':
						if($column->length == 1 && $column->type == 'tinyint') $constraint_set['type'] = array('type' => 'boolean');
						else $constraint_set['type'] = array('type' => 'int');
						break;

					case 'real':
					case 'double':
					case 'double precision':
						$constraint_set['type'] = array('type' => 'float');
						break;

					case 'bit':
						$constraint_set['type'] = array('type' => 'boolean');
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

				if($column->length) $constraint_set['maxlength'] = array('limit' => $column->length);

				if(!empty($constraint_set)) $constraints[$id] = $constraint_set;
			}

			$this->_constraints_db[$identifier] = $constraints;
		}

		//Set the mixers constraints
		return $this->_constraints_db[$identifier];
	}
}