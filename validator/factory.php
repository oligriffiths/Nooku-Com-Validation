<?php
/**
 * Validation Component
 *
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/oligriffiths/Nooku-Validation-Component for the canonical source repository
 */

namespace Oligriffiths\Component\Validation;

use Nooku\Library;


class ValidatorFactory extends Library\FilterFactory
{
    public function createValidator($validator, array $config = array())
    {
        if(is_string($validator) && strpos($validator, '.') === false )
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('filter');
            $identifier['name'] = $validator;
        }
        else $identifier = $validator;

        $validator = $this->getObject($identifier, $config);

        $validator->decorate('com://oligriffiths/validation.decorator.validator');

        $decorator = $this->getObject('com://oligriffiths/validation.decorator.validator', array('delegate' => $validator));

        return $decorator;
    }
}