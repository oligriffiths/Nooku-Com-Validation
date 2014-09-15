<?php
/**
 * Created By: Oli Griffiths
 * Date: 29/11/2012
 * Time: 13:05
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

abstract class ConstraintAbstract extends Library\Object implements ConstraintInterface
{
    protected $_type;
	protected $_options;
	protected $_validator;
	protected $_validator_options;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

        //Set the type, for debugging
        $this->_type = $config->type;

		//Set validator if supplied
		if($config->validator){
			$this->_validator = $config->validator;
			if($this->_validator instanceof ValidatorInterface) $this->_validator->setConstraint($this);
		}

		//Store validator options
		$this->_validator_options = $config->validator_options->toArray();

		//Store options
		$this->_options = $config;
		unset($this->_options->service_identifier);
		unset($this->_options->service_container);
		unset($this->_options->validator_options);
		unset($this->_options->validator);

		//Ensure all required options are set
		$required = $this->getRequiredOptions();
		foreach($required AS $key){
			if(!isset($this->_options->$key)){
				throw new \InvalidArgumentException('A required option ('.$key.') for the constraint "'.$this->_type.'" was not supplied');
			}
		}
	}

	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
            'type' => $this->getIdentifier()->name,
			'message' => '{{ target }} is not a valid {{ type }}, "{{ value }}" given',
			'message_invalid' => '{{ target }} must be of type "{{ value_type }}", "{{ value }}" given',
			'message_target' => 'This value',
			'allow_null' => false,
			'value_type' => 'scalar',
			'validator_options' => array(),
			'validator' => null
		));

		parent::_initialize($config);
	}

	/**
	 * Allow public access to options
	 * @param $name
	 * @return null
	 */
	function __get($name)
	{
		if(isset($this->_options->$name)) return Library\ObjectConfig::unbox($this->_options->$name);
		return null;
	}


	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return array('message');
	}


	/**
	 * Returns the options set for the constraint
	 * @return mixed
	 */
	public function getOptions()
	{
		return $this->_options;
	}


	/**
	 * Returns the validator for this constraint
	 * @param array $options - optional constructor options for the validator
	 * @return mixed|object
	 */
	public function getValidator($options = array())
	{
		if(!$this->_validator instanceof ValidatorInterface){
            $options = array_merge($this->_validator_options, $options);
            $options['constraint'] = $this;
			$identifier = $this->getIdentifier()->toArray();
			$identifier['path'] = array('validator');
			$identifier['name'] = $this->_type;

            //Locate the validator, fallback to default if not specific constraint exists
            $manager = Library\ObjectManager::getInstance();
            if(!$manager->getClass($identifier)){
                $manager->registerAlias('com:validation.validator.default', $identifier);
            }

            //Set the type
            $options['type'] = $identifier['name'];

			$this->_validator = $this->getObject($identifier, $options);
		}

		return $this->_validator;
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
		$message = $translator($this->_options->$key);

		//Get all the placeholders to replace
		preg_match_all('#\{\{\s*([^\}]+)\s*\}\}#', $message, $matches);
		foreach($matches[0] AS $k => $match){
			$k = trim($matches[1][$k]);
			if($k == 'target') $k = 'message_target';

			if (is_array($value) && isset($value[$k])) $replace = $value[$k];
			else if($k == 'value') $replace = $value;
			else if($k == 'type' && !$this->_options->type) $replace = $this->_type;
			else $replace = $this->_options->{$k};

			$message = str_replace($match, $replace, $message);
		}

		return $message;
	}


	/**
	 * Validates the data against the constraint
	 *
	 * Throws KException on failure
	 *
	 * @param $value
	 * @throws KException
	 * @return mixed
	 */
	public function validate($value)
	{
		return $this->getValidator()->validate($value);
	}


	/**
	 * Validates the data against the constraint, returns true on success, false on fail
	 *
	 * @param $value
	 * @return bool
	 */
	public function isValid($value)
	{
		return $this->getValidator()->isValid($value);
	}
}