<?php
/**
 * Validation Component
 *
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/oligriffiths/Nooku-Validation-Component for the canonical source repository
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

/**
 * Class ValidatorImage
 *
 * Image validator. Validates image against various parameters.
 *
 * This file is a modified version of the Symphony file validator, part of the Symfony package. Credit to Bernhard Schussek <bschussek@gmail.com>
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorImage extends ValidatorFile
{
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
            'mime_types' => array('image/*'),
            'min_width' => null,
            'max_width' => null,
            'min_height' => null,
            'max_height' => null
        ));

        parent::_initialize($config);
    }


	/**
	 * Validate an image
	 *
	 * Images are first validated against the file validator, then against conditions set the config options above
	 *
	 * @see ConstraintImage for options
	 * @see ValidatorInterface::validate
	 */
	public function validate($value)
	{
		parent::validate($value);
        
        $config = $this->getConfig();

		if (null === $config->min_width && null === $config->max_width
			&& null === $config->min_height && null === $config->max_height) {
			return;
		}

		$size = @getimagesize($value);
		if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
			throw new \RuntimeException($this->getMessage('no_size'));

			return;
		}

		$width  = $size[0];
		$height = $size[1];

		$message = null;
		if ($config->min_width) {
			if (!ctype_digit((string) $config->min_width)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid minimum width', $config->min_width));
			}

			if ($width < $config->min_width) {
				throw new \RuntimeException($this->getMessage(array(
					'width'    => $width,
					'min_width' => $config->min_width
				), 'min_width'));
			}
		}

		if ($config->max_width) {
			if (!ctype_digit((string) $config->max_width)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid maximum width', $config->max_width));
			}

			if ($width > $config->max_width) {
				throw new \RuntimeException($this->getMessage(array(
					'width'    => $width,
					'max_width' => $config->max_width
				), 'max_width'));

				return;
			}
		}

		if ($config->min_height) {
			if (!ctype_digit((string) $config->min_height)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid minimum height', $config->min_height));
			}

			if ($height < $config->min_height) {
				throw new \RuntimeException($this->getMessage(array(
					'height'    => $height,
					'min_height' => $config->min_height
				),'min_height'));

				return;
			}
		}

		if ($config->max_height) {
			if (!ctype_digit((string) $config->max_height)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid maximum height', $config->max_height));
			}

			if ($height > $config->max_height) {
				throw new \RuntimeException($this->getMessage(array(
					'height'    => $height,
					'max_height' => $config->max_height
				), 'max_height'));
			}
		}

		return true;
	}
}
