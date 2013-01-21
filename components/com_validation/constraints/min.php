<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintMin extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'min' => null,
			'message' => '{{ target }} should be {{ min }} or more, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}