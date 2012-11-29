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
 * ChoiceValidator validates that the value is one of the expected values.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorChoice extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (!$constraint->choices && !$constraint->callback) {
            throw new ConstraintDefinitionException('Either "choices" or "callback" must be specified on constraint Choice');
        }

        if (null === $value) {
            return;
        }

        if ($constraint->multiple && !is_array($value)) {
            throw new ComValidationExceptionUnexpectedtype($value, 'array');
        }

        if ($constraint->callback) {
            if (is_callable(array($this, $constraint->callback))) {
                $choices = call_user_func(array($this, $constraint->callback));
            } elseif (is_callable($constraint->callback)) {
                $choices = call_user_func($constraint->callback);
            } else {
                throw new ConstraintDefinitionException('The Choice constraint expects a valid callback');
            }
        } else {
            $choices = $constraint->choices;
        }

        if ($constraint->multiple) {
            foreach ($value as $_value) {
                if (!in_array($_value, $choices, $constraint->strict)) {
                    throw new ComValidationExceptionValidator($constraint->multipleMessage, array('{{ value }}' => $_value));
                }
            }

            $count = count($value);

            if ($constraint->min !== null && $count < $constraint->min) {
                throw new ComValidationExceptionValidator($constraint->minMessage, array('{{ limit }}' => $constraint->min));

                return;
            }

            if ($constraint->max !== null && $count > $constraint->max) {
                throw new ComValidationExceptionValidator($constraint->maxMessage, array('{{ limit }}' => $constraint->max));

                return;
            }
        } elseif (!in_array($value, $choices, $constraint->strict)) {
            throw new ComValidationExceptionValidator($constraint->message, array('{{ value }}' => $value));
        }
    }
}
