<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Message Mixin
 *
 */
class DecoratorValidator extends Library\ObjectDecorator
{
    /**
     * Performs validation, throws exception on error
     *
     * @param $value
     * @return mixed
     * @throws \RuntimeException
     */
    public function validate($value)
    {
        $success = $this->getDelegate()->validate($value);
        if(!$success){
            throw new \RuntimeException($this->getDelegate()->getMessage($value));
        }

        return $success;
    }

    /**
     * Set the decorated object
     *
     * @param  ObjectInterface $delegate The object to decorate
     * @return ObjectDecorator
     * @throws \InvalidArgumentException If the delegate does not extend from Object
     */
    public function setDelegate($delegate)
    {
        //Skip the parent setDelegate as it throws an error if $delegate is no an instance of Library\Object
        return Library\ObjectDecoratorAbstract::setDelegate($delegate);
    }

}