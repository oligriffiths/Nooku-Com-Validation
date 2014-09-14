<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterTimestamp extends Library\FilterTimestamp
{
    protected $_allow_zeros;

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_allow_zeros = $config->allow_zeros;
    }


    /**
	 * Validates that the value is an ISO 8601 timestamp string.
	 *
	 * The format is "yyyy-mm-ddThh:ii:ss" (note the literal "T" in the middle, which acts as a
	 * separator -- may also be a space). As an alternative, the value may be an array with all
	 * of the keys for `Y, m, d, H, i`, and optionally `s`, in which case the value is converted
	 * to an ISO 8601 string before validating it.
	 *
	 * Also checks that the date itself is valid (for example, no Feb 30).
	 *
	 * @param mixed The value to validate.
	 * @return  bool    True when the variable is valid
	 */
	public function validate($value)
	{
		// look for YmdHis keys?
		if (is_array($value)) {
			$value = $this->_arrayToTimestamp($value);
		}

		// correct length?
		if (strlen($value) != 19) {
			return false;
		}

		// valid date?
		$date = substr($value, 0, 10);
		if (! $this->getObject('com:validation.filter.date', array('allow_zeros' => $this->_allow_zeros))->validate($date)) {
			return false;
		}

		// valid separator?
		$sep = substr($value, 10, 1);
		if ($sep != 'T' && $sep != ' ') {
			return false;
		}

		// valid time?
		$time = substr($value, 11, 8);
		if (! $this->getObject('com:validation.filter.time', array('allow_zeros' => $this->_allow_zeros))->validate($time)) {
			return false;
		}

		return true;
	}
}