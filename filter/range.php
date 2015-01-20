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
 * Class ValidatorRange
 * @package Oligriffiths\Component\Validation
 */
class FilterRange extends FilterAbstract
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
            'value_type' => 'numeric'
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
        $config = $this->getConfig();

		$message = null;
		if ($config->min == $config->max && $value != $config->min) {
			$message = $this->getMessage($value, 'exact');
		}

		if (null !== $config->max && $value > $config->max) {
			$message = $this->getMessage($value, 'max');
		}

		if (null !== $config->min && $value < $config->min) {
			$message = $this->getMessage($value, 'min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
