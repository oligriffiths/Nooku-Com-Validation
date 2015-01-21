<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class BooleanTest extends ValidatorTestAbstract
{
    protected $_valid = array(
        'params_nostrict' => array(true, false,'on','off', 1, 0),
        'params_strict' => array(true, false),
    );

    protected $_invalid = array(
        'params_nostrict' => array('abc'),
        'params_strict' => array('on','off', 1, 0),
    );

    protected $_params = array(
        'params_nostrict' => array(),
        'params_strict' => array('strict' => true)
    );
}