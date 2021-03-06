<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintIp extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} is not a valid IP address, "{{ value }}" given'
		));
		parent::_initialize($config);
	}
}