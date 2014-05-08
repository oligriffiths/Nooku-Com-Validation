<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class FilterMin extends Library\FilterAbstract
{
	protected $_min;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);
		$this->_min = $config->min;
	}


	protected function _initialize(Library\ObjectConfig $config)
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
	public function validate($value)
	{
		return $value >= $this->_min;
	}


	/**
	 * Sanitize the data, returns null if value less than min
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return $value < $this->_min ? null : $value;
	}
}