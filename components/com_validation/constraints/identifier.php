<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintIdentifier extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be valid identifier, "{{ value }}" given',
			'value_type' => null
		));
		parent::_initialize($config);
	}
}