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
 *
 * @deprecated Deprecated since version 2.1, to be removed in 2.3.
 */
class ComValidationValidatorMin extends ComValidationValidatorDefault
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param ComValidationConstraintDefault $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_numeric($value)) {
            throw new ComValidationExceptionValidator($constraint->invalidMessage, array(
                '{{ value }}' => $value,
                '{{ limit }}' => $constraint->limit,
            ));

            return;
        }

        if ($value < $constraint->limit) {
            throw new ComValidationExceptionValidator($constraint->message, array(
                '{{ value }}' => $value,
                '{{ limit }}' => $constraint->limit,
            ));
        }
    }
}
