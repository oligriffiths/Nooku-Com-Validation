<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:44
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintRange extends ComValidationConstraintDefault
{
	/**
	 * Supply the min and max values as parameters. Values are inclusive
	 * @param KConfig $config
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'min' => null,
			'max' => null,
			'message_min' => 'This value should be {{ min }} or more, {{ value }} given',
			'message_max' => 'This value should be {{ max }} or less, {{ value }} given',
		));
		parent::_initialize($config);
	}


	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message_max','message_min','min','max');
	}
}