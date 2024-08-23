<?php

/**
 * Local mapping file to specify mime-types based on common file-name extensions.
 *
 * Please note that this mapping takes precedence over the content-based mime-type detection
 * and should only contain mappings which cannot be detected properly from the file contents.
 */

namespace App\Class;

class General
{
	public static function isEmpty($object)
    {
        if (!isset($object))
            return true;
        if (is_null($object))
            return true;
        if (is_string($object) && strlen($object) <= 0)
            return true;
        if (is_array($object) && empty($object))
            return true;
        if (is_numeric($object) && is_nan($object))
            return true;
        if (is_object($object) && $object == new \stdClass())
            return true;

        return false;
    }
	public static function mimetypes()
	{
		return array(		
			// images			
			'image/png' => 'png',		
			'image/jpeg' => 'jpg',
			'image/jpeg' => 'jpe',
			'image/jpeg' => 'jpeg',
			'image/gif' => 'gif',
			'image/bmp' => 'bmp',			
			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',
			// audio/video
			'mp3' => 'audio/mpeg',
			'mov' => 'video/quicktime',
			'qt' => 'video/quicktime',
			// adobe
			'application/pdf' => 'pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			// ms office
			'application/msword' => 'doc',
			'application/rtf' => 'rtf',
			'application/vnd.ms-excel' => 'xls',
			'application/vnd.ms-powerpoint' => 'ppt',		
			// open office
			'application/vnd.oasis.opendocument.text' => 'odt',
			'application/vnd.oasis.opendocument.spreadsheet' => 'ods',		
			//other
			'xls' => 'application/vnd.ms-excel',
			'xlm' => 'application/vnd.ms-excel',
			'xla' => 'application/vnd.ms-excel',
			'xlc' => 'application/vnd.ms-excel',
			'xlt' => 'application/vnd.ms-excel',
			'xlw' => 'application/vnd.ms-excel',		
			'ppt' => 'application/vnd.ms-powerpoint',
			'pps' => 'application/vnd.ms-powerpoint',
			'pot' => 'application/vnd.ms-powerpoint',
			'doc' => 'application/msword',
			'dot' => 'application/msword',
			'odc' => 'application/vnd.oasis.opendocument.chart',
			'otc' => 'application/vnd.oasis.opendocument.chart-template',
			'odf' => 'application/vnd.oasis.opendocument.formula',
			'otf' => 'application/vnd.oasis.opendocument.formula-template',
			'odg' => 'application/vnd.oasis.opendocument.graphics',
			'otg' => 'application/vnd.oasis.opendocument.graphics-template',
			'odi' => 'application/vnd.oasis.opendocument.image',
			'oti' => 'application/vnd.oasis.opendocument.image-template',
			'odp' => 'application/vnd.oasis.opendocument.presentation',
			'otp' => 'application/vnd.oasis.opendocument.presentation-template',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'odt' => 'application/vnd.oasis.opendocument.text',
			'otm' => 'application/vnd.oasis.opendocument.text-master',
			'ott' => 'application/vnd.oasis.opendocument.text-template',
			'oth' => 'application/vnd.oasis.opendocument.text-web',
			'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
			'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',		
			'xps' => 'application/vnd.ms-xpsdocument',
			'rar' => 'application/x-rar-compressed',
			'7z' => 'application/x-7z-compressed',
			's7z' => 'application/x-7z-compressed',
			'vcf' => 'text/vcard',
			'ics' => 'text/calendar',
			'dwg' => 'application/acad',
		);
	}
}
