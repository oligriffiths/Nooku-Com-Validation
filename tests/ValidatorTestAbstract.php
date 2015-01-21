<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

use Nooku\Library;

abstract class ValidatorTestAbstract extends \PHPUnit_Framework_TestCase
{
    private $__manager;

    protected $_valid;
    protected $_invalid;
    protected $_params = array();

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->__manager = Library\ObjectManager::getInstance();
    }

    /**
     * Performs a test on the filter that should return a valid response
     */
    public function testValid()
    {
        $this->perform(true);
    }

    /**
     * Performs a test on the filter that should return an invalid response
     */
    public function testInvalid()
    {
        $this->perform(false);
    }

    /**
     * Performs a valid/invalid test
     *
     * @param $valid
     */
    protected function perform($valid)
    {
        $values = (array) ($valid ? $this->_valid : $this->_invalid);

        //If the array key is not numeric, we're doing a multi param test
        $multiParams = !is_numeric(key($values));
        if($multiParams){
            $valueset = $values;

            foreach($valueset AS $key => $values){

                $params = isset($this->_params[$key]) ? $this->_params[$key] : array();

                $filter = $this->getFilter($params);
                $this->execute($values, $filter, $valid);
            }
        }else{
            $filter = $this->getFilter();
            $this->execute($values, $filter, $valid);
        }
    }

    /**
     * Execute test with the supplied values on the filter
     *
     * @param $values
     * @param $filter
     * @param $valid
     */
    protected function execute($values, $filter, $valid)
    {
        foreach($values AS $value)
        {
            $message = null;
            try{
                $succeed = $filter->validate($value);
            }catch(\Exception $e){
                $succeed = false;
                $message = $e->getMessage();
            }

            if($valid) $this->assertTrue($succeed, $message);
            else $this->assertFalse($succeed, $message);
        }
    }

    /**
     * Extracts the filter name from the class name
     *
     * @return string
     */
    protected function getFilterName()
    {
        $class = get_class($this);
        $class = preg_replace('#Test$#','', $class);
        $class = preg_replace('#^Validator#','', $class);

        return strtolower($class);
    }

    /**
     * Gets the corresponding filter
     *
     * @return Library\ObjectInterface
     */
    protected function getFilter($params = null, $name = null)
    {
        $name = $name ?: $this->getFilterName();
        $identifier = 'com://oligriffiths/validation.filter.'.$name;

        return $this->getObject($identifier, (isset($params) ? $params : $this->_params));
    }

    /**
     * Convenience method to get an object from the ObjectManager
     *
     * @param $identifier
     * @return Library\ObjectInterface
     */
    protected function getObject($identifier, $params = array())
    {
        return $this->__manager->getObject($identifier, $params);
    }
}