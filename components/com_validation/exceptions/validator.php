<?php
/**
 * User: Oli Griffiths
 * Date: 06/10/2012
 * Time: 17:21
 */

class ComValidationExceptionValidator extends KException
{
	public function __construct($message = null, $values = array(), $code = KHttpResponse::INTERNAL_SERVER_ERROR, Exception $previous = null)
	{
		foreach($values AS $key => $value)
		{
			$message = str_replace($key, $value, JText::_($message));
		}

		parent::__construct($message, $code, $previous);
	}
}