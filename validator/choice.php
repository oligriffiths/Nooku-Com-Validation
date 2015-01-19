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
 * Class ValidatorChoice
 *
 * Choice validator. Validates that the value is in an array of choices
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorChoice extends ValidatorAbstract
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
            'message' => '{{message_target}} is not a valid {{type}}, must be one of {{choices}}, "{{value}}" given',
			'filter' => false,
            'choices' => array()
		));

		parent::_initialize($config);
	}


    /**
     * Returns the options that are required for this constraint to be valid
     * @return array
     */
    public function getRequiredOptions()
    {
        return array('message','choices');
    }


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value)
	{
		if (!in_array($value, $this->getConfig()->choices->toArray())) {
			throw new \RuntimeException($this->getMessage($value));
		}

		return true;
	}
}
