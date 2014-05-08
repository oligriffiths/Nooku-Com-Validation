<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Nooku\Component\Validation;

use Nooku\Library;
use Nooku\Library\scalar;

class FilterTime extends Library\FilterTime
{
    /**
     * If true, zero times are allowed, e.g. 00:00:00
     *
     * @var boolean
     */
    protected $_allow_zeros;

    
    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_allow_zeros = $config->allow_zeros;
    }


    /**
     * Validates that the value is an ISO 8601 time string (hh:ii::ss format).
     *
     * As an alternative, the value may be an array with all of the keys for `H`, `i`, and optionally
     * `s`, in which case the value is converted to an ISO 8601 string before validating it.
     *
     * @param   scalar  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        if($this->_allow_zeros && $value == '00:00:00') return true;

        return parent::validate($value);
    }
}