<?php

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorType extends ComValidationValidatorDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	protected function _validate($value, ComValidationConstraintDefault $constraint)
	{
		if($constraint->convert_string && is_string($value))
		{
			switch($constraint->type)
			{
				case 'long':
				case 'integer':
				case 'int':
					if(filter_var($value, FILTER_VALIDATE_INT) == $value) $value = filter_var($value, FILTER_VALIDATE_INT);
					break;

				case 'real':
				case 'double':
				case 'float':
					if(filter_var($value, FILTER_VALIDATE_FLOAT) == $value) $value = filter_var($value, FILTER_VALIDATE_FLOAT);
					break;

				case 'boolean':
				case 'bool':
					if(strtolower($value) == 'true' || $value === '1' || $value === 1) $value = true;
					if(strtolower($value) == 'false' || $value === '0' || $value ===0) $value = false;
					break;
			}
		}

		if ($constraint->convert_bool) {
			if ($value === 0) {
				$value = false;
			} else if ($value === 1) {
				$value = true;
			}
		}

		$type = strtolower($constraint->type);
		$type = $type == 'boolean' ? 'bool' : $type;
		$isFunction = 'is_'.$type;
		$ctypeFunction = 'ctype_'.$type;


		$result = false;
		if (function_exists($isFunction) && call_user_func($isFunction, $value)) {
			$result = true;
		} elseif (function_exists($ctypeFunction) && call_user_func($ctypeFunction, $value)) {
			$result = true;
		} elseif ($value instanceof $constraint->type) {
			$result = true;
		}

		if(!$result){
			$message = $constraint->getMessage(gettype($value));
			throw new KException($message);
		}

		return true;
	}
}
