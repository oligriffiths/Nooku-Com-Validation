<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterMin extends KFilterAbstract
{
	protected $_min;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		$this->_min = $config->min;
	}


	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'min' => null
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value is not less than min
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $value >= $this->_min;
	}


	/**
	 * Sanitize the data, returns null if value less than min
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	protected function _sanitize($value)
	{
		return $value < $this->_min ? null : $value;
	}
}