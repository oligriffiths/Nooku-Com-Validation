<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
defined('KOOWA') or die('Protected resource');

class ComValidationFilterTimestamp extends KFilterTimestamp
{
	protected $_allow_blank;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_allow_blank = $config->allow_blank;
	}


	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'allow_blank' => false
		));
		parent::_initialize($config);
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
	protected function _validate($value)
	{
		// look for YmdHis keys?
		if (is_array($value)) {
			$value = $this->_arrayToTimestamp($value);
		}

		// correct length?
		if (strlen($value) != 19) {
			return false;
		}

		//Allow blank timestamps?
		if($this->_allow_blank && $value == '0000-00-00 00:00:00'){
			return true;
		}

		// valid date?
		$date = substr($value, 0, 10);
        //Removing validation on null dates as this breaks on createable and modifiable behaviours
//		if (! $this->getService('koowa:filter.date')->validate($date)) {
//			return false;
//		}

		// valid separator?
		$sep = substr($value, 10, 1);
		if ($sep != 'T' && $sep != ' ') {
			return false;
		}

		// valid time?
		$time = substr($value, 11, 8);
		if (! $this->getService('koowa:filter.time')->validate($time)) {
			return false;
		}

		return true;
	}
}