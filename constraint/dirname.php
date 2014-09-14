<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintDirname extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be valid directory name, "{{ value }}" given',
		));
		parent::_initialize($config);
	}
}