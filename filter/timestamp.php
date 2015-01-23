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
     * @param   scalar  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        if(is_null($value)) return;

        return parent::validate($value);
    }


    /**
     * Converts an array of date parts to a string date.
     *
     * @param array $array The array of date parts.
     * @return string
     */
    protected function _arrayToDate($array)
    {
        $date = array_key_exists('Y', $array) &&
            trim($array['Y']) != '' &&
            array_key_exists('m', $array) &&
            trim($array['m']) != '' &&
            array_key_exists('d', $array) &&
            trim($array['d']) != '';

        if (! $date) {
            return;
        }

        $y = (int) $array['Y'];
        $m = (int) $array['m'];
        $d = (int) $array['d'];

        if($m < 10) $m = '0'.$m;
        if($d < 10) $d = '0'.$d;

        return $y . '-' . $m . '-' . $d;
    }

    /**
     * Converts an array of time parts to a string time.
     *
     * @param array $array The array of time parts.
     * @return string
     */
    protected function _arrayToTime($array)
    {
        $time = array_key_exists('H', $array) &&
            trim($array['H']) != '' &&
            array_key_exists('i', $array) &&
            trim($array['i']) != '';

        if (! $time) {
            return;
        }

        $s = array_key_exists('s', $array) && trim($array['s']) != ''
            ? $array['s']
            : '00';

        $h = (int) $array['H'];
        $m = (int) $array['i'];
        $s = (int) $s;

        if($h < 10) $h = '0'.$h;
        if($m < 10) $m = '0'.$m;
        if($s < 10) $s = '0'.$h;

        return $h . ':' . $m . ':' . $s;
    }
}