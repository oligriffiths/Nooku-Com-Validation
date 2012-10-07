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
 */
class ComValidationConstraintRequired extends ComValidationConstraintDefault
{
    public $constraints = array();

    public function getDefaultOption()
    {
        return 'constraints';
    }
}
