<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 14:38
 */

class ComValidationValidatorSet extends KObjectSet
{
	protected $_errors = array();

	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		if($config->constraints) $this->addConstraints(KConfig::unbox($config->constraints));
	}


	public function addConstraints($constraints)
	{
		foreach($constraints AS $column => $constraintset)
		{
			if(is_array($constraintset)){
				$set = $this->getService('com://site/validation.constraint.set');

				foreach($constraintset AS $key => $rule)
				{
					$options = array();
					if(is_array($rule))
					{
						$options = $rule;
						$rule = $key;
					}

					$set->addConstraint($rule, array('options' => $options));
				}

				$constraintset = $set;
			}

			if($constraintset instanceof ComValidationConstraintSet)
			{
				$this->addConstraintSet($column, $constraintset);
			}
		}
	}


	public function addConstraintSet($handle, ComValidationConstraintSet $constraintset)
	{
		$this->_object_set->offsetSet($handle, $constraintset);
		return $this;
	}


	public function getConstraintSet($handle)
	{
		if($this->_object_set->offsetExists($handle))
		{
			return $this->_object_set->offsetGet($handle);
		}

		return null;
	}


	public function validate($data)
	{
		$errors = array();
		foreach($data AS $key => $value)
		{
			if($constraints = $this->getConstraintSet($key))
			{
				try{
					$constraints->validate($value);
				}catch(Exception $e)
				{
					if(!isset($errors[$key])) $errors[$key] = array();

					$errors[$key][] = $e->getMessage();
				}
			}
		}

		$this->_errors = $errors;
		return count($errors) ? false : true;
	}


	public function getErrors()
	{
		return $this->_errors;
	}
}