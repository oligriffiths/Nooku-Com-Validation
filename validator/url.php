<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ValidatorUrl extends ValidatorDefault
{
	/**
	 * Validate the value is a url
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
		if($value instanceof \RuntimeException) $value = (string) $value;
		return parent::_validate($value, $constraint);
	}
}
