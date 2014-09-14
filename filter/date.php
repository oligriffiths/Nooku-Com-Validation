<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;
use Nooku\Library\scalar;

class FilterDate extends Library\FilterDate
{
    /**
     * If true, zero dates are allowed, e.g. 0000-00-00
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
     * Validates that a value is an ISO 8601 date string
     *
     * The format is "yyyy-mm-dd".  Also checks to see that the date
     * itself is valid (for example, no Feb 30).
     *
     *
     * @param   scalar  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        if($this->_allow_zeros && $value == '0000-00-00') return true;

        return parent::validate($value);
    }
}