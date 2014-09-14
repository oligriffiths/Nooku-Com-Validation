<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintFile extends ConstraintDefault
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'value_type' => null,
			'maxSize' => null,
			'mimeTypes' => array(),
			'notFoundMessage' => 'The file {{ value }} could not be found',
			'notReadableMessage' => 'The file {{ value }} is not readable',
			'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}',
			'mimeTypesMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}',
		
			'uploadIniSizeErrorMessage' => 'The file is too large. Allowed maximum size is {{ limit }} {{ suffix }}',
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
}