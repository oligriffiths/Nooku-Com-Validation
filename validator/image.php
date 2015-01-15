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
            'mimeTypes' => array('image/*'),
            'minWidth' => null,
            'maxWidth' => null,
            'maxHeight' => null,
            'minHeight' => null,

            'mimeTypesMessage' => 'This file is not a valid image',
            'sizeNotDetectedMessage' => 'The size of the image could not be detected',
            'maxWidthMessage' => 'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px',
            'minWidthMessage' => 'The image width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px',
            'maxHeightMessage' => 'The image height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px',
            'minHeightMessage' => 'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px',
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
	protected function _validate($value)
	{
		parent::_validate($value);
        
        $config = $this->getConfig();

		if (null === $config->minWidth && null === $config->maxWidth
			&& null === $config->minHeight && null === $config->maxHeight) {
			return;
		}

		$size = @getimagesize($value);
		if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
			throw new \RuntimeException($this->getMessage($config->sizeNotDetectedMessage));

			return;
		}

		$width  = $size[0];
		$height = $size[1];

		$message = null;
		if ($config->minWidth) {
			if (!ctype_digit((string) $config->minWidth)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid minimum width', $config->minWidth));
			}

			if ($width < $config->minWidth) {
				throw new \RuntimeException($this->getMessage(array(
					'width'    => $width,
					'min_width' => $config->minWidth
				), 'minWidthMessage'));
			}
		}

		if ($config->maxWidth) {
			if (!ctype_digit((string) $config->maxWidth)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid maximum width', $config->maxWidth));
			}

			if ($width > $config->maxWidth) {
				throw new \RuntimeException($this->getMessage(array(
					'width'    => $width,
					'max_width' => $config->maxWidth
				), 'maxWidthMessage'));

				return;
			}
		}

		if ($config->minHeight) {
			if (!ctype_digit((string) $config->minHeight)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid minimum height', $config->minHeight));
			}

			if ($height < $config->minHeight) {
				throw new \RuntimeException($this->getMessage(array(
					'height'    => $height,
					'min_height' => $config->minHeight
				),'minHeightMessage'));

				return;
			}
		}

		if ($config->maxHeight) {
			if (!ctype_digit((string) $config->maxHeight)) {
				throw new \RuntimeException(sprintf('"%s" is not a valid maximum height', $config->maxHeight));
			}

			if ($height > $config->maxHeight) {
				throw new \RuntimeException($this->getMessage(array(
					'height'    => $height,
					'max_height' => $config->maxHeight
				), 'maxHeightMessage'));
			}
		}

		return true;
	}
}
