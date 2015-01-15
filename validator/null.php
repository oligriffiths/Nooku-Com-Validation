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
 * Class ValidatorNull
 *
 * Null validator. Ensures a value is null
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorNull extends ValidatorAbstract
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'message' => '{{target}} must be null',
			'allow_null' => true,
			'value_type' => null
		));

		parent::_initialize($config);
	}
}