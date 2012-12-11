<?php
/**
 * Created By: Oli Griffiths
 * Date: 29/11/2012
 * Time: 13:05
 */
defined('KOOWA') or die('Protected resource');

class ComValidationConstraintDefault extends KObject
{
	protected $_options;
	protected $_validator;
	protected $_validator_options;

	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		//Set validator if supplied
		if($config->validator){
			$this->_validator = $config->validator;
			$this->_validator->setConstraint($this);
		}
		else{
			$this->_validator_options = $config->validator_options->toArray();
		}

		//Store options
		$this->_options = $config->options;

		//Ensure all required options are set
		$required = $this->getRequiredOptions();
		foreach($required AS $key){
			if(!isset($this->_options->$key)){
				throw new KException('A required option ('.$key.') for the constraint '.$this->getIdentifier()->name.'  was not supplied');
			}
		}
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'options' => array(
				'message' => '{{ target }} {{ value }} is not a valid '.$this->getIdentifier()->name,
				'message_target' => 'The value'
			),
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
		if(isset($this->_options->$name)) return $this->_options->$name;
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
	 * Get the object handle
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique, or NULL
	 */
	public function getHandle()
	{
		return $this->getIdentifier()->name;
	}


	/**
	 * Returns the validator for this constraint
	 * @param array $options - optional constructor options for the validator
	 * @return mixed|object
	 */
	public function getValidator($options = array())
	{
		if(!$this->_validator){
			$options['constraint'] = $this;
			$options = array_merge($this->_validator_options, $options);
			$identifier = clone $this->getIdentifier();
			$identifier->path = 'validator';
			$this->_validator = $this->getService($identifier, $options);
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
		$message = $this->$key;

		//Get all the placeholders to replace
		preg_match_all('#\{\{\s*([^\}]+)\s*\}\}#', $message, $matches);
		foreach($matches AS $k => $match){
			$k = trim($matches[1][$k]);
			if($k == 'target') $k = $key.'_'.$k;

			if($k == 'value') $replace = $value;
			else $replace = $this->_options->{$k};

			$message = str_replace($match, $replace, $message);
		}


		return $message;
	}


	public function validate($value)
	{
		return $this->getValidator()->validate($value);
	}
}