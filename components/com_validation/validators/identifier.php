<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorIdentifier extends ComValidationValidatorDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value is an identifier
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	protected function _validate($value, ComValidationConstraintDefault $constraint)
	{
		if(!$value instanceof KServiceIdentifier && !is_string($value)){
			throw new KException($constraint->getMessage($value));
		}

		if($value instanceof KServiceIdentifier) return true;

		try{
			$this->getIdentifier($value);
			return true;
		}catch(KException $e){
			throw new KException($constraint->getMessage($value));
		}
	}
}
