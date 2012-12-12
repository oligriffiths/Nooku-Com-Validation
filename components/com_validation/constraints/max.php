<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintMax extends ComValidationConstraintDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'max' => null,
			'message' => '{{ target }} should be {{ max }} or less, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}