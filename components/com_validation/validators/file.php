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
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ComValidationValidatorFile extends ComValidationValidatorDefault
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, ComValidationConstraintDefault $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($value instanceof UploadedFile && !$value->isValid()) {
            switch ($value->getError()) {
                case UPLOAD_ERR_INI_SIZE:
                    $maxSize = UploadedFile::getMaxFilesize();
                    $maxSize = $constraint->maxSize ? min($maxSize, $constraint->maxSize) : $maxSize;
                    throw new ComValidationExceptionValidator($constraint->uploadIniSizeErrorMessage, array(
                        '{{ limit }}' => $maxSize,
                        '{{ suffix }}' => 'bytes',
                    ));

                    return;
                case UPLOAD_ERR_FORM_SIZE:
                    throw new ComValidationExceptionValidator($constraint->uploadFormSizeErrorMessage);

                    return;
                case UPLOAD_ERR_PARTIAL:
                    throw new ComValidationExceptionValidator($constraint->uploadPartialErrorMessage);

                    return;
                case UPLOAD_ERR_NO_FILE:
                    throw new ComValidationExceptionValidator($constraint->uploadNoFileErrorMessage);

                    return;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new ComValidationExceptionValidator($constraint->uploadNoTmpDirErrorMessage);

                    return;
                case UPLOAD_ERR_CANT_WRITE:
                    throw new ComValidationExceptionValidator($constraint->uploadCantWriteErrorMessage);

                    return;
                case UPLOAD_ERR_EXTENSION:
                    throw new ComValidationExceptionValidator($constraint->uploadExtensionErrorMessage);

                    return;
                default:
                    throw new ComValidationExceptionValidator($constraint->uploadErrorMessage);

                    return;
            }
        }

        if (!is_scalar($value) && !$value instanceof FileObject && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new ComValidationExceptionUnexpectedtype($value, 'string');
        }

        $path = $value instanceof FileObject ? $value->getPathname() : (string) $value;

        if (!is_file($path)) {
            throw new ComValidationExceptionValidator($constraint->notFoundMessage, array('{{ file }}' => $path));

            return;
        }

        if (!is_readable($path)) {
            throw new ComValidationExceptionValidator($constraint->notReadableMessage, array('{{ file }}' => $path));

            return;
        }

        if ($constraint->maxSize) {
            if (ctype_digit((string) $constraint->maxSize)) {
                $size = filesize($path);
                $limit = $constraint->maxSize;
                $suffix = 'bytes';
            } elseif (preg_match('/^(\d+)k$/', $constraint->maxSize, $matches)) {
                $size = round(filesize($path) / 1000, 2);
                $limit = $matches[1];
                $suffix = 'kB';
            } elseif (preg_match('/^(\d+)M$/', $constraint->maxSize, $matches)) {
                $size = round(filesize($path) / 1000000, 2);
                $limit = $matches[1];
                $suffix = 'MB';
            } else {
                throw new ConstraintDefinitionException(sprintf('"%s" is not a valid maximum size', $constraint->maxSize));
            }

            if ($size > $limit) {
                throw new ComValidationExceptionValidator($constraint->maxSizeMessage, array(
                    '{{ size }}'    => $size,
                    '{{ limit }}'   => $limit,
                    '{{ suffix }}'  => $suffix,
                    '{{ file }}'    => $path,
                ));

                return;
            }
        }

        if ($constraint->mimeTypes) {

            $mimeTypes = (array) $constraint->mimeTypes;
	        $mime = mime_content_type($value);
            $valid = false;

            foreach ($mimeTypes as $mimeType) {
                if ($mimeType === $mime) {
                    $valid = true;
                    break;
                }

                if ($discrete = strstr($mimeType, '/*', true)) {
                    if (strstr($mime, '/', true) === $discrete) {
                        $valid = true;
                        break;
                    }
                }
            }

            if (false === $valid) {
                throw new ComValidationExceptionValidator($constraint->mimeTypesMessage, array(
                    '{{ type }}'    => '"'.$mime.'"',
                    '{{ types }}'   => '"'.implode('", "', $mimeTypes) .'"',
                    '{{ file }}'    => $path,
                ));
            }
        }
    }
}
