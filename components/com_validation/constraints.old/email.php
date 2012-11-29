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
class ComValidationConstraintEmail extends ComValidationConstraintDefault
{
    public $message = 'This value is not a valid email address.';
    public $checkMX = false;
    public $checkHost = false;
}
