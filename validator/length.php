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
 * Class ValidatorLength
 *
 * Length validator.
 *
 * Ensures string length is between min and max
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorLength extends ValidatorAbstract
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
            'value_type' => 'string',
            'min' => 0,
            'max' => null,
            'message_exact' => 'This value should contain exactly {{min}} characters, {{value}} given',
            'message_min' => 'This value is too short. It should have {{min}} characters or more, {{value}} given',
            'message_max' => 'This value is too long. It should have {{max}} characters or less, {{value}} given',
            'charset' => 'UTF-8'
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
		if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			throw new \UnexpectedValueException('The value passed to '.__CLASS__.'::'.__FUNCTION__.' must be scalar, or implement __toString');
		}

        $config = $this->getConfig();
        
		if (function_exists('grapheme_strlen') && 'UTF-8' === $config->charset) {
			$length = grapheme_strlen($value);
		} elseif (function_exists('mb_strlen')) {
			$length = mb_strlen($value, $config->charset);
		} else {
			$length = strlen($value);
		}

		$message = null;
		if ($config->min == $config->max && $length != $config->min) {
			$message = $this->getMessage($length, 'message_exact');
		}

		if (null !== $config->max && $length > $config->max) {
			$message = $this->getMessage($length, 'message_max');
		}

		if (null !== $config->min && $length < $config->min) {
			$message = $this->getMessage($length, 'message_min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
