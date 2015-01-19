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
class ValidatorRange extends ValidatorDefault
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
            'message_min' => '{{message_target}} should be {{min}} or more, {{value}} given',
            'message_max' => '{{message_target}} should be {{max}} or less, {{value}} given',
            'value_type' => 'numeric'
		));

		parent::_initialize($config);
	}


    /**
     * Returns the options that are required for this validator to be valid
     * @return array
     */
    public function getRequiredOptions()
    {
        return array('message','min','max');
    }


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
        $config = $this->getConfig();

		$message = null;
		if ($constraint->min == $config->max && $value != $config->min) {
			$message = $this->getMessage($value, 'message_exact');
		}

		if (null !== $config->max && $value > $config->max) {
			$message = $this->getMessage($value, 'message_max');
		}

		if (null !== $config->min && $value < $config->min) {
			$message = $this->getMessage($value, 'message_min');
		}

		if($message !== null){
			throw new \RuntimeException($message);
		}

		return true;
	}
}
