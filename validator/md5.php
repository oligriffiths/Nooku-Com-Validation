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
 * Class ValidatorMd5
 *
 * MD5 string validator
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorMd5 extends ValidatorAbstract
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
			'message' => '{{message_target}} must be a valid MD5 hash, "{{value}}" given',
            'value_type' => 'string'
		));

		parent::_initialize($config);
	}
}