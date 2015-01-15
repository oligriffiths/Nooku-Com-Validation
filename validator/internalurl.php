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
 * Class ValidatorInternalurl
 *
 * Internal URL validator.
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorInternalurl extends ValidatorAbstract
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
            'message' => '{{target}} must be valid internal url, "{{value}}" given',
            'value_type' => false
        ));

        parent::_initialize($config);
    }


	/**
	 * Validate the value is an internal url
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
		if($value instanceof Library\HttpUrlInterface) $value = (string) $value;

		return parent::_validate($value);
	}
}
