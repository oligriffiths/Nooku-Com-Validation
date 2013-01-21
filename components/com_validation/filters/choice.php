<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterChoice extends KFilterAbstract
{
	protected $_choices;


	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_choices = $config->choices->toArray();
	}


	protected function _initialize(KConfig $config)
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
	protected function _validate($value)
	{
		return in_array($value, $this->_choices);
	}


	/**
 	 * Sanitize the data, returns null if not in list of chocies
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	protected function _sanitize($value)
	{
		return !in_array($value, $this->_choices) ? null : $value;
	}
}