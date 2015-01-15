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
 * Class ValidatorFile
 *
 * This file is a modified version of the Symphony file validator, part of the Symfony package. Credit goes to Bernhard Schussek <bschussek@gmail.com>
 *
 * @package Oligriffiths\Component\Validation
 */
class ValidatorFile extends ValidatorAbstract
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
			'filter' => false,
            'value_type' => null,
            'maxSize' => null,
            'mimeTypes' => array(),
            'notFoundMessage' => 'The file {{value}} could not be found',
            'notReadableMessage' => 'The file {{value}} is not readable',
            'maxSizeMessage' => 'The file is too large ({{size}} {{suffix}}). Allowed maximum size is {{limit}} {{suffix}}',
            'mimeTypesMessage' => 'The mime type of the file is invalid ({{type}}). Allowed mime types are {{types}}',

            'uploadIniSizeErrorMessage' => 'The file is too large. Allowed maximum size is {{limit}} {{suffix}}',
            'uploadFormSizeErrorMessage' => 'The file is too large',
            'uploadPartialErrorMessage' => 'The file was only partially uploaded',
            'uploadNoFileErrorMessage' => 'No file was uploaded',
            'uploadNoTmpDirErrorMessage' => 'No temporary folder was configured in php.ini',
            'uploadCantWriteErrorMessage' => 'Cannot write temporary file to disk',
            'uploadExtensionErrorMessage' => 'A PHP extension caused the upload to fail',
            'uploadErrorMessage' => 'The file could not be uploaded',
		));

		parent::_initialize($config);
	}


	/**
	 * Validate a file
	 *
	 * @param string|array $value - this can be either a file path or an array from $_FILES
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function _validate($value)
	{
		if (null === $value || '' === $value) {
			return;
		}

		$message = null;
        $config = $this->getConfig();

		if (is_array($value)) {
			$value = new Library\ObjectConfig($value);

			$message = null;
			if($value->error){
				switch ($value->error) {
					case UPLOAD_ERR_INI_SIZE:
						$uploadSize = ini_get('upload_max_filesize');

						$uploadFormat = strtoupper(substr($uploadSize, -1, 1));
						switch($uploadFormat){
							case 'G': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024; break;
							case 'K': $upload = (float) substr($uploadSize, 0, -1) * 1024; break;
							default: $upload = $uploadSize; $uploadFormat = ''; break;
						}

						$maxFormat = strtoupper(substr($config->maxSize, -1, 1));
						switch($maxFormat){
							case 'G': $max = (float) substr($config->maxSize, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $max = (float) substr($config->maxSize, 0, -1) * 1024 * 1024; break;
							case 'K': $max = (float) substr($config->maxSize, 0, -1) * 1024; break;
							default: $max = $config->maxSize; $maxFormat = ''; break;
						}

						$max = $max ? min($upload, $max) : $upload;
						$format = $max == $upload ? $uploadFormat : $maxFormat;

						switch($format){
							case 'G': $max = $max / 1024 / 1024 / 1024; break;
							case 'M': $max = $max / 1024 / 1024; break;
							case 'K': $max = $max / 1024; break;
						}

						$message = $this->getMessage(array(
							'limit' => $max,
							'suffix' => $format.'B',
						), 'uploadIniSizeErrorMessage');
						break;

					case UPLOAD_ERR_FORM_SIZE:
						$message = $config->uploadFormSizeErrorMessage;
						break;

					case UPLOAD_ERR_PARTIAL:
						$message = $config->uploadPartialErrorMessage;
						break;

					case UPLOAD_ERR_NO_FILE:
						$message = $config->uploadNoFileErrorMessage;
						break;

					case UPLOAD_ERR_NO_TMP_DIR:
						$message = $config->uploadNoTmpDirErrorMessage;
						break;

					case UPLOAD_ERR_CANT_WRITE:
						$message = $config->uploadCantWriteErrorMessage;
						break;

					case UPLOAD_ERR_EXTENSION:
						$message = $config->uploadExtensionErrorMessage;
						break;

					default:
						$message = $config->uploadErrorMessage;
						break;
				}
			}

			if($message){
				throw new \RuntimeException($message);
			}

			return true;
		}

		if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			$message = $this->getMessage(array('value_type' => 'string', 'value' => gettype($value)), 'message_invalid');
			throw new \RuntimeException($message);
		}

		$path = (string) $value;
		if (!is_file($path)) {
			throw new \RuntimeException($this->getMessage($path, 'notFoundMessage'));
			return;
		}

		if (!is_readable($path)) {
			throw new \RuntimeException($this->getMessage($path, 'notReadableMessage'));
			return;
		}

		if ($config->maxSize) {

			$format = strtoupper(substr($config->maxSize, -1, 1));
			$limit = (float) substr($config->maxSize, 0, -1);
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
					$limit = $config->maxSize;
					$format = '';
					$size = filesize($path);
					break;
			}

			if ($size > $limit) {
				throw new \RuntimeException($this->getMessage(array(
					'size'    => $size,
					'limit'   => $limit,
					'suffix'  => $format.'B',
					'file'    => $path,
				), 'maxSizeMessage'));
			}
		}

		$mimeTypes = $config->mimeTypes->toArray();
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
				throw new \RuntimeException($this->getMessage(array(
					'type'    => '"'.$mime.'"',
					'types'   => '"'.implode('", "', $mimeTypes) .'"',
					'file'    => $path,
				), 'mimeTypesMessage'));
			}
		}

		return true;
	}
}
