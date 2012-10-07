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
class ComValidationValidatorCollection extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof KDatabaseRowInterface || $value instanceof KConfig)) {
            throw new ComValidationExceptionUnexpectedtype($value, 'array, KConfig or KDatabaseRowsetInterface');
        }

	    if($value instanceof KConfig) $value = $value->toArray();
	    if($value instanceof KDatabaseRowInterface) $value = $value->getData();

        foreach ($constraint->fields as $field => $fieldConstraint) {

	        if(is_array($value) && array_key_exists($field, $value))
	        {
		        foreach ($fieldConstraint->constraints as $constr) {
			        $validator = $constr->getValidator();
			        $validator->validate($value[$field], $constr);
		        }
	        }else{
		        throw new ComValidationExceptionValidator($constraint->missingFieldsMessage, array(
			        '{{ field }}' => $field
		        ));
	        }
        }

        if (!$constraint->allowExtraFields) {
            foreach ($value as $field => $fieldValue) {
                if (!isset($constraint->fields[$field])) {
                    throw new ComValidationExceptionValidator($constraint->extraFieldsMessage, array(
                        '{{ field }}' => $field
                    ));
                }
            }
        }
    }
}
