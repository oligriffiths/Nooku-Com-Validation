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
 * Validates whether a value match or not given regexp pattern
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 *
 * @api
 */
class ComValidationValidatorRegex extends ComValidationValidatorDefault
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

        $value = (string) $value;

        if ($constraint->match xor preg_match($constraint->pattern, $value)) {
            throw new ComValidationExceptionValidator($constraint->message, array('{{ value }}' => $value));
        }
    }
}
