<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintIp extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} is not a valid IP address, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}