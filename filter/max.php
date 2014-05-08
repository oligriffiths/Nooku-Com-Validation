<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class FilterMax extends Library\FilterAbstract
{
	protected $_max;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);
		$this->_max = $config->max;
	}


	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'max' => null
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value is not greater than max
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		return $value <= $this->_max;
	}


	/**
	 * Sanitize the data, returns null if value greater than max
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return $value > $this->_max ? null : $value;
	}
}