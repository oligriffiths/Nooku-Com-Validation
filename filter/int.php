<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 11:55
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class FilterInt extends Library\FilterInt
{
    /**
     * //@TODO: Remove when fixed in framework
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'max' => PHP_INT_MAX,
            'min' => ~PHP_INT_MAX,
        ));

        parent::_initialize($config);
    }
}