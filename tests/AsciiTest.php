<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class AsciiTest extends ValidatorTestAbstract
{
    protected $_valid = 'abc 123 !@#';
    protected $_invalid = 'àèî';
}