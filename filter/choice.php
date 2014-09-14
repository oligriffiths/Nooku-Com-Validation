<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterChoice extends Library\FilterAbstract
{
	protected $_choices;


	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

		$this->_choices = $config->choices->toArray();
	}


	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'choices' => array()
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value is blank
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		return in_array($value, $this->_choices);
	}


	/**
 	 * Sanitize the data, returns null if not in list of chocies
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return !in_array($value, $this->_choices) ? null : $value;
	}
}