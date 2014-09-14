<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintMax extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'max' => null,
			'message' => '{{ target }} should be {{ max }} or less, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}