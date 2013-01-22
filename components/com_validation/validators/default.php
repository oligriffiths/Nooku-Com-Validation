<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorDefault extends KObject implements ComValidationValidatorInterface
{
	protected $_constraint;
	protected $_filter;
	protected $_error;

	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_constraint = $config->constraint;

		if($config->filter){
			if(!$config->filter instanceof KServiceIdentifier && strpos($config->filter,'.') === false){
				$identifier = clone $this->getIdentifier();
				$identifier->path = array('filter');
				$identifier->name = $config->filter;
				$config->filter = $identifier;
			}

			$this->_filter = $this->getService($config->filter, $config->constraint ? $config->constraint->getOptions()->toArray() : array());
		}
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'constraint' => null
		));

		if($config->filter !== false){
			$filter = $config->filter ?: $this->getIdentifier()->name;
			$config->filter = $filter;
		}
		parent::_initialize($config);
	}


	/**
	 * Sets the constraint in the validator
	 * @param ComValidationConstraintInterface $constraint
	 * @return mixed
	 */
	public function setConstraint(ComValidationConstraintInterface $constraint)
	{
		$this->_constraint = $constraint;
		return $this;
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	public function validate($value, $constraint = null)
	{
		$constraint = $constraint ?: $this->_constraint;

		//Clear any previous errors
		$this->_error = null;

		//Validate type
		if($this->checkType($value, $constraint) === null) return true;

		//Validate
		return $this->_validate($value, $constraint);
	}


	/**
	 * Checks the value conforms to the constraint value type
	 * @param $value
	 * @param ComValidationConstraintDefault $constraint
	 * @return bool
	 * @throws KException
	 */
	protected function checkType($value, ComValidationConstraintDefault $constraint)
	{
		//Check if value is null
		if(!$constraint->allow_null && is_null($value)){
			return null;
		}

		//Run type check on the value
		if($constraint->value_type){
			$result = true;
			switch($constraint->value_type){
				case 'string':
					if(!(is_string($value) || is_scalar($value)) && !method_exists($value, '__toString')) $result = false;
					break;

				case 'array':
					if(!is_array($value) && !$value instanceof \Countable) $result = false;
					break;

				default:
					$function = 'is_'.strtolower($constraint->value_type);
					if(function_exists($function) && !$function($value)) $result = false;
					break;
			}

			if(!$result){
				$message = $constraint->getMessage(gettype($value), 'message_invalid');
				throw new KException($message);
			}
		}

		return true;
	}


	/**
	 * Validates the value using the attached filter
	 * @param $value
	 * @param ComValidationConstraintDefault $constraint
	 * @return bool
	 * @throws KException
	 */
	protected function _validate($value, ComValidationConstraintDefault $constraint)
	{
		//Validate with filter
		$result = $this->_filter->validate($value, $constraint);
		if(!$result){
			$message = $constraint->getMessage($value);
			throw new KException($message);
		}

		return true;
	}


	/**
	 * @param $value
	 * @param null | ComValidationConstraintDefault $constraint
	 * @return bool
	 */
	public function isValid($value, $constraint = null)
	{
		try{
			$this->validate($value, $constraint);
			return true;

		}catch(KException $e)
		{
			$this->_error = $e->getMessage();
			return false;
		}
	}


	/**
	 * Returns any cached error message
	 *
	 * @return mixed
	 */
	public function getError()
	{
		return $this->_error;
	}
}
