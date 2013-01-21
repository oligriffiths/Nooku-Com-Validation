<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 13:08
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintEmail extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} is not a valid email address, "{{ value }}" given'
		));
		parent::_initialize($config);
	}

}