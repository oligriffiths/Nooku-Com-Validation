<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
interface ComValidationValidatorInterface
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
	 * @param null | ComValidationConstraintDefault $constraint
	 * @return mixed
	 */
	public function isValid($value, $constraint = null);
}
