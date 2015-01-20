<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

return array(

    'identifiers'  => array(
        'com://oligriffiths/validation.filter.alnum'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.alpha'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.ascii'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.base64'       => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.boolean'      => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.cmd'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.date'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.digit'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.dirname'      => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.email'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.filename'     => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.float'        => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.html'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.identifier'   => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.ini'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.int'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.internalurl'  => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.ip'           => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.json'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.lang'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.md5'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.numeric'      => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.path'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.sha1'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.slug'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.string'       => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.tidy'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.time'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.timestamp'    => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.url'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.word'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.xml'          => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
        'com://oligriffiths/validation.filter.yaml'         => array('mixins' => array('com://oligriffiths/validation.mixin.message'), 'decorators' => array('com://oligriffiths/validation.decorator.validator')),
    )
);
