<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 13:13
 */

class ComValidationConstraintSet extends KObjectSet
{
	/**
	 * Adds a constraint to the set
	 * @param ComValidationConstraintInterface|string $constraint
	 * @param array $options
	 * @return ComValidationConstraintSet
	 */
	public function addConstraint($constraint, $options = array())
	{
		$this->insert($constraint instanceof ComValidationConstraintInterface ? $constraint : $this->getService('com://site/validation.constraint.'.$constraint, $options));
		return $this;
	}


	/**
	 * Validates all the constraints in the set
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		foreach($this AS $constraint)
		{
			if($validator = $constraint->getValidator())
			{
				$validator->validate($value);
			}
		}

		return true;
	}
}