<?php
/**
 * Validation Component
 *
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/oligriffiths/Nooku-Validation-Component for the canonical source repository
 */

namespace Oligriffiths\Component\Validation;

use Nooku\Library;
use Nooku\Library\ObjectConfig;

/**
 * Class ValidatorAbstract
 *
 * Base class for validators
 *
 * @package Oligriffiths\Component\Validation
 */
abstract class FilterAbstract extends Library\FilterAbstract
{
    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getIdentifier()->getConfig()->append(array('decorators' => array('com://oligriffiths/validation.decorator.validator')));
    }


    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   Library\ObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'mixins' => array('com://oligriffiths/validation.mixin.message')
        ));

        parent::_initialize($config);
    }
}
