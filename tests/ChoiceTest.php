<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class ChoiceTest extends ValidatorTestAbstract
{
    protected $_valid = 'one';
    protected $_invalid = 'four';

    protected $_params = array(
        'choices' => array('one','two','three')
    );
}