<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterFalse extends KFilterAbstract
{
	/**
	 * Validate the value is false
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $value === false;
	}

	/**
	 * Sanitize the data, returns null
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	protected function _sanitize($value)
	{
		return $this->_validate($value) ? $value : null;
	}
}