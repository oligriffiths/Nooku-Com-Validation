<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 13:13
 */

class ComValidationConstraintSet extends KObjectSet
{
	public function addConstraint($name, $options = array())
	{
		$this->insert($this->getService('com://site/validation.constraint.'.$name, $options));
		return $this;
	}


	public function validate($value)
	{
		foreach($this AS $constraint)
		{
			if($validator = $constraint->getValidator())
			{
				return $validator->validate($value);
			}
		}

		return true;
	}
}