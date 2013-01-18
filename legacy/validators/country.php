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
 * Validates whether a value is a valid country code
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorCountry extends ComValidationValidatorDefault
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

        if (!in_array($value, \Symfony\Component\Locale\Locale::getCountries())) {
            throw new ComValidationExceptionValidator($constraint->message, array('{{ value }}' => $value));
        }
    }
}
