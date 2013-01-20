<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterBoolean extends KFilterBoolean
{
	protected $_strict;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_strict = $config->strict;
	}


	/**
	 * Validate the value is boolean.
	 *
	 * In strict mode, the value must be either true or false
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		if(!parent::_validate($value)) return false;

		if($this->_strict) return is_bool($value);

		return true;
	}
}