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
 * Validates whether a value is a valid image file and is valid
 * against minWidth, maxWidth, minHeight and maxHeight constraints
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class ComValidationValidatorImage extends FileValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        parent::validate($value, $constraint);

        if (null === $constraint->minWidth && null === $constraint->maxWidth
            && null === $constraint->minHeight && null === $constraint->maxHeight) {
            return;
        }

        $size = @getimagesize($value);
        if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
            throw new ComValidationExceptionValidator($constraint->sizeNotDetectedMessage);

            return;
        }

        $width  = $size[0];
        $height = $size[1];

        if ($constraint->minWidth) {
            if (!ctype_digit((string) $constraint->minWidth)) {
                throw new ConstraintDefinitionException(sprintf('"%s" is not a valid minimum width', $constraint->minWidth));
            }

            if ($width < $constraint->minWidth) {
                throw new ComValidationExceptionValidator($constraint->minWidthMessage, array(
                    '{{ width }}'    => $width,
                    '{{ min_width }}' => $constraint->minWidth
                ));

                return;
            }
        }

        if ($constraint->maxWidth) {
            if (!ctype_digit((string) $constraint->maxWidth)) {
                throw new ConstraintDefinitionException(sprintf('"%s" is not a valid maximum width', $constraint->maxWidth));
            }

            if ($width > $constraint->maxWidth) {
                throw new ComValidationExceptionValidator($constraint->maxWidthMessage, array(
                    '{{ width }}'    => $width,
                    '{{ max_width }}' => $constraint->maxWidth
                ));

                return;
            }
        }

        if ($constraint->minHeight) {
            if (!ctype_digit((string) $constraint->minHeight)) {
                throw new ConstraintDefinitionException(sprintf('"%s" is not a valid minimum height', $constraint->minHeight));
            }

            if ($height < $constraint->minHeight) {
                throw new ComValidationExceptionValidator($constraint->minHeightMessage, array(
                    '{{ height }}'    => $height,
                    '{{ min_height }}' => $constraint->minHeight
                ));

                return;
            }
        }

        if ($constraint->maxHeight) {
            if (!ctype_digit((string) $constraint->maxHeight)) {
                throw new ConstraintDefinitionException(sprintf('"%s" is not a valid maximum height', $constraint->maxHeight));
            }

            if ($height > $constraint->maxHeight) {
                throw new ComValidationExceptionValidator($constraint->maxHeightMessage, array(
                    '{{ height }}'    => $height,
                    '{{ max_height }}' => $constraint->maxHeight
                ));
            }
        }
    }
}
