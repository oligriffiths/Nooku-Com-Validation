<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorLength extends ComValidationValidatorDefault
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
		if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			throw new UnexpectedValueException('The value passed to '.__CLASS__.'::'.__FUNCTION__.' must be scalar, or implement __toString');
		}

		if (function_exists('grapheme_strlen') && 'UTF-8' === $constraint->charset) {
			$length = grapheme_strlen($value);
		} elseif (function_exists('mb_strlen')) {
			$length = mb_strlen($value, $constraint->charset);
		} else {
			$length = strlen($value);
		}

		$message = null;
		if ($constraint->min == $constraint->max && $length != $constraint->min) {
			$message = $constraint->getMessage($length, 'message_exact');
		}

		if (null !== $constraint->max && $length > $constraint->max) {
			$message = $constraint->getMessage($length, 'message_max');
		}

		if (null !== $constraint->min && $length < $constraint->min) {
			$message = $constraint->getMessage($length, 'message_min');
		}

		if($message !== null){
			throw new KException($message);
		}

		return true;
	}
}
