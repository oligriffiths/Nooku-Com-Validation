<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ValidatorIdentifier extends ValidatorDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate the value is an identifier
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
        if($value instanceof Library\ObjectIdentifier) return true;

		if(!is_string($value) && !is_array($value)){
			throw new \RuntimeException($constraint->getMessage($value));
		}

		try{
			$this->getIdentifier($value);
			return true;
		}catch(\Exception $e){
			throw new \RuntimeException($constraint->getMessage($value));
		}
	}
}
