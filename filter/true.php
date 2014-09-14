<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterTrue extends Library\FilterAbstract
{
	/**
	 * Validate the value is true
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		return $value === true;
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
		return $this->validate($value) ? $value : null;
	}
}