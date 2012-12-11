<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintChoice extends ComValidationConstraintDefault
{
	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message','choices');
	}
}