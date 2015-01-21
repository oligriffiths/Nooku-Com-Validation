<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterDate extends Library\FilterDate
{
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
}