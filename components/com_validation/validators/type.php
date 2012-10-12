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
class ComValidationValidatorType extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value) {
            return;
        }

        if($constraint->convert_string && is_string($value))
        {
            switch($constraint->type)
            {
                case 'long':
                case 'integer':
                case 'int':
                    if(preg_match('#[0-9]+#', $value)) $value = intval($value);
                    break;

                case 'real':
                case 'double':
                case 'float':
                    if(preg_match('#[0-9]+(\.[0-9]+)?#', $value)) $value = floatval($value);
                    break;

                case 'boolean':
                case 'bool':
                    if(strtolower($value) == 'true' || $value == '1') $value = true;
                    if(strtolower($value) == 'false' || $value == '0') $value = false;
                    break;
            }
        }

        if ($constraint->convert_bool) {
            if ($value == '0') {
                $value = false;
            } else if ($value == '1') {
                $value = true;
            }
        }

        $type = strtolower($constraint->type);
        $type = $type == 'boolean' ? 'bool' : $constraint->type;
        $isFunction = 'is_'.$type;
        $ctypeFunction = 'ctype_'.$type;

        if (function_exists($isFunction) && call_user_func($isFunction, $value)) {
            return;
        } elseif (function_exists($ctypeFunction) && call_user_func($ctypeFunction, $value)) {
            return;
        } elseif ($value instanceof $constraint->type) {
            return;
        }

        throw new ComValidationExceptionValidator($constraint->message, array(
            '{{ value }}' => is_object($value) ? get_class($value) : (is_array($value) ? 'Array' : (string) $value),
            '{{ type }}'  => $constraint->type,
        ));
    }
}
