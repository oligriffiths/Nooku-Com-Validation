<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorRange extends ComValidationValidatorDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	protected function _validate($value, ComValidationConstraintDefault $constraint)
	{
		$message = null;
		if ($constraint->min == $constraint->max && $value != $constraint->min) {
			$message = $constraint->getMessage($value, 'message_exact');
		}

		if (null !== $constraint->max && $value > $constraint->max) {
			$message = $constraint->getMessage($value, 'message_max');
		}

		if (null !== $constraint->min && $value < $constraint->min) {
			$message = $constraint->getMessage($value, 'message_min');
		}

		if($message !== null){
			throw new KException($message);
		}

		return true;
	}
}
