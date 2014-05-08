<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:03
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintMd5 extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{ target }} must be a valid MD5 hash, "{{ value }}" given',
		));
		parent::_initialize($config);
	}
}