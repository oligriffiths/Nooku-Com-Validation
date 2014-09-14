<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterBase64 extends Library\FilterBase64
{
	/**
	 * Validate the value is base64 encoded
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		if(!parent::validate($value)) return false;

		return base64_encode(base64_decode($value)) == $value;
	}

	/**
	 * Sanitize the data, returns null
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return null;
	}
}