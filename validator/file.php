<?php

namespace Nooku\Component\Validation;

use Nooku\Library;

/**
 * This file is a modified version of the Symphony file validator, part of the Symfony package.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class ValidatorFile extends ValidatorDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'filter' => false
		));
		parent::_initialize($config);
	}


	/**
	 * Validate a file against the constraint
	 *
	 * @see ConstraintFile for constraint options
	 * @param string|array $value - this can be either a file path or an array from $_FILES
	 * @param ConstraintDefault $constraint
	 * @return bool
	 * @throws KException
	 */
	protected function _validate($value, ConstraintDefault $constraint)
	{
		if (null === $value || '' === $value) {
			return;
		}

		$message = null;

		if (is_array($value)) {
			$value = new Library\ObjectConfig($value);

			$message = null;
			if($value->error){
				switch ($value->error) {
					case UPLOAD_ERR_INI_SIZE:
						$uploadSize = ini_get('upload_max_filesize');
						$maxSize = $constraint->maxSize;

						$uploadFormat = strtoupper(substr($uploadSize, -1, 1));
						switch($uploadFormat){
							case 'G': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024; break;
							case 'K': $upload = (float) substr($uploadSize, 0, -1) * 1024; break;
							default: $upload = $uploadSize; $uploadFormat = ''; break;
						}

						$maxFormat = strtoupper(substr($constraint->maxSize, -1, 1));
						switch($maxFormat){
							case 'G': $max = (float) substr($constraint->maxSize, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $max = (float) substr($constraint->maxSize, 0, -1) * 1024 * 1024; break;
							case 'K': $max = (float) substr($constraint->maxSize, 0, -1) * 1024; break;
							default: $max = $constraint->maxSize; $maxFormat = ''; break;
						}

						$max = $max ? min($upload, $max) : $upload;
						$format = $max == $upload ? $uploadFormat : $maxFormat;

						switch($format){
							case 'G': $max = $max / 1024 / 1024 / 1024; break;
							case 'M': $max = $max / 1024 / 1024; break;
							case 'K': $max = $max / 1024; break;
						}

						$message = $constraint->getMessage(array(
							'limit' => $max,
							'suffix' => $format.'B',
						), 'uploadIniSizeErrorMessage');
						break;

					case UPLOAD_ERR_FORM_SIZE:
						$message = $constraint->uploadFormSizeErrorMessage;
						break;

					case UPLOAD_ERR_PARTIAL:
						$message = $constraint->uploadPartialErrorMessage;
						break;

					case UPLOAD_ERR_NO_FILE:
						$message = $constraint->uploadNoFileErrorMessage;
						break;

					case UPLOAD_ERR_NO_TMP_DIR:
						$message = $constraint->uploadNoTmpDirErrorMessage;
						break;

					case UPLOAD_ERR_CANT_WRITE:
						$message = $constraint->uploadCantWriteErrorMessage;
						break;

					case UPLOAD_ERR_EXTENSION:
						$message = $constraint->uploadExtensionErrorMessage;
						break;

					default:
						$message = $constraint->uploadErrorMessage;
						break;
				}
			}

			if($message){
				throw new \RuntimeException($message);
			}

			return true;
		}

		if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			$message = $constraint->getMessage(array('value_type' => 'string', 'value' => gettype($value)), 'message_invalid');
			throw new \RuntimeException($message);
		}

		$path = (string) $value;
		if (!is_file($path)) {
			throw new \RuntimeException($constraint->getMessage($path, 'notFoundMessage'));
			return;
		}

		if (!is_readable($path)) {
			throw new \RuntimeException($constraint->getMessage($path, 'notReadableMessage'));
			return;
		}

		if ($constraint->maxSize) {

			$format = strtoupper(substr($constraint->maxSize, -1, 1));
			$limit = (float) substr($constraint->maxSize, 0, -1);
			switch($format){
				case 'G':
					$size = round(filesize($path) / 1024 / 1024 / 1024, 2);
					break;
				case 'M':
					$size = round(filesize($path) / 1024 / 1024, 2);
					break;
				case 'K':
					$size = round(filesize($path) / 1024, 2);
					break;
				default:
					$limit = $constraint->maxSize;
					$format = '';
					$size = filesize($path);
					break;
			}

			if ($size > $limit) {
				throw new \RuntimeException($constraint->getMessage(array(
					'size'    => $size,
					'limit'   => $limit,
					'suffix'  => $format.'B',
					'file'    => $path,
				), 'maxSizeMessage'));
			}
		}

		$mimeTypes = $constraint->mimeTypes;
		if (count($mimeTypes)) {

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
				throw new \RuntimeException($constraint->getMessage(array(
					'type'    => '"'.$mime.'"',
					'types'   => '"'.implode('", "', $mimeTypes) .'"',
					'file'    => $path,
				), 'mimeTypesMessage'));
			}
		}

		return true;
	}
}
