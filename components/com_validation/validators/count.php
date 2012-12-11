<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorCount extends ComValidationValidatorDefault
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
	public function validate($value, $constraint = null)
	{
		$constraint = $constraint ?: $this->_constraint;

		if (null === $value) {
			return;
		}

		if (!is_array($value) && !$value instanceof \Countable) {
			throw new UnexpectedValueException('The value passed to '.__CLASS__.'::'.__FUNCTION__.' must be an array, or implement countable');
		}

		$count = count($value);
		$message = null;

		if ($constraint->min == $constraint->max && $count != $constraint->min) {
			$message = $constraint->getMessage($count, 'message_exact');
		}

		if (null !== $constraint->max && $count > $constraint->max) {
			$message = $constraint->getMessage($count, 'message_max');
		}

		if (null !== $constraint->min && $count < $constraint->min) {
			$message = $constraint->getMessage($count, 'message_min');
		}

		if($message !== null){
			throw new KException($message);
		}

		return true;
	}
}
