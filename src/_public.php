<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of MediaExifInfo, a plugin for Dotclear 2.
# 
# Copyright (c) 2019 wwwouaiebe
# contact: http://www.ouaie.be/contact
# this plugin is inspired by the entryPhotoExifWidget plugin  by Jean-Christian Denis
# and the attachments plugin by Olivier Meunier
# 
# This  program is free software;
# you can redistribute it and/or modify it under the terms of the 
# GNU General Public License as published by the Free Software Foundation;
# either version 3 of the License, or any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA#
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {return;}

$core->tpl->addBlock('MediaExifInfos', array('mediaExifInfoTpl', 'MediaExifInfos'));
$core->tpl->addBlock('MediaExifInfosHeader', array('mediaExifInfoTpl', 'MediaExifInfosHeader'));
$core->tpl->addBlock('MediaExifInfosFooter', array('mediaExifInfoTpl', 'MediaExifInfosFooter'));
$core->tpl->addBlock('MediaExifInfoIf', array('mediaExifInfoTpl', 'MediaExifInfoIf'));

$core->tpl->addValue('MediaExifInfoFileName', array('mediaExifInfoTpl', 'MediaExifInfoFileName'));
$core->tpl->addValue('MediaExifInfoMimeType', array('mediaExifInfoTpl', 'MediaExifInfoMimeType'));
$core->tpl->addValue('MediaExifInfoSize', array('mediaExifInfoTpl', 'MediaExifInfoSize'));
$core->tpl->addValue('MediaExifInfoHtml', array('mediaExifInfoTpl', 'MediaExifInfoHtml'));
$core->tpl->addValue('MediaExifInfoThumbnailRelUrl', array('mediaExifInfoTpl', 'MediaExifInfoThumbnailRelUrl'));
$core->tpl->addValue('MediaExifInfoRelUrl', array('mediaExifInfoTpl', 'MediaExifInfoRelUrl'));
$core->tpl->addValue('MediaExifInfoClass', array('mediaExifInfoTpl', 'MediaExifInfoClass'));
$core->tpl->addValue('MediaExifInfoExposureTime', array('mediaExifInfoTpl', 'MediaExifInfoExposureTime'));
$core->tpl->addValue('MediaExifInfoFNumber', array('mediaExifInfoTpl', 'MediaExifInfoFNumber'));
$core->tpl->addValue('MediaExifInfoFocalLength', array('mediaExifInfoTpl', 'MediaExifInfoFocalLength'));
$core->tpl->addValue('MediaExifInfoISOSpeedRatings', array('mediaExifInfoTpl', 'MediaExifInfoISOSpeedRatings'));
$core->tpl->addValue('MediaExifInfoMake', array('mediaExifInfoTpl', 'MediaExifInfoMake'));
$core->tpl->addValue('MediaExifInfoModel', array('mediaExifInfoTpl', 'MediaExifInfoModel'));
$core->tpl->addValue('MediaExifInfoDateTimeOriginal', array('mediaExifInfoTpl', 'MediaExifInfoDateTimeOriginal'));
$core->tpl->addValue('MediaExifInfoAllExif', array('mediaExifInfoTpl', 'MediaExifInfoAll'));

class mediaExifInfoTpl
{

	/* MediaExifInfos */
	
    public static function MediaExifInfos($attr, $content)
    {
       $res =
            "<?php\n" .
            'if ($_ctx->posts !== null && $core->media) {' . "\n" .
            '$_ctx->mediaInfos = new ArrayObject($core->media->getPostMedia($_ctx->posts->post_id,null,"attachment"));' . "\n" .
            "?>\n" .
            '<?php foreach ($_ctx->mediaInfos as $attach_i => $attach_f) : ' .
            '$GLOBALS[\'attach_i\'] = $attach_i; $GLOBALS[\'attach_f\'] = $attach_f;' .
            '$_ctx->file_url = $attach_f->file_url;' .
 			'$m = fileExifInfo::SearchExifData(\'public/\' . $attach_f->relname);' .
			' ?>' .
            $content .
            '<?php endforeach; $_ctx->mediaInfos = null; unset($attach_i,$attach_f,$_ctx->file_url); ?>' .

            "<?php } ?>\n";

        return $res;
    }

	/* MediaExifInfosHeader */
	
    public static function MediaExifInfosHeader($attr, $content)
    {
        return
            "<?php if (\$attach_i == 0) : ?>" .
            $content .
            "<?php endif; ?>";
    }

	/* MediaExifInfosFooter */
	
    public static function MediaExifInfosFooter($attr, $content)
    {
        return
            "<?php if (\$attach_i+1 == count(\$_ctx->mediaInfos)) : ?>" .
            $content .
            "<?php endif; ?>";
    }

	/* MediaExifInfoIf */

