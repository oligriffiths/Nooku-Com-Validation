<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Class ValidatorUrl
 *
 * URL validator
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorUrl extends ValidatorAbstract
{
	/**
	 * Validate the value is a url
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
		if($value instanceof Library\HttpUrlInterface) $value = (string) $value;

		return parent::_validate($value);
	}
}
