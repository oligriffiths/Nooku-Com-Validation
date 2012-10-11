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
class ComValidationValidatorDate extends ComValidationValidatorDefault
{
    const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})$/';

    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value || '' === $value || $value instanceof \DateTime) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new ComValidationExceptionUnexpectedtype($value, 'string');
        }

        $value = (string) $value;

        if (!preg_match(static::PATTERN, $value, $matches) || (!checkdate($matches[2], $matches[3], $matches[1]) && !($constraint->allow_nulldate && $matches[1] && $matches[2] && $matches[3]) )) {
            throw new ComValidationExceptionValidator($constraint->message, array('{{ value }}' => $value));
        }
    }
}