     public static function MediaExifInfoIf($attr, $content)
    {
        $if = array();

        $operator = isset($attr['operator']) ? dcTemplate::getOperator($attr['operator']) : '&&';

        if (isset($attr['has_exif'])) {
            $sign = (boolean) $attr['has_exif'] ? '' : '!';
            $if[] = $sign . '$m[\'has_exif\']';
        }

        if (isset($attr['has_thumbnail'])) {
            $sign = (boolean) $attr['has_thumbnail'] ? '' : '!';
            $if[] = $sign . '$m[\'has_thumbnail\']';
        }

        if (isset($attr['is_jpg'])) {
            $sign = (boolean) $attr['is_jpg'] ? '' : '!';
            $if[] = $sign . '$m[\'is_jpg\']';
        }
		
        if (isset($attr['is_tiff'])) {
            $sign = (boolean) $attr['is_tiff'] ? '' : '!';
            $if[] = $sign . '$m[\'is_tiff\']';
        }

        if (count($if) != 0) {
            return '<?php if(' . implode(' ' . $operator . ' ', (array) $if) . ') : ?>' . $content . '<?php endif; ?>';
        } else {
            return $content;
        }
    }

	/* MediaExifInfoMimeType */

    public static function MediaExifInfoMimeType($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
       return '<?php echo ' . sprintf($f, '$m[\'MimeType\']') . '; ?>';
    }

	/* MediaExifInfoFileName */

