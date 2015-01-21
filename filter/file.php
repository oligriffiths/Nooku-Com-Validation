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
 * Class FilterFile
 *
 * This file is a modified version of the Symphony file validator, part of the Symfony package. Credit goes to Bernhard Schussek <bschussek@gmail.com>
 *
 * @package Oligriffiths\Component\Validation
 */
class FilterFile extends FilterAbstract
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
            'max_size' => null,
            'mime_types' => array(),
            'upload_max_size' => ini_get('upload_max_filesize')
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
	public function validate($value)
	{
		$message = null;
        $config = $this->getConfig();

		if (is_array($value)) {
			$value = new Library\ObjectConfig($value);

			$message = null;
			if($value->error){
				switch ($value->error) {
					case UPLOAD_ERR_INI_SIZE:
						$uploadSize = $config->upload_max_size;
						$uploadFormat = strtoupper(substr($uploadSize, -1, 1));

						switch($uploadFormat){
							case 'G': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $upload = (float) substr($uploadSize, 0, -1) * 1024 * 1024; break;
							case 'K': $upload = (float) substr($uploadSize, 0, -1) * 1024; break;
							default: $upload = $uploadSize; $uploadFormat = ''; break;
						}

						$maxFormat = strtoupper(substr($config->max_size, -1, 1));
						switch($maxFormat){
							case 'G': $max = (float) substr($config->max_size, 0, -1) * 1024 * 1024 * 1024; break;
							case 'M': $max = (float) substr($config->max_size, 0, -1) * 1024 * 1024; break;
							case 'K': $max = (float) substr($config->max_size, 0, -1) * 1024; break;
							default: $max = $config->max_size; $maxFormat = ''; break;
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
						), 'upload_ini_size');
						break;

					case UPLOAD_ERR_FORM_SIZE:
						$message = 'upload_form_size';
						break;

					case UPLOAD_ERR_PARTIAL:
						$message = 'upload_partial';
						break;

					case UPLOAD_ERR_NO_FILE:
						$message = 'upload_no_file';
						break;

					case UPLOAD_ERR_NO_TMP_DIR:
						$message = 'upload_no_tmp_dir';
						break;

					case UPLOAD_ERR_CANT_WRITE:
						$message = 'upload_cant_write';
						break;

					case UPLOAD_ERR_EXTENSION:
						$message = 'upload_extension';
						break;

					default:
						$message = 'upload';
						break;
				}
			}

			if($message){
				throw new \RuntimeException($this->getMessage($message));
			}

			return true;
		}

		if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
			$message = $this->getMessage(array('value_type' => 'string', 'value' => gettype($value)), 'invalid');
			throw new \RuntimeException($message);
		}

		$path = (string) $value;
		if (!is_file($path)) {
			throw new \RuntimeException($this->getMessage($path, 'not_found'));
			return;
		}

		if (!is_readable($path)) {
			throw new \RuntimeException($this->getMessage($path, 'not_readable'));
			return;
		}

		if ($config->max_size) {

			$format = strtoupper(substr($config->max_size, -1, 1));
			$limit = (float) substr($config->max_size, 0, -1);
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
					$limit = $config->max_size;
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
				), 'max_size'));
			}
		}

		$mime_types = $config->mime_types->toArray();
		if (count($mime_types)) {

			$mime = mime_content_type($value);
			$valid = false;

			foreach ($mime_types as $mimeType) {
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
					'mime_type'    => '"'.$mime.'"',
					'mime_types'   => '"'.implode('", "', $mime_types) .'"',
					'file'    => $path,
				), 'invalid_mime_type'));
			}
		}

		return true;
	}
}
