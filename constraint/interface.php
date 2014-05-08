<?php
/**
 * Created By: Oli Griffiths
 * Date: 12/12/2012
 * Time: 17:28
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

interface ConstraintInterface
{
	/**
	 * Returns the options that are required for this constraint to be valid
	 * @return array
	 */
	public function getRequiredOptions();


	/**
	 * Returns the options set for the constraint
	 * @return mixed
	 */
	public function getOptions();

	/**
	 * Get the object handle
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique, or NULL
	 */
	public function getHandle();


	/**
	 * Returns the validator for this constraint
	 * @param array $options - optional constructor options for the validator
	 * @return mixed|object
	 */
	public function getValidator($options = array());


	/**
	 * Gets the message and replaces placeholders with their values
	 * @param null $value - value used to {{ value }} placeholder
	 * @param string $key - message key, for use with multiple messages
	 * @return mixed|null
	 */
	public function getMessage($value = null, $key = 'message');


	/**
	 * Shorthand method to call validate on the validator
	 * @param $value
	 * @return mixed
	 */
	public function validate($value);
}