<?php

namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
interface ValidatorInterface
{
    /**
     * Validates the supplied value and throws exception on failure
     *
     * @param mixed      $value      The value that should be validated
     *
     * @throws \RuntimeException
     */
    public function validate($value);

	/**
	 *
     * Validates the supplied value
     *
	 * @param mixed      $value      The value that should be validated
	 * @return BOOL
	 */
	public function isValid($value);

    /**
     * Gets the message and replaces placeholders with their values
     *
     * @param null $value - value used to {{ value }} placeholder
     * @param string $key - message key, for use with multiple messages
     * @return mixed|null
     */
    public function getMessage($value = null, $key = 'message');
}
