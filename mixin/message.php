<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;
use Nooku\Library\ObjectConfig;

/**
 * Message Mixin
 *
 */
class MixinMessage extends Library\ObjectMixinAbstract
{
    /**
     * @var \Nooku\Library\ObjectConfig
     */
    protected $_config;

    /**
     * Object constructor
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_config = $config;

        $this->getObject('translator')->load('com://oligriffiths/validation');
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
            'message' => '{{target}} is not a valid {{type}}, "{{value}}" given',
            'message_bad_type' => '{{target}} must be of type "{{value_type}}", "{{value}}" given',
            'message_target' => 'This value',
        ));

        parent::_initialize($config);
    }

    /**
     * Gets the mixin config
     *
     * @return Library\ObjectConfig
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Gets the message and replaces placeholders with their values
     * @param null $values - values used to replace placeholders
     * @param string $message_key - message key, for use with multiple messages
     * @return string
     */
    public function getMessage($values = array(), $message_key = null)
    {
        //Compile replacement options
        $options = clone $this->getMixer()->getConfig();
        $options
            ->append($this->getConfig())
            ->append(is_array($values) ? $values : array('value' => $values))
            ->append(array('type' => $this->getMixer()->getIdentifier()->name));

        //Translate the message
        $translator = $this->getObject('translator');
        $message = null;

        //If message key supplied, check config first, then loaded language file
        if($message_key){
            $message_key = 'FILTER_ERROR_'.strtoupper($message_key);
            $message = $translator->translate($options->$message_key ?: $message_key);
            if($message == $message_key) $message = null;
        }

        //If no message, check for filter specific, then default message
        if(!$message){
            $message_key = 'FILTER_ERROR_'.strtoupper($options->type);
            $message = $translator->translate($message_key);
            $message = $message == $message_key ? $translator->translate('FILTER_ERROR_DEFAULT') : $message;
        }

        return $this->_replaceParameters($message, $options);
    }


    /**
     * Handles parameter replacements, replaces %key% with appropriate value from $parameters
     *
     * @param string $string String
     * @param array  $parameters A config object of parameters
     * @return string String after replacing the parameters
     */
    protected function _replaceParameters($string, Library\ObjectConfigInterface $parameters)
    {
        //Get all the placeholders to replace
        preg_match_all('#\%\s*([^\%]+)\s*\%#', $string, $matches);
        foreach($matches[0] AS $k => $match){

            $key = $matches[1][$k];

            //Find the replacement value
            $replace = $parameters->get($key);

            //Convert ObjectConfigs to array
            if($replace instanceof Library\ObjectConfigInterface) $replace = $replace->toArray();

            //Convert arrays to string
            if(is_array($replace)) $replace = implode(',', $replace);

            //Perform token replacement
            $string = str_replace($match, $replace, $string);
        }

        return $string;
    }

    /**
     * Performs validation on the filters, throws exception on error
     *
     * @param $value
     * @return mixed
     * @throws \RuntimeException
     */
    public function execute($value)
    {
        $success = $this->getMixer()->validate($value);
        if(!$success){
            throw new \RuntimeException($this->getMessage($value));
        }

        return $success;
    }
}