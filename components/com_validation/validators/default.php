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
 * Base class for constraint validators
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorDefault extends KObject implements ComValidationValidatorInterface, KServiceInstantiatable
{
	/**
	 * Force creation of a singleton
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @param 	object	A KServiceInterface object
	 * @return KDatabaseTableDefault
	 */
	public static function getInstance(KConfigInterface $config, KServiceInterface $container)
	{
		// Check if an instance with this identifier already exists or not
		if (!$container->has($config->service_identifier))
		{
			//Create the singleton
			$classname = $config->service_identifier->classname;
			$instance  = new $classname($config);
			$container->set($config->service_identifier, $instance);
		}

		return $container->get($config->service_identifier);
	}


	/**
	 * Stub implementation delegating to the deprecated isValid method.
	 *
	 * @see ComValidationValidatorInterface::validate
	 */
	public function validate($value, ComValidationConstraintDefault $constraint){}


	/**
	 * @param $value
	 * @param null | ComValidationConstraintDefault $constraint
	 * @return bool
	 */
	public function isValid($value, $constraint = null)
	{
		if(!$constraint instanceof ComValidationConstraintDefault)
		{
			$identifier = clone $this->getIdentifier();
			$identifier->path = 'constraint';
			$constraint = $this->getService($identifier, is_array($constraint) ? $constraint : array());
		}

		try{
			$this->validate($value, $constraint);
			return true;

		}catch(ComValidationExceptionValidator $e)
		{
			return false;
		}
	}
}
