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
 */
class ComValidationValidatorCount extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value) && !$value instanceof \Countable) {
            throw new ComValidationExceptionUnexpectedtype($value, 'array or \Countable');
        }

        $count = count($value);

        if ($constraint->min == $constraint->max && $count != $constraint->min) {
            throw new ComValidationExceptionValidator($constraint->exactMessage, array(
                '{{ count }}' => $count,
                '{{ limit }}' => $constraint->min,
            ));

            return;
        }

        if (null !== $constraint->max && $count > $constraint->max) {
            throw new ComValidationExceptionValidator($constraint->maxMessage, array(
                '{{ count }}' => $count,
                '{{ limit }}' => $constraint->max,
            ));

            return;
        }

        if (null !== $constraint->min && $count < $constraint->min) {
            throw new ComValidationExceptionValidator($constraint->minMessage, array(
                '{{ count }}' => $count,
                '{{ limit }}' => $constraint->min,
            ));
        }
    }
}
