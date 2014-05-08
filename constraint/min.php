<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintMin extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'min' => null,
			'message' => '{{ target }} should be {{ min }} or more, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}