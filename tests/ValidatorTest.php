<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

use Nooku\Library;

abstract class ValidatorTestAbstract extends \PHPUnit_Framework_TestCase
{
    protected $_tests = array();
    protected $_manager;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_tests = json_decode(file_get_contents(dirname(__FILE__).'/tests.json'), true);

        $this->_manager = Library\ObjectManager::getInstance();
    }

    protected function perform($name)
    {
        $successArg = $this->_tests[$name][0];
        $failureArg = $this->_tests[$name][1];

        $filter = $this->getFilter($name);

//        $this->assertTrue($filter->validate($successArg));

        $fail = false;
        try{
            $filter->validate($failureArg);
        }catch(\Exception $e){
            $fail = true;
        }

        $this->assertTrue($fail);
    }

    protected function getFilter($name)
    {
        $identifier = 'com://oligriffiths/validation.filter.'.$name;

        return $this->getObject($identifier);
    }

    protected function getObject($identifier)
    {
        return $this->_manager->getObject($identifier);
    }
}

$tests = json_decode(file_get_contents(dirname(__FILE__).'/tests.json'), true);

$class = "class ValidatorTest extends ValidatorTestAbstract{\n";

foreach($tests AS $name => $options)
{
    $class .= "
    public function test".ucfirst($name)."(){

        \$this->perform('$name');
    }\n";
}

$class .= '}';

eval($class);

function locale_set_default(){}
