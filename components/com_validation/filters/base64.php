<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterBase64 extends KFilterBase64
{
	/**
	 * Validate the value is base64 encoded
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		if(!parent::_validate($value)) return false;

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
	protected function _sanitize($value)
	{
		return null;
	}
}