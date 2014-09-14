<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
interface ValidatorInterface
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, $constraint = null);

	/**
	 *
	 * @abstract
	 * @param $value
	 * @param null | ConstraintDefault $constraint
	 * @return mixed
	 */
	public function isValid($value, $constraint = null);


	/**
	 * Sets the constraint in the validator
	 * @param ConstraintInterface $constraint
	 * @return mixed
	 */
	public function setConstraint(ConstraintInterface $constraint);
}
