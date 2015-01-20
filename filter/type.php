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
class FilterType extends FilterAbstract
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
            'type' => null
		));

		parent::_initialize($config);
	}

	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
        $config = $this->getOptions();

        //Create function names
		$type = strtolower($config->type);
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
