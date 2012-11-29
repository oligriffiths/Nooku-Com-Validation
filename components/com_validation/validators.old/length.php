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
class ComValidationValidatorLength extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new ComValidationExceptionUnexpectedtype($value, 'string');
        }

        $stringValue = (string) $value;

        if (function_exists('grapheme_strlen') && 'UTF-8' === $constraint->charset) {
            $length = grapheme_strlen($stringValue);
        } elseif (function_exists('mb_strlen')) {
            $length = mb_strlen($stringValue, $constraint->charset);
        } else {
            $length = strlen($stringValue);
        }

        if ($constraint->min == $constraint->max && $length != $constraint->min) {
            throw new ComValidationExceptionValidator($constraint->exactMessage, array(
                '{{ value }}' => $stringValue,
                '{{ limit }}' => $constraint->min,
            ));

            return;
        }

        if (null !== $constraint->max && $length > $constraint->max) {
            throw new ComValidationExceptionValidator($constraint->maxMessage, array(
                '{{ value }}' => $stringValue,
                '{{ limit }}' => $constraint->max,
            ));

            return;
        }

        if (null !== $constraint->min && $length < $constraint->min) {
            throw new ComValidationExceptionValidator($constraint->minMessage, array(
                '{{ value }}' => $stringValue,
                '{{ limit }}' => $constraint->min,
            ));
        }
    }
}
