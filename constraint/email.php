<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 13:08
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintEmail extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} is not a valid email address, "{{ value }}" given'
		));
		parent::_initialize($config);
	}

}