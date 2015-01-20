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
class FilterCount extends FilterAbstract
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
            'min' => null,
            'max' => null,
		));

		parent::_initialize($config);
	}


	/**
	 * Validate a value
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
		if (!is_array($value) && !$value instanceof \Countable) {
			throw new \UnexpectedValueException('The value passed to '.__CLASS__.'::'.__FUNCTION__.' must be an array, or implement countable');
		}

		$count = count($value);
		$message = null;
        $options = $this->getConfig();

		if ($options->min == $options->max && $count != $options->min) {
			$message = $this->getMessage($count, 'exact');
		}

		if (null !== $options->max && $count > $options->max) {
			$message = $this->getMessage($count, 'max');
		}

		if (null !== $options->min && $count < $options->min) {
			$message = $this->getMessage($count, 'min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
