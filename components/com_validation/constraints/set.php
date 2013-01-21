<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 13:13
 */

class ComValidationConstraintSet extends KObjectSet
{
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		if($config->constraints) $this->addConstraints(KConfig::unbox($config->constraints));
	}


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
	 * Adds multiple constraints to the set
	 * @param $constraints
	 */
	public function addConstraints($constraints)
	{
		foreach($constraints AS $key => $constraint)
		{
			$options = array();

			if(!$constraint instanceof ComValidationConstraintInterface){

				if(is_array($constraint)){
					$options = $constraint;
					$constraint = $key;
				}

				if(strpos($constraint, '.') === false){
					$identifier = clone $this->getIdentifier();
					$identifier->name = $constraint;
				}
			}

			$this->addConstraint($constraint, $options);
		}
	}


	/**
	 * Returns a constraint by name if exists
	 * @param $constraint
	 * @return mixed|null
	 */
	public function getConstraint($constraint)
	{
		if($this->hasConstraint($constraint)){
			return $this->_object_set->offsetGet($constraint);
		}

		return null;
	}


	/**
	 * Returns true if constraint exists
	 * @param $constraint
	 * @return bool
	 */
	public function hasConstraint($constraint)
	{
		return $this->_object_set->offsetExists($constraint);
	}


	/**
	 * Validates all the constraints in the set
	 * @param $value
	 * @return bool
	 */
	public function validate($value)
	{
		$errors = array();

		foreach($this AS $constraint)
		{
			if($validator = $constraint->getValidator())
			{
				try{
					$validator->validate($value);
				}catch(KException $e){
					$errors[] = $e->getMessage();
				}
			}
		}

		return empty($errors) ? true : $errors;
	}
}