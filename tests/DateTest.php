<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class DateTest extends ValidatorTestAbstract
{
    protected $_valid = array(
        array('Y'=>2000,'m'=>1,'d'=>1), '2000-01-01'
    );

    protected $_invalid = array(
        array('Y'=>2000,'m'=>13,'d'=>1), '2000-13-01'
    );
}