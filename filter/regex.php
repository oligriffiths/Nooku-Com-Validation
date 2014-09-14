<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterRegex extends Library\FilterAbstract
{
	protected $_regex;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

		$this->_regex = $config->regex;

		if( trim($this->_regex, '#') == $this->_regex &&
			trim($this->_regex, '/') == $this->_regex &&
			trim($this->_regex, '@') == $this->_regex &&
			trim($this->_regex, '{}') == $this->_regex
		){
			throw new \UnexpectedValueException('Regex is missing starting and ending delimiters. (Use one of # / @ {})');
		}
	}

	protected function _initialize(Library\ObjectConfig $config)
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
	public function validate($value)
	{
		return (bool) preg_match($this->_regex, $value);
	}

	/**
	 * Sanitize the data, returns null if value doesn't match regex
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