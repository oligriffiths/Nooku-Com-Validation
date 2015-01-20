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
class FilterIdentifier extends Library\FilterIdentifier
{
	/**
	 * Validate the value is an identifier
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
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
