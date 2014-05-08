<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintBoolean extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
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