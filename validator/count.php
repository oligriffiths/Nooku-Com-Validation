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
 * Class ValidatorCount
 *
 * Count validator. Counts the number of items in an array
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorCount extends ValidatorAbstract
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
            'min' => null,
            'max' => null,
            'message_exact' => 'This collection should contain exactly {{min}} elements, {{value}} given',
            'message_min' => 'This collection should contain {{min}} elements or more, {{value}} given',
            'message_max' => 'This collection should contain {{max}} elements or less, {{value}} given',
            'value_type' => 'array'
		));

		parent::_initialize($config);
	}


	/**
	 * Validate a value
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
		if (!is_array($value) && !$value instanceof \Countable) {
			throw new \UnexpectedValueException('The value passed to '.__CLASS__.'::'.__FUNCTION__.' must be an array, or implement countable');
		}

		$count = count($value);
		$message = null;
        $options = $this->getConfig();

		if ($options->min == $options->max && $count != $options->min) {
			$message = $this->getMessage($count, 'message_exact');
		}

		if (null !== $options->max && $count > $options->max) {
			$message = $this->getMessage($count, 'message_max');
		}

		if (null !== $options->min && $count < $options->min) {
			$message = $this->getMessage($count, 'message_min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
