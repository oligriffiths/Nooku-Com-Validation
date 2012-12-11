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

	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_constraint = $config->constraint;
		$this->_filter = $config->filter;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'constraint' => null
		));

		if(!isset($config->filter)) $config->filter = $this->getService('com://site/validation.filter.'.$this->getIdentifier()->name, $config->constraint ? $config->constraint->getOptions()->toArray() : array());
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	public function validate($value, $constraint = null)
	{
		$constraint = $constraint ?: $this->_constraint;
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
			return false;
		}
	}
}
