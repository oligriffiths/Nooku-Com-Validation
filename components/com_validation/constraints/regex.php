<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintRegex extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'regex' => null,
			'value_type' => 'string'
		));
		parent::_initialize($config);
	}

	public function getRequiredOptions()
	{
		return array('message','regex');
	}
}