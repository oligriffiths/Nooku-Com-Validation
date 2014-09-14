<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintRegex extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'regex' => null,
			'message' => '{{ target }} does not validate against the regular expression "{{ regex }}", "{{ value }}" given',
			'value_type' => 'string'
		));
		parent::_initialize($config);
	}

	public function getRequiredOptions()
	{
		return array('message','regex');
	}
}