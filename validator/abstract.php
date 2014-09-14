<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
abstract class ValidatorAbstract extends Library\Object implements ValidatorInterface
{
    protected $_type;
	protected $_constraint;
	protected $_filter;

	public function __construct(Library\ObjectConfig $config = null)
	{
		parent::__construct($config);

        $this->_type = $config->type;

		$this->_constraint = $config->constraint;

		$this->_filter = $config->filter;
	}

	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
            'type' => $this->getIdentifier()->name,
			'constraint' => null
		))->append(array(
            'filter' => $config->type
        ));

		parent::_initialize($config);
	}


	/**
	 * Sets the constraint in the validator
	 * @param ConstraintInterface $constraint
	 * @return mixed
	 */
	public function setConstraint(ConstraintInterface $constraint)
	{
		$this->_constraint = $constraint;
		return $this;
	}


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value, $constraint = null)
	{
		$constraint = $constraint ?: $this->_constraint;

		//Validate type
		if($this->checkType($value, $constraint) === null) return true;

		//Validate
		return $this->_validate($value, $constraint);
	}


	/**
	 * Checks the value conforms to the constraint value type
	 * @param $value
	 * @param ConstraintDefault $constraint
	 * @return bool
	 * @throws KException
	 */
	protected function checkType($value, ConstraintDefault $constraint)
	{
		//Check if value is null
		if(!$constraint->allow_null && is_null($value)){
			return null;
		}

		//Run type check on the value
		if($constraint->value_type){
			$result = true;
			switch($constraint->value_type){
				case 'string':
					if(!(is_string($value) || is_scalar($value) || method_exists($value, '__toString'))) $result = false;
					break;

				case 'array':
					if(!(is_array($value) || $value instanceof \Iterator || $value instanceof \ArrayAccess)) $result = false;
					break;

				default:
					$function = 'is_'.strtolower($constraint->value_type);
					if(function_exists($function) && !$function($value)) $result = false;
					break;
			}

			if(!$result){
				$message = $constraint->getMessage(gettype($value), 'message_invalid');
				throw new \RuntimeException($message);
			}
		}

		return true;
	}


	/**
	 * Validates the value using the attached filter
	 * @param $value
	 * @param ConstraintDefault $constraint
	 * @return bool
	 * @throws KException
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
		//Validate with filter
		$result = $this->getFilter()->validate($value, $constraint);
		if(!$result){
			$message = $constraint->getMessage($value);
			throw new \RuntimeException($message);
		}

		return true;
	}


    /**
     * Gets the attached filter
     * @return Library\FilterInterface|Library\ObjectInterface
     */
    protected function getFilter()
    {
        if(!$this->_filter instanceof Library\FilterInterface){

            if(!$this->_filter instanceof Library\ObjectIdentifier && strpos($this->_filter,'.') === false){
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'] = array('filter');
                $identifier['name'] = $this->_filter;
                $this->_filter = $identifier;
            }

            $options = $this->_constraint ? $this->_constraint->getOptions()->toArray() : array();
            $this->_filter = $this->getObject($this->_filter, $options);
        }

        return $this->_filter;
    }


	/**
	 * @param $value
	 * @param null | ConstraintDefault $constraint
	 * @return bool
	 */
	public function isValid($value, $constraint = null)
	{
		try{
			$this->validate($value, $constraint);
			return true;

		}catch(\Exception $e)
		{
			return false;
		}
	}
}
