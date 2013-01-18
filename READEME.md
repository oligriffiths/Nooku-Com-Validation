--------------------------------------------------------
Validation component for Nooku Framework 12.2+
--------------------------------------------------------

The component consists of 4 major parts

* Validatable controller behavior
* Validatable database behavior
* Constraints
* Validators & Filters

This component is designed to be plug and play very easily.
Database schemas are also converted into constraints and applied automatically.

--------------------------------------------------------

Controller behavior:

The controller behavior is responsible for handling validation before add/edit/save/apply.
Errors are caught and raised as notices as opposed to exceptions thrown by the database behavior.


Database behavior:

The database behavior is responsible for reading the database schema and converting this into associated constraints.
It also handles running validation before insert/update, storing the current data should validation fail,
and re-populating the object after select, so no need to handle storing the data yourself.


Constraints:

The concept of constraints and validators is borrowed from symphony.
A constraint holds the meta data that is used to perform a validation.
Constraints has types, different constraints contain different validation parameters, for example:
    min - holds a minimum value and a specific error message should validation fail
    image - holds mime types, min/max width/height and related error messages

A constraint must implement ComValidationConstraintInterface


Validators:

A validator is where the leg work happens for validation.
Validators take a value and a constraint, and validate the value against the constraints parameters.
If validation fails, validators must throw an exception with the specific error message or true on success.
If a validator does not have a specific implementation (class oin the validators folder) then the default validator
class will be loaded. This in turn loads a corresponding KFilter from the Koowa package.
Some validators have custom implementations that can not be achieved with a filter, usually validators that require
the input to be an array (KFilter automatically iterates arrays and validates the values)

--------------------------------------------------------

Happy coding!