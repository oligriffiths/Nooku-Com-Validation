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
class FilterInternalurl extends Library\FilterInternalurl
{
	/**
	 * Validate the value is an internal url
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
		if($value instanceof Library\HttpUrlInterface) $value = (string) $value;

		return parent::validate($value);
	}
}
