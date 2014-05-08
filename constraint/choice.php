<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ConstraintChoice extends ConstraintDefault
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