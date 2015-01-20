<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterRequired extends FilterBlank
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
            'strict' => false
        ));

        parent::_initialize($config);
    }

	/**
	 * Validate the value is set
	 * A value is not blank if it is not '', null or 0
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be validated
	 * @return    bool    True when the variable is valid
	 */
	public function validate($value)
	{
		if($this->getConfig()->strict){
            return $value !== null && $value !== '' && $value !== 0;
        }else{
            return $value != null;
        }
	}

	/**
	 * Sanitize the data, returns $value
	 *
	 * Variable passed to this function will always be a scalar
	 *
	 * @param    scalar    Value to be sanitized
	 * @return    mixed
	 */
	public function sanitize($value)
	{
		return $this->validate($value) ? $value : null;
	}
}