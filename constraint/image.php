<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:16
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class ConstraintImage extends ConstraintFile
{
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
}