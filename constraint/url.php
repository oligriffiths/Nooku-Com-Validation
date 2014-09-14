<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintUrl extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be valid url, "{{ value }}" given',
			'value_type' => false
		));
		parent::_initialize($config);
	}
}