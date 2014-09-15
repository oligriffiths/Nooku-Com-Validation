--------------------------------------------------------
# Validation component for Nooku Framework 0.9
--------------------------------------------------------

The component consists of 4 major parts

* Validatable controller behavior
* Validatable database behavior
* Constraints
* Validators & Filters

This component is designed to be plug and play very easily.
Database schemas are also converted into constraints and applied automatically.

--------------------------------------------------------

## Controller behavior:

The controller behavior is responsible for handling validation before add/edit/save/apply.
Errors are caught and raised as notices as opposed to exceptions thrown by the database behavior.

To implement this, add 'com://oligriffiths/validation.controller.behavior.validatable' to the controller that will save the entity


## Database behavior:

The database behavior is responsible for reading the database schema and converting this into associated constraints.
It also handles running validation before insert/update, storing the current data should validation fail,
and re-populating the object after select, so no need to handle storing the data yourself.

To implement this, add 'com://oligriffiths/validation.database.behavior.validatable' to the database table for the entity


## Constraints:

The concept of constraints and validators is borrowed from symphony.
A constraint holds the meta data that is used to perform a validation.
Constraints has types, different constraints contain different validation parameters, for example:

* min - holds a minimum value and a specific error message should validation fail
* image - holds mime types, min/max width/height and related error messages

A constraint must implement `Nooku\Component\Validation\ConstraintInterface`


## Validators:

A validator is where the leg work happens for validation.
Validators take a value and a constraint, and validate the value against the constraints parameters.
If validation fails, validators must throw an exception with the specific error message or true on success.
If a validator does not have a specific implementation (class in the validators folder) then the default validator
class will be loaded. This in turn loads a corresponding Filter from the Nooku library.
Some validators have custom implementations that can not be achieved with a filter, usually validators that require
the input to be an array (Nooku filters automatically iterate arrays and validate the values)

There are 2 additional types that are of importance

## Constraint sets:

Constraint sets hold a set of constraints, and all those constraints can be validated by the `validate()` method

## Validator sets:

Validator sets hold constraint sets by key and are used to validate an array of key/value pairs


--------------------------------------------------------

## Validators:

Here is a list of the available validators (explainations provided where necessary):

	alnum       <- alpha numeric
	alpha
	ascii
	base64
	blank
	boolean
	choice      <- array of pre-defined choices, e.g. ENUM columns
	cmd         <- A 'command' is a string containing only the characters [A-Za-z0-9.-_]
	count       <- counts the values in an array
	date
	digit
	email
	false
	file
	float
	identifier  <- Ensure the value conforms to a URI identifier
	image
	int
	internalurl
	ip
	json
	lang        <- Language code in the format en-US
	length
	max
	md5
	min
	notblank    <- Ensure a value is not blank, eg 0, '', false but not null
	notnull
	null
	numeric     <- Ensures a value is numeric. See KFilterNumeric for definition
	path        <- Ensure the value is a string formatted as a path
	range
	regex       <- Validates against the supplied regex
	required    <- Alias to notblank, different message
	string
	time
	timestamp
	true
	type        <- Validates the value against phps built in or any custom is_(type) method. Eg is_string, is_int. Can also check for value instanceof "class"
	url
	word        <- A 'word' is a string containing only the characters [A-Za-z_]

The specific parameters (e.g. length requires a max parameter) required for each constraint can be viewed by looking at the _initialize method of the class or default class in the constraints folder. If no class exists, it requires no parameters.

Note: With the exception of null and notnull, null values will not be validated unless 'allow_null' => true is passed through to the constraint config.

## Usage

### Behaviors

#### Database behavior
This will automatically create constraints for different column names and types, and perform validation before insert and update.

To attach the behavior to one of your tables, add the following to the table _initialize method:

```php
protected function _initialize(\Nooku\Library\Config $config)
{
    $config->append(array(
        'behaviors' => array(
            'com:validation.database.behavior.validatable'
        )
    ))
}
```

If validation fails, an exception will be thrown.

You can attach additional custom constraints for each column in the table by passing through a constraints property to the behavior. Each key of the constraints array corresponds to a column in the table. The value of this property should be an array of constraints being attached.

```php
protected function _initialize(\Nooku\Library\Config $config)
{
    $config->append(array(
        'behaviors' => array(
            'com:validation.database.behavior.validatable' => array(
                'constraints' => array(
                    'password' => array('md5')
                )
            )
        )
    ))
}
```

The example above attached the MD5 constraint to the password column.

Note: passing through a constraint for a column will cause the constraints loaded from the database to be merged with the custom constraints. To surpress this functionality pass 'replace' => true through.

#### Controller Behavior

If you are attaching the database behavior, it is advised to also attached the controller behavior so that errors thrown by the database behavior are caught, and the appropriate redirect is set.

To do so add the behavior in the _initialize method of the controller:

```php
protected function _initialize(\Nooku\Library\Config $config)
{
    $config->append(array(
        'behaviors' => array(
            'com:validation.controller.behavior.validatable'
        )
    ))
}
```
	
#### Custom uses

Constraints and validators can also be used in a stand alone mode for any time you wish to validate some data. All you need to do it get an instance of the constraint, and call validate on the constraint, passing your data value in.

E.g.:

```php
$this->getObject('com:validation.constraint.email')->validate('test@test.com');
```
	
If the email address is valid, true will be returned, else an exception will be thrown.

For simple validation that just required true/false without the error itself;

```php
$valid = $this->getObject('com:validation.constraint.email')->isValid('test@test.com');
```
	
The exception will be caught and true/false returned. To access the specific error that was thrown, call `getError()` on the constraint;

##### Constraint sets

Constraints can also be added to a constraint set; a group on constraints upon which a value can be validated against. All constraints in the set must validate for the set to be valid.

```php
$set = $this->getObject('com:validation.constraint.set', array(
    'constraints' => array(
        'com:validation.constraint.email',
        'com:validation.constraint.length' => array('min' => 0, 'max' => 150)
    )
));

$valid = $set->validate('test@test.com');
$errors = $set->getErrors();
```
	
You can also add constraints to an existing set by calling `$set->addConstraint()`

`$set->validate()` will return true/false for success/failure. The specific errors raised can be retrieved using `$set->getErrors()`;

##### Validator sets

Validator sets hold multiple constraint sets by key name. An array of data can then be validated against the constraint sets, each key of the data array corresponds to a key holding a constraint set.

For the validator set to be valid, all constraint sets must validate/return true;

```php
$set = $this->getObject('com:validation.validator.set',
    array('constraints' =>
        'email' => array('com:validation.constraint.email'),
        'name' => array('com:validation.constraint.length' => array('min' => 0, 'max' => 150))
    )
));

$valid = $set->validate(array('email' => 'test@test.com', 'name' => 'Jon Doe'));
$errors = $set->getErrors();
```
	
The above will validate an array/iterable object with the keys email and name, against the constraints passed in to the validator set.

`$set->validate()` will return true/false on success/failure. The specific errors raised can be retrieved using `$set->getErrors()`;


