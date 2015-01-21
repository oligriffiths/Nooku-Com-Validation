<?php
/**
 * User: Oli Griffiths <http://github.com/oligriffiths>
 * Date: 15/01/15
 * Time: 18:52
 */

require_once 'ValidatorTestAbstract.php';

class Base64Test extends ValidatorTestAbstract
{
    protected $_valid = 'bXkgcGhyYXNl';
    protected $_invalid = 'bXkgcGhyYXNlabc';
}