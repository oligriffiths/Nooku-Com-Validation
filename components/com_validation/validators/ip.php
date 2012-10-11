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
 * Validates whether a value is a valid IP address
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 *
 * @api
 */
class ComValidationValidatorIp extends ComValidationValidatorDefault
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

        switch ($constraint->version) {
            case ComValidationConstraintIp::V4:
               $flag = FILTER_FLAG_IPV4;
               break;

            case ComValidationConstraintIp::V6:
               $flag = FILTER_FLAG_IPV6;
               break;

            case ComValidationConstraintIp::V4_NO_PRIV:
               $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE;
               break;

            case ComValidationConstraintIp::V6_NO_PRIV:
               $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE;
               break;

            case ComValidationConstraintIp::ALL_NO_PRIV:
               $flag = FILTER_FLAG_NO_PRIV_RANGE;
               break;

            case ComValidationConstraintIp::V4_NO_RES:
               $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_RES_RANGE;
               break;

            case ComValidationConstraintIp::V6_NO_RES:
               $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE;
               break;

            case ComValidationConstraintIp::ALL_NO_RES:
               $flag = FILTER_FLAG_NO_RES_RANGE;
               break;

            case ComValidationConstraintIp::V4_ONLY_PUBLIC:
               $flag = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
               break;

            case ComValidationConstraintIp::V6_ONLY_PUBLIC:
               $flag = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
               break;

            case ComValidationConstraintIp::ALL_ONLY_PUBLIC:
               $flag = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
               break;

            default:
                $flag = null;
                break;
        }

        if (!filter_var($value, FILTER_VALIDATE_IP, $flag)) {
            throw new ComValidationExceptionValidator($constraint->message, array('{{ value }}' => $value));
        }
    }
}
