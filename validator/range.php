<?php

namespace Nooku\Component\Validation;

use Nooku\Library;

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ValidatorRange extends ValidatorDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
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
			throw new \RuntimeException($message);
		}

		return true;
	}
}
