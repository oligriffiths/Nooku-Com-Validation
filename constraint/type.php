<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 13:08
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintType extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'type' => null,
			'convert_string' => false,
			'convert_bool' => false,
			'message' => '{{ target }} is not a valid {{ type }}, "{{ value }}" given'
		));
		parent::_initialize($config);
	}

	public function getRequiredOptions()
	{
		return array('message','type');
	}
}