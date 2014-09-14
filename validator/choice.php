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
class ValidatorChoice extends ValidatorDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
		if (!in_array($value, $constraint->choices)) {
			throw new \RuntimeException($constraint->getMessage($value));
		}

		return true;
	}
}
