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
class FilterUrl extends Library\FilterUrl
{
	/**
	 * Validate the value is a url
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
		if($value instanceof Library\HttpUrlInterface) $value = (string) $value;

		return parent::validate($value);
	}
}
