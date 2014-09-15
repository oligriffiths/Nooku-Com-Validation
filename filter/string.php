<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;
use Nooku\Library\scalar;

class FilterString extends Library\FilterString
{
    /**
     * Validate a value
     *
     * @param   scalar  $value Value to be validated
     * @return	bool	True when the variable is valid
     */
    public function validate($value)
    {
        return is_string($value);
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  $value Value to be sanitized
     * @return	string
     */
    public function sanitize($value)
    {
        return (string) $value;
    }
}