<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterTime extends Library\FilterTime
{
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