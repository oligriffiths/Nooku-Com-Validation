<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class FilterBlank extends Library\FilterAbstract
{
	/**
	 * Validate the value is blank
	 * Blank values are any value that is not '' or null
	 * Thus, 0 is a valid value
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		return !('' !== $value && null !== $value);
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