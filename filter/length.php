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
class FilterLength extends FilterAbstract
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
            'min' => 0,
            'max' => null,
            'charset' => 'UTF-8',
            'value_type' => 'string'
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
			$message = $this->getMessage($length, 'exact');
		}

		if (null !== $config->max && $length > $config->max) {
			$message = $this->getMessage($length, 'max');
		}

		if (null !== $config->min && $length < $config->min) {
			$message = $this->getMessage($length, 'min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
