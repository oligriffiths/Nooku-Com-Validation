<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 13:13
 */

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintSet extends Library\ObjectSet
{
	public function __construct(Library\ObjectConfig $config = null)
	{
		parent::__construct($config);

		if($config->constraints) $this->addConstraints(Library\ObjectConfig::unbox($config->constraints));
	}

	/**
	 * Adds a constraint to the set
	 * @param ConstraintInterface|string $constraint
	 * @param array $options
	 * @return ConstraintSet
	 */
	public function addConstraint($constraint, $options = array())
	{
        if($constraint instanceof ConstraintInterface) return $this->insert($constraint);

        //Locate the constraint, fallback to default if not specific constraint exists
        $manager = Library\ObjectManager::getInstance();
		$identifier = $this->getIdentifier('com:validation.constraint.'.$constraint);
        if(!$manager->getClass($identifier)){
            $manager->registerAlias('com:validation.constraint.default', $identifier);
        }

        //Set the type
        $options['type'] = $constraint;

        //Get the constraint
        $constraint = $this->getObject($identifier, $options);

        //Ensure this is a valid constraint
        if(!$constraint instanceof ConstraintInterface) throw new \UnexpectedValueException('Constraint must implement ConstraintInterface');

        $this->insert($constraint);

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

			if(!$constraint instanceof ConstraintInterface){

				if(is_array($constraint)){
					$options = $constraint;
					$constraint = $key;
				}

				if(strpos($constraint, '.') === false){
					$identifier = $this->getIdentifier()->toArray();
					$identifier['name'] = $constraint;
				}
			}

			$this->addConstraint($constraint, $options);
		}
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
				}catch(\Exception $e){
					$errors[] = $e->getMessage();
				}
			}
		}

		return empty($errors) ? true : $errors;
	}
}