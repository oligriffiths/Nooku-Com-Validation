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
 * Class ValidatorAbstract
 *
 * Base class for validators
 *
 * @package Oligriffiths\Component\Validation
 */
abstract class ValidatorAbstract extends Library\Object implements ValidatorInterface
{
    /**
     * Validator type
     *
     * @var string
     */
    protected $_type;

    /**
     * Filter used for validation, if string or identifier, filter is instantiated
     *
     * @var mixed
     */
    protected $_filter;


    /**
     * Constructor
     *
     * @param Library\ObjectConfig  $config  A ObjectConfig object with optional configuration options
     * @return Object
     */
	public function __construct(Library\ObjectConfig $config = null)
	{
		parent::__construct($config);

        //Set the validator type
        $this->_type = $config->type;

        //Set the filter, lazy loaded if necessary
		$this->_filter = $config->filter;

        //Ensure all required options are set
        $required = $this->getRequiredOptions();
        $options = $config->toArray();
        foreach($required AS $key){
            if(null === $config->$key || ($options[$key] && empty($options[$key]))){
                throw new \InvalidArgumentException('A required option ('.$key.') for the validator "'.$this->_type.'" was not supplied');
            }
        }
	}


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
            'message' => '{{message_target}} is not a valid {{type}}, "{{value}}" given',
            'message_bad_type' => '{{message_target}} must be of type "{{value_type}}", "{{value}}" given',
            'message_target' => 'This value',
            'allow_null' => false,
            'value_type' => 'scalar',
            'filter' => $this->getIdentifier()->name
        ));

		parent::_initialize($config);
	}


    /**
     * Returns the options that are required for this validator to be valid
     * @return array
     */
    public function getRequiredOptions()
    {
        return array('message');
    }


	/**
	 * Validate a value against the constraint
	 *
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
		//Validate type
		if($this->checkType($value) === null) return null;

		//Validate
		return $this->_validate($value);
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


	/**
	 * Checks the value conforms to the validator value type
     *
	 * @param $value
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function checkType($value)
	{
		//Check if value is null
		if(!$this->getConfig()->allow_null && is_null($value)){
			return null;
		}

		//Run type check on the value
		if($this->getConfig()->value_type){
			$result = true;
			switch($this->getConfig()->value_type){
				case 'string':
					if(!(is_string($value) || is_scalar($value) || method_exists($value, '__toString'))) $result = false;
					break;

				case 'array':
					if(!(is_array($value) || $value instanceof \Iterator)) $result = false;
					break;

				default:
					$function = 'is_'.strtolower($this->getConfig()->value_type);
					if(function_exists($function) && !$function($value)) $result = false;
					break;
			}

			if(!$result){
				$message = $this->getMessage(gettype($value), 'message_bad_type');
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
	protected function _validate($value)
	{
		//Validate with filter
		$result = $this->getFilter()->validate($value);
		if(!$result){
			$message = $this->getMessage($value);
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

            $options = $this->getConfig()->toArray();

            //Remove some default options
            foreach(array('message','message_bad_type','message_target','allow_null','value_type','filter') AS $key){
                unset($options[$key]);
            }

            $this->_filter = $this->getObject($this->_filter, $options);
        }

        return $this->_filter;
    }


    /**
     * Gets the message and replaces placeholders with their values
     * @param null $value - value used to {{ value }} placeholder
     * @param string $key - message key, for use with multiple messages
     * @return mixed|null
     */
    public function getMessage($value = null, $key = 'message')
    {
        $translator = $this->getObject('translator');
        $message = $translator($this->getConfig()->$key);
        $options = $this->getConfig();

        //Get all the placeholders to replace
        preg_match_all('#\{\{\s*([^\}]+)\s*\}\}#', $message, $matches);
        foreach($matches[0] AS $k => $match){
            $k = trim($matches[1][$k]);
            if($k == 'target') $k = 'message_target';

            if (is_array($value) && isset($value[$k])) $replace = $value[$k];
            else if($k == 'value') $replace = $value;
            else if($k == 'type') $replace = $this->getConfig()->type;
            else $replace = $options->get($k);

            if($replace instanceof Library\ObjectConfigInterface) $replace = implode(',', $replace->toArray());

            $message = str_replace($match, $replace, $message);
        }

        return $message;
    }
}
