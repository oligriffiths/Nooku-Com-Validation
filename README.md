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

There are 2 additional types that are of importance

Constraint sets:

Constraint sets hold a set of constraints, and all those constraints can be validated by the validate() method

Validator sets:

Validator sets hold constraint sets by key and are used to validate an array of key/value pairs


--------------------------------------------------------

## Validators:

Here is a list of the available validators (explainations provided where necessary):

alnum       - alpha numeric
alpha
ascii
base64
blank
boolean
choice      - array of pre-defined choices
cmd         - A 'command' is a string containing only the characters [A-Za-z0-9.-_]
count       - counts the values in an array
date
digit
email
false
file
float
identifier  - Ensure the value conforms to a URI identifier
image
int
internalurl
ip
json
lang        - Language code in the format en-US
length
max
md5
min
notblank    - Ensure a value is not blank, eg 0, '', false but not null
notnull
null
numeric     - Ensures a value is numeric. See KFilterNumeric for definition
path        - Ensure the value is a string formatted as a path
range
regex       - Validates against the supplied regex
required    - Alias to notblank, different message
string
time
timestamp
true
type        - Validates the value against php's built in or any custom is_(type) method. Eg is_string, is_int. Can also check for value instanceof "class"
url
word        - A 'word' is a string containing only the characters [A-Za-z_]

Happy coding!
