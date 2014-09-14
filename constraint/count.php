<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:44
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintCount extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'min' => null,
			'max' => null,
			'message_exact' => 'This collection should contain exactly {{ min }} elements, {{ value }} given',
			'message_min' => 'This collection should contain {{ min }} elements or more, {{ value }} given',
			'message_max' => 'This collection should contain {{ max }} elements or less, {{ value }} given',
			'value_type' => 'array'
		));
		parent::_initialize($config);
	}


	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message_exact','message_min','message_max','min','max');
	}
}