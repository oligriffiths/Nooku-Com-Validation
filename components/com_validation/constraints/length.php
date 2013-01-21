<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:44
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintLength extends ComValidationConstraintDefault
{
	/**
	 * Supply the min and max values as parameters. Values are inclusive
	 * @param KConfig $config
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'min' => null,
			'max' => null,
			'message_exact' => 'This value should contain exactly {{ min }} characters, {{ value }} given',
			'message_min' => 'This value is too short. It should have {{ min }} characters or more, {{ value }} given',
			'message_max' => 'This value is too long. It should have {{ max }} characters or less, {{ value }} given',
			'charset' => 'UTF-8'
		));
		parent::_initialize($config);
	}


	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message_exact','message_max','message_min','min','max');
	}
}