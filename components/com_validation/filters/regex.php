<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterRegex extends KFilterAbstract
{
	protected $_regex;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_regex = $config->regex;

		$regex = $this->_regex;
		if( trim($regex, '#') == $regex &&
			trim($regex, '/') == $regex &&
			trim($regex, '@') == $regex &&
			trim($regex, '{}') == $regex
		){
			throw new KException('Regex is missing starting and ending delimiters. (Use one of # / @ {})');
		}
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'regex' => null
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value according to the regex
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	protected function _validate($value)
	{
		return preg_match($this->_regex, $value);
	}

	/**
	 * Sanitize the data, returns null if value doesn't match regex
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