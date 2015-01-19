<?php
/**
 * Validation Component
 *
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/oligriffiths/Nooku-Validation-Component for the canonical source repository
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Class ValidatorIdentifier
 *
 * Identifier validator. Ensures the passed identifier is valid. Can be object or string
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorIdentifier extends ValidatorAbstract
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
            'message' => '{{message_target}} must be valid identifier, "{{value}}" given',
            'value_type' => null,
			'filter' => false
		));

		parent::_initialize($config);
	}


	/**
	 * Validate the value is an identifier
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
        if($value instanceof Library\ObjectIdentifierInterface) return true;

		if(!is_string($value) && !is_array($value)){
			throw new \RuntimeException($this->getMessage($value));
		}

		try{
			$this->getIdentifier($value);
			return true;
		}catch(\Exception $e){
			throw new \RuntimeException($this->getMessage($value));
		}
	}
}
