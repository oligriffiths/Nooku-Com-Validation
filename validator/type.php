<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Class ValidatorType
 *
 * Type validator. Validates the value is_{type}
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorType extends ValidatorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   Library\ObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'filter' => false,
            'type' => null,
            'strict' => false,
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
        $config = $this->getOptions();
        
		if(!$config->strict && is_string($value))
		{
			switch($config->type)
			{
				case 'long':
				case 'integer':
				case 'int':
					if(null !== filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)) return true;
					break;

				case 'real':
				case 'double':
				case 'float':
					if(null !== filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)) return true;
					break;

				case 'boolean':
				case 'bool':
                    if(null !== filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) return true;
					break;
			}
		}

        //Create function names
		$type = strtolower($config->type);
		$type = $type == 'boolean' ? 'bool' : $type;
		$isFunction = 'is_'.$type;
		$ctypeFunction = 'ctype_'.$type;

        //Validate functions
		$result = false;
        if (is_object($value) && $value instanceof $config->type) {
            $result = true;
        }else if (function_exists($isFunction) && call_user_func($isFunction, $value) === true) {
			$result = true;
		} elseif (function_exists($ctypeFunction) && call_user_func($ctypeFunction, $value) === true) {
			$result = true;
		}

		if(!$result){
			$message = $this->getMessage(gettype($value));
			throw new \RuntimeException($message);
		}

		return true;
	}
}
