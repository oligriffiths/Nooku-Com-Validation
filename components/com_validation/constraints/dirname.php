<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintDirname extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be valid directory name, "{{ value }}" given',
		));
		parent::_initialize($config);
	}
}