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
 * Class ValidatorBlank
 *
 * Blank validator. Ensures the value is blank.
 *
 * This means value is null or empty string. 0 is not considered blank
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorBlank extends ValidatorAbstract
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
			'message' => '{{message_target}} must be blank, "{{value}}" given',
		));

		parent::_initialize($config);
	}
}