<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:44
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintLength extends ConstraintDefault
{
	/**
	 * Supply the min and max values as parameters. Values are inclusive
	 * @param Library\ObjectConfig $config
	 */
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'min' => 0,
			'max' => null,
			'message_exact' => 'This value should contain exactly {{ min }} characters, {{ value }} given',
			'message_min' => 'This value is too short. It should have {{ min }} characters or more, {{ value }} given',
			'message_max' => 'This value is too long. It should have {{ max }} characters or less, {{ value }} given',
			'charset' => 'UTF-8'
		));
		parent::_initialize($config);
	}


	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message_exact','message_max','message_min','max');
	}
}