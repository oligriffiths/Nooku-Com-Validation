<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintTrue extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be true, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}