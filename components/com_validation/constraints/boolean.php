<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintBoolean extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'strict' => false,
			'message' => '{{ target }} must be a boolean, "{{ value }}" given',
		))->append(array(
			'validator_options' => array('strict' => $config->strict)
		));
		parent::_initialize($config);
	}
}