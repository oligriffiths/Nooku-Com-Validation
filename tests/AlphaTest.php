<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class AlphaTest extends ValidatorTestAbstract
{
    protected $_valid = 'abc';
    protected $_invalid = 'abc123';
}