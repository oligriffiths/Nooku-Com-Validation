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
 * Class ValidatorNotnull
 *
 * Validates a value is not null
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorNotnull extends ValidatorAbstract
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
			'message' => '{{target}} must not be null',
			'allow_null' => true,
			'value_type' => null
		));

		parent::_initialize($config);
	}
}