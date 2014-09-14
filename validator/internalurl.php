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
class ValidatorInternalurl extends ValidatorDefault
{
	/**
	 * Validate the value is an internal url
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
		if($value instanceof Library\HttpUrl) $value = (string) $value;

		return parent::_validate($value, $constraint);
	}
}
