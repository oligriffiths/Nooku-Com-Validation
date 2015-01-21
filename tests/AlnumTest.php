<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class AlnumTest extends ValidatorTestAbstract
{
    protected $_valid = '123abc';
    protected $_invalid = '!123abc';
}