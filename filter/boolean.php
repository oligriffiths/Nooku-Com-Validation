<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterBoolean extends Library\FilterBoolean
{
	protected $_strict;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

		$this->_strict = $config->strict;
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'strict' => false,
        ));

        parent::_initialize($config);
    }


	/**
	 * Validate the value is boolean.
	 *
	 * In strict mode, the value must be either true or false
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		if(!parent::validate($value)) return false;

		if($this->_strict) return is_bool($value);

		return true;
	}
}