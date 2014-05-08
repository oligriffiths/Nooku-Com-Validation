<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintInternalurl extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be valid internal url, "{{ value }}" given',
			'value_type' => false
		));
		parent::_initialize($config);
	}
}