    public static function MediaExifInfoFileName($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'FileName\']') . '; ?>';
    }

	/* MediaExifInfoHtml */

    public static function MediaExifInfoHtml($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Html\']') . '; ?>';
    }

	/* MediaExifInfoThumbnailRelUrl */

    public static function MediaExifInfoThumbnailRelUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'ThumbnailRelUrl\']') . '; ?>';
    }

	/* MediaExifInfoRelUrl */

    public static function MediaExifInfoRelUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'RelUrl\']') . '; ?>';
    }

	/* MediaExifInfoClass */

    public static function MediaExifInfoClass($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Class\']') . '; ?>';
    }

	/* MediaExifInfoExposureTime */

     public static function MediaExifInfoExposureTime($attr)
    {
 	   return mediaExifInfoTpl::MediaExifInfoFormat($attr, 'ExposureTime');
    }

	/* MediaExifInfoFNumber */

    public static function MediaExifInfoFNumber($attr)
    {
 	   return mediaExifInfoTpl::MediaExifInfoFormat($attr, 'FNumber');
    }

	/* MediaExifInfoFocalLength */

    public static function MediaExifInfoFocalLength($attr)
    {
	   return mediaExifInfoTpl::MediaExifInfoFormat($attr, 'FocalLength');
    }

	/* MediaExifInfoISOSpeedRatings */

    public static function MediaExifInfoISOSpeedRatings($attr)
    {
 	   return mediaExifInfoTpl::MediaExifInfoFormat($attr, 'ISOSpeedRatings');
    }

	/* MediaExifInfoFormat */

	public static function MediaExifInfoFormat($attr, $tech)
	{
 		$format = '%s';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',$m[\'' . $tech . '\']) ') . '; ?>';
	}

	/* MediaExifInfoSize */

    public static function MediaExifInfoSize($attr)
    {
		$divisor = "1";
        if (isset($attr['divisor'])) {
			$divisor = $attr['divisor'];
		}
		
		$format = "%d";
        if (isset($attr['format'])) {
			$format = $attr['format'];
		}
	
		$var = '$m[\'Size\']/' . $divisor;
        $f = $GLOBALS['core']->tpl->getFilters($attr);
       return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',' . $var .')' ) . ' ; ?>';
    }

	/* MediaExifInfoAll */

	public static function MediaExifInfoAll($attr)
	{
 		$format = '%s %s %s %s %s %s';
 		$var = '$m[\'Make\'],$m[\'Model\'],$m[\'FocalLength\'],$m[\'FNumber\'],$m[\'ExposureTime\'],$m[\'ISOSpeedRatings\']';
        if (isset($attr['format'])) {
			$formatArray = explode ( '%', addslashes($attr['format']) );
			$var = '';
			$format = '';
			foreach ( $formatArray  as $i => $formatPart ) {
				switch ( $formatPart ) {
					case 'Model':
					case 'FocalLength':
					case 'FNumber':
					case 'ExposureTime':
					case 'ISOSpeedRatings':
					case 'Make':
						$var .= '$m[\'' . $formatPart . '\'],';
						$format .= '%s';
						break;
					default:
						$format .= $formatPart;
						break;
				}					
			}
			$var = substr ($var, 0, -1 );
		}
 		$f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',' . $var . ')' ) . '; ?>';
	}

	/* MediaExifInfoMake */

    public static function MediaExifInfoMake($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Make\']') . '; ?>';
    }

	/* MediaExifInfoModel */

    public static function MediaExifInfoModel($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Model\']') . '; ?>';
    }

	/* MediaExifInfoDateTimeOriginal */

    public static function MediaExifInfoDateTimeOriginal($attr)
    {
		$format = '%A %e %B %Y %H:%M:%S';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'dt::dt2str(\'' . $format . '\', $m[\'DateTimeOriginal\'])' ) . '; ?>';
    }
}
if ( ! class_exists('fileExifInfo') ) {
	class fileExifInfo 
	{
		/* SearchExifData */

		public static function SearchExifData( $fi )
		{
			$mi = array(
				'RelUrl' => $fi,
				'Class' => '',
				'ExposureTime' => '',
				'FNumber' => '',
				'FocalLength' => '',
				'ISOSpeedRatings' => '',
				'Make' => '',
				'Model' => '',
				'DateTimeOriginal' => '',
				'has_exif' => false,
				'ThumbnailRelUrl' => '',
				'Size' => '0',
				'Html' => '',
				'MimeType' => '',
				'FileName' =>'',
				'has_thumbnail' =>false,
				'is_jpg' => false,
				'is_tiff' => false
			);
			if ( file_exists($mi['RelUrl']) )
			{
				$path_parts = pathinfo($mi['RelUrl']);
				$ThumbnailRelUrl = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_s.' . $path_parts['extension'];
				$ext = strtoupper ( $path_parts['extension'] );
				if ( 'JPG' != $ext && 'JPEG' != $ext && 'TIF' != $ext && 'TIFF' != $ext ) {
					return $mi;
				}
				if ( 'JPG' == $ext || 'JPEG' == $ext ) {
					$mi['is_jpg'] = true;
				}
				else {
					$mi['is_tiff'] = true;
				}
				if ( file_exists($ThumbnailRelUrl) )
				{
					$mi['ThumbnailRelUrl'] = $ThumbnailRelUrl;
					$mi['has_thumbnail'] = true;
				}

				$exif = exif_read_data($mi['RelUrl'], 'ANY_TAG', true );
				if ( $exif )
				{
					if ( $exif[ 'FILE'] )
					{
						if (!empty($exif[ 'FILE']['FileSize']))
						{
							$mi['Size'] = $exif[ 'FILE']['FileSize'];
						}
						if (!empty($exif[ 'FILE']['MimeType']))
						{
							$mi['MimeType'] = $exif[ 'FILE']['MimeType'];
						}
						if (!empty($exif[ 'FILE']['FileName']))
						{
							$mi['FileName'] = $exif[ 'FILE']['FileName'];
						}
					}
					if ( $exif[ 'COMPUTED'] ) {
						if ( $exif[ 'COMPUTED']['Height'] && $exif[ 'COMPUTED']['Width'] )
						{
							$mi['Class'] =  $exif[ 'COMPUTED']['Height'] == $exif[ 'COMPUTED']['Width'] ? 'Square' : ( $exif[ 'COMPUTED']['Height'] > $exif[ 'COMPUTED']['Width'] ? "Portrait" : "Landscape" );
						}
						if ( $exif[ 'COMPUTED']['html'] ) {
							$mi['Html'] = $exif[ 'COMPUTED']['html'];
						}
					}
					if ( $exif[ 'IFD0'] )
					{
						if (!empty($exif[ 'IFD0']['Make']))
						{
							$mi['Make'] = $exif[ 'IFD0']['Make'];
						}
						if (!empty($exif[ 'IFD0']['Model']))
						{
							$mi['Model'] = $exif[ 'IFD0']['Model'];
						}
					}
					if ( $exif[ 'EXIF'] )
					{
						if (!empty($exif[ 'EXIF']['FNumber']))
						{
							$fl = sscanf($exif[ 'EXIF']['FNumber'],'%d/%d');
							$mi['FNumber'] = $fl && $fl[0] && $fl[1] ? $fl[0]/$fl[1].'' : $exif[ 'EXIF']['FNumber'];
						}
						if (!empty($exif[ 'EXIF']['ExposureTime']))
						{
							$fl = sscanf($exif[ 'EXIF']['ExposureTime'],'%d/%d');
							if ( $fl && $fl[0] && $fl[1] )
							{
								if ( $fl[0] == $fl[1] )
								{
									$mi['ExposureTime'] = '1';
								}
								else if ( $fl[0] > $fl[1] )
								{
									$mi['ExposureTime'] = sprintf ( '%d',  $fl[0]/ $fl[1] );
								}
								else
								{
									$mi['ExposureTime'] = $exif[ 'EXIF']['ExposureTime'];
								}
							}
							else
							{
								$mi['ExposureTime'] = $exif[ 'EXIF']['ExposureTime'];
							}
						}
						if (!empty($exif[ 'EXIF']['ISOSpeedRatings']))
						{
							$mi['ISOSpeedRatings'] = $exif[ 'EXIF']['ISOSpeedRatings'];
						}
						if (!empty($exif[ 'EXIF']['FocalLength']))
						{
							$fl = sscanf($exif[ 'EXIF']['FocalLength'],'%d/%d');
							$mi['FocalLength'] = $fl && $fl[0] && $fl[1] ? sprintf ( '%d',  $fl[0]/ $fl[1] ) : $im['FocalLength'];
						}
						if (!empty($exif[ 'EXIF']['DateTimeOriginal']))
						{
							$mi['DateTimeOriginal'] = $exif[ 'EXIF']['DateTimeOriginal'];
						}
					}
				}
				
				if ( !empty($mi['ISOSpeedRatings']) && !empty($mi['FocalLength']) && !empty($mi['FNumber']) && !empty($mi['ExposureTime']) )
				{
					$mi['has_exif'] = true;
				}			
			} 
			
			return $mi;
		}
	}	
}