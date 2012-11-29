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
 * Contains the properties of a constraint definition.
 *
 * A constraint can be defined on a class, an option or a getter method.
 * The Constraint class encapsulates all the configuration required for
 * validating this class, option or getter result successfully.
 *
 * Constraint instances are immutable and serializable.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationConstraintDefault extends KObject implements KObjectHandlable
{
	/**
	 * The name of the group given to all constraints with no explicit group
	 * @var string
	 */
	const DEFAULT_GROUP = 'Default';

	/**
	 * Marks a constraint that can be put onto classes
	 * @var string
	 */
	const CLASS_CONSTRAINT = 'class';

	/**
	 * Marks a constraint that can be put onto properties
	 * @var string
	 */
	const PROPERTY_CONSTRAINT = 'property';

	/**
	 * @var array
	 */
	public $groups = array(self::DEFAULT_GROUP);

	/**
	 * Initializes the constraint with options.
	 *
	 * You should pass an associative array. The keys should be the names of
	 * existing properties in this class. The values should be the value for these
	 * properties.
	 *
	 * Alternatively you can override the method getDefaultOption() to return the
	 * name of an existing property. If no associative array is passed, this
	 * property is set instead.
	 *
	 * You can force that certain options are set by overriding
	 * getRequiredOptions() to return the names of these options. If any
	 * option is not set here, an exception is thrown.
	 *
	 * @param mixed $options The options (as associative array)
	 *                       or the value for the default
	 *                       option (any other type)
	 *
	 * @throws InvalidOptionsException       When you pass the names of non-existing
	 *                                       options
	 * @throws MissingOptionsException       When you don't pass any of the options
	 *                                       returned by getRequiredOptions()
	 * @throws ConstraintDefinitionException When you don't pass an associative
	 *                                       array, but getDefaultOption() returns
	 *                                       NULL
	 *
	 * @api
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		$options = $config->toArray();

		$missingOptions = array_flip((array) $this->getRequiredOptions());

		if (is_array($options) && count($options) == 1 && isset($options['value'])) {
			$options = $options['value'];
		}

		if (is_array($options) && count($options) > 0 && is_string(key($options))) {
			foreach ($options as $option => $value) {
				if (property_exists($this, $option)) {
					$this->$option = $value;
					unset($missingOptions[$option]);
				} else {
					$invalidOptions[] = $option;
				}
			}
		} elseif (null !== $options && ! (is_array($options) && count($options) === 0)) {
			$option = $this->getDefaultOption();

			if (null === $option) {
				throw new ConstraintDefinitionException(
					sprintf('No default option is configured for constraint %s', get_class($this))
				);
			}

			if (property_exists($this, $option)) {
				$this->$option = $options;
				unset($missingOptions[$option]);
			} else {
				$invalidOptions[] = $option;
			}
		}

		if (count($missingOptions) > 0) {
			throw new MissingOptionsException(
				sprintf('The options "%s" must be set for constraint %s', implode('", "', array_keys($missingOptions)), get_class($this)),
				array_keys($missingOptions)
			);
		}

		$this->groups = (array) $this->groups;
	}

	/**
	 * Get the object handle
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique, or NULL
	 */
	public function getHandle()
	{
		return $this->getIdentifier()->name;
	}

	/**
	 * Unsupported operation.
	 */
	public function __set($option, $value)
	{
		throw new InvalidOptionsException(sprintf('The option "%s" does not exist in constraint %s', $option, get_class($this)), array($option));
	}

	/**
	 * Adds the given group if this constraint is in the Default group
	 *
	 * @param string $group
	 *
	 * @api
	 */
	public function addImplicitGroupName($group)
	{
		if (in_array(Constraint::DEFAULT_GROUP, $this->groups) && !in_array($group, $this->groups)) {
			$this->groups[] = $group;
		}
	}

	/**
	 * Returns the name of the default option
	 *
	 * Override this method to define a default option.
	 *
	 * @return string
	 * @see __construct()
	 *
	 * @api
	 */
	public function getDefaultOption()
	{
		return null;
	}

	/**
	 * Returns the name of the required options
	 *
	 * Override this method if you want to define required options.
	 *
	 * @return array
	 * @see __construct()
	 *
	 * @api
	 */
	public function getRequiredOptions()
	{
		return array();
	}

	/**
	 * Returns the name of the class that validates this constraint
	 *
	 * By default, this is the fully qualified name of the constraint class
	 * suffixed with "Validator". You can override this method to change that
	 * behaviour.
	 *
	 * @return string
	 *
	 * @api
	 */
	public function validatedBy()
	{
		$identifier = clone $this->getIdentifier();
		$identifier->path = array('validator');
		return $identifier->classname;
	}

	/**
	 * Returns whether the constraint can be put onto classes, properties or
	 * both
	 *
	 * This method should return one or more of the constants
	 * Constraint::CLASS_CONSTRAINT and Constraint::PROPERTY_CONSTRAINT.
	 *
	 * @return string|array  One or more constant values
	 *
	 * @api
	 */
	public function getTargets()
	{
		return self::PROPERTY_CONSTRAINT;
	}


	public function getValidator()
	{
		$identifier = clone $this->getIdentifier();
		$identifier->path = 'validator';
		return $this->getService($identifier);
	}
}
