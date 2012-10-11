<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



/**
 * @Annotation
 *
 * @api
 */
class ComValidationConstraintType extends ComValidationConstraintDefault
{
    public $message = 'This value should be of type {{ type }}.';
    public $type;
    public $convert_string = false; //Allows a string to be converted is it is numeric

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'type';
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('type');
    }
}
