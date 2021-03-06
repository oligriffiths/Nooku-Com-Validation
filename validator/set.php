<?php
/**
 * User: Oli Griffiths
 * Date: 07/10/2012
 * Time: 14:38
 */

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ValidatorSet extends Library\ObjectArray
{
	protected $_errors = array();

	/**
	 * Constructor
	 *
	 * @param Library\ObjectConfig|null $config  An optional Library\ObjectConfig object with configuration options
	 * @return $this
	 */
	public function __construct(Library\ObjectConfig $config = null)
	{
		parent::__construct($config);

        $constraints = Library\ObjectConfig::unbox($config->constraints);
		if(is_array($constraints)) $this->addConstraints($constraints);
	}


	/***
	 * Adds constraint sets by key to the set
	 * @param array $constraints
	 */
	public function addConstraints(array $constraints)
	{
		foreach($constraints AS $column => $constraintset)
		{
			if(is_array($constraintset)){
				$constraintset = $this->getObject('com:validation.constraint.set', array('constraints' => $constraints));
			}

			if($constraintset instanceof ConstraintSet)
			{
				$this->offsetSet($column, $constraintset);
			}
		}

        return $this;
	}


	/**
	 * Validates a keyed array against the stored constraint sets
	 *
	 * @param $data
	 * @return bool True on valid, false on failure
	 */
	public function validate($data)
	{
        $this->_errors = array();
		foreach($data AS $key => $value)
		{
			if($constraints = $this->offsetGet($key))
			{
                if(true !== ($errors = $constraints->validate($value))){
                    $this->_errors[$key] = $errors;
                }
			}
		}

		return count($this->_errors) ? false : true;
	}


	/**
	 * Returns any errors generated by the validate method above
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
}