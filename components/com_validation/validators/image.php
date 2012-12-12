<?php

/**
 * This file is a modified version of the Symphony file validator, part of the Symfony package.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorImage extends ComValidationValidatorFile
{
	/**
	 * Validate an image against the constraint
	 *
	 * Images are first validated against the file validator, then against conditions set in the constraint
	 *
	 * @see ComValidationConstraintImage for options
	 * @see ComValidationValidatorInterface::validate
	 */
	protected function _validate($value, ComValidationConstraintDefault $constraint)
	{
		parent::_validate($value, $constraint);

		if (null === $constraint->minWidth && null === $constraint->maxWidth
			&& null === $constraint->minHeight && null === $constraint->maxHeight) {
			return;
		}

		$size = @getimagesize($value);
		if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
			throw new KException($constraint->getMessage($constraint->sizeNotDetectedMessage));

			return;
		}

		$width  = $size[0];
		$height = $size[1];

		$message = null;
		if ($constraint->minWidth) {
			if (!ctype_digit((string) $constraint->minWidth)) {
				throw new KException(sprintf('"%s" is not a valid minimum width', $constraint->minWidth));
			}

			if ($width < $constraint->minWidth) {
				throw new KException($constraint->getMessage(array(
					'width'    => $width,
					'min_width' => $constraint->minWidth
				), 'minWidthMessage'));
			}
		}

		if ($constraint->maxWidth) {
			if (!ctype_digit((string) $constraint->maxWidth)) {
				throw new KException(sprintf('"%s" is not a valid maximum width', $constraint->maxWidth));
			}

			if ($width > $constraint->maxWidth) {
				throw new KException($constraint->getMessage(array(
					'width'    => $width,
					'max_width' => $constraint->maxWidth
				), 'maxWidthMessage'));

				return;
			}
		}

		if ($constraint->minHeight) {
			if (!ctype_digit((string) $constraint->minHeight)) {
				throw new KException(sprintf('"%s" is not a valid minimum height', $constraint->minHeight));
			}

			if ($height < $constraint->minHeight) {
				throw new KException($constraint->getMessage(array(
					'height'    => $height,
					'min_height' => $constraint->minHeight
				),'minHeightMessage'));

				return;
			}
		}

		if ($constraint->maxHeight) {
			if (!ctype_digit((string) $constraint->maxHeight)) {
				throw new KException(sprintf('"%s" is not a valid maximum height', $constraint->maxHeight));
			}

			if ($height > $constraint->maxHeight) {
				throw new KException($constraint->getMessage(array(
					'height'    => $height,
					'max_height' => $constraint->maxHeight
				), 'maxHeightMessage'));
			}
		}

		return true;
	}
}
