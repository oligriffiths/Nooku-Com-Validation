<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

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

    protected $_translator;

    /**
     * Object constructor
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_translator = $config->translator;

        $this->loadTranslations();
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
            'translator' => null,
        ));
    }

    /**
     * Loads the translation files
     */
    protected function loadTranslations()
    {
        static $loaded;

        if(!$loaded){
            if(!$this->getTranslator()->isLoaded('com://oligriffiths/validation')){
                $loaded = $this->getTranslator()->load('com://oligriffiths/validation');
            }
        }
    }

    /**
     * Lazy loads the translator
     * @return Library\TranslatorInterface
     */
    protected function getTranslator()
    {
        if(!$this->_translator instanceof Library\TranslatorInterface){
            $this->_translator = $this->getObject('translator');
        }

        return $this->_translator;
    }

    /**
     * Gets the message and replaces placeholders with their values
     *
     * @param array $values - values used to replace placeholders in the message string
     * @param string $message_key - message key, allows selection of a different message
     * @return string
     */
    public function getMessage($values = array(), $message_key = null)
    {
        //Compile replacement options
        $options = clone $this->getMixer()->getConfig();
        $options
            ->append(is_array($values) ? $values : array('value' => $values))
            ->append(array(
                'type' => $this->getMixer()->getIdentifier()->name,
                'message_target' => $this->getTranslation('MESSAGE_TARGET')
            ));

        //Translate the message
        $message = null;

        //If message key supplied check for specific message
        if($message_key) $message = $this->getTranslation($message_key);

        //If no message, check for filter specific, then default message
        $message = $message ?: ($this->getTranslation($options->type) ?: $this->getTranslation('DEFAULT'));

        //Perform string replacement
        return $this->_replaceParameters($message, $options);
    }

    /**
     * Gets a translation from the translator, or null if none found
     *
     * @param $key - The translation key. A prefix of FILTER_ERROR_ is added and uppercased.
     * @return null|string
     */
    protected function getTranslation($key)
    {
        $key = 'FILTER_ERROR_'.strtoupper($key);

        //Ensure key exists, return null if not set
        if(!$this->getTranslator()->getCatalogue()->has($key)) return null;

        //Translate the key
        return $this->getTranslator()->translate($key);
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
        foreach($matches[1] AS $k => $match){

            //Find the replacement value
            $replace = $parameters->get($match);

            //Convert ObjectConfigs to array
            if($replace instanceof Library\ObjectConfigInterface) $replace = $replace->toArray();

            //Convert arrays and objects to string
            if(is_array($replace) && is_numeric(key(is_array($replace)))) $replace = implode(',',$replace);
            else if(is_array($replace) || is_object($replace)) $replace = json_encode($replace);

            //Perform token replacement
            $string = str_replace($matches[0][$k], $replace, $string);
        }

        return $string;
    }
}