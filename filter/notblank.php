<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class FilterNotblank extends FilterBlank
{
	/**
	 * Validate the value is not blank
	 * A value is not blank if it is not '' and not null
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		return !parent::validate($value);
	}

	/**
	 * Sanitize the data, returns $value
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return $value;
	}
}