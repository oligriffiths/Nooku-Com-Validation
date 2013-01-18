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
 * Validator for Callback constraint
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorCallback extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($object, ComValidationConstraintDefault $constraint)
    {
        if (null === $object) {
            return;
        }

        // has to be an array so that we can differentiate between callables
        // and method names
        if (!is_array($constraint->methods)) {
            throw new ComValidationExceptionUnexpectedtype($constraint->methods, 'array');
        }

        $methods = $constraint->methods;

        foreach ($methods as $method) {
            if (is_array($method)) {
                if (!is_callable($method)) {
                    throw new ConstraintDefinitionException(sprintf('"%s::%s" targeted by Callback constraint is not a valid callable', $method[0], $method[1]));
                }

                call_user_func($method, $object, $constraint);
            } else {
                if (!method_exists($object, $method)) {
                    throw new ConstraintDefinitionException(sprintf('Method "%s" targeted by Callback constraint does not exist', $method));
                }

                $object->$method($constraint);
            }
        }
    }
}
