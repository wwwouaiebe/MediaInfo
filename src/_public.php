<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of MediaInfo, a plugin for Dotclear 2.
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

$core->tpl->addBlock('MediaInfos', array('mediaInfoTpl', 'MediaInfos'));
$core->tpl->addBlock('MediaInfosHeader', array('mediaInfoTpl', 'MediaInfosHeader'));
$core->tpl->addBlock('MediaInfosFooter', array('mediaInfoTpl', 'MediaInfosFooter'));
$core->tpl->addBlock('MediaInfoIf', array('mediaInfoTpl', 'MediaInfoIf'));

$core->tpl->addValue('MediaInfoUrl', array('mediaInfoTpl', 'MediaInfoUrl'));
$core->tpl->addValue('MediaInfoFileName', array('mediaInfoTpl', 'MediaInfoFileName'));
$core->tpl->addValue('MediaInfoMimeType', array('mediaInfoTpl', 'MediaInfoMimeType'));
$core->tpl->addValue('MediaInfoSize', array('mediaInfoTpl', 'MediaInfoSize'));
$core->tpl->addValue('MediaInfoThumbnailUrl', array('mediaInfoTpl', 'MediaInfoThumbnailUrl'));
$core->tpl->addValue('MediaInfoRelUrl', array('mediaInfoTpl', 'MediaInfoRelUrl'));
$core->tpl->addValue('MediaInfoClass', array('mediaInfoTpl', 'MediaInfoClass'));
$core->tpl->addValue('MediaInfoExposureTime', array('mediaInfoTpl', 'MediaInfoExposureTime'));
$core->tpl->addValue('MediaInfoFNumber', array('mediaInfoTpl', 'MediaInfoFNumber'));
$core->tpl->addValue('MediaInfoFocalLength', array('mediaInfoTpl', 'MediaInfoFocalLength'));
$core->tpl->addValue('MediaInfoISOSpeedRatings', array('mediaInfoTpl', 'MediaInfoISOSpeedRatings'));
$core->tpl->addValue('MediaInfoMake', array('mediaInfoTpl', 'MediaInfoMake'));
$core->tpl->addValue('MediaInfoModel', array('mediaInfoTpl', 'MediaInfoModel'));
$core->tpl->addValue('MediaInfoDateTimeOriginal', array('mediaInfoTpl', 'MediaInfoDateTimeOriginal'));
$core->tpl->addValue('MediaInfoAllExif', array('mediaInfoTpl', 'MediaInfoAllExif'));

$core->tpl->addValue('EntryMediaInfoCount', array('mediaInfoTpl', 'EntryMediaInfoCount'));

$core->addBehavior('tplIfConditions', array('mediaInfoBehavior', 'tplIfConditions'));

class mediaInfoTpl
{

    public static function MediaInfos($attr, $content)
    {
        $res =
            "<?php\n" .
            'if ($_ctx->posts !== null && $core->media) {' . "\n" .
            '$_ctx->mediaInfos = new ArrayObject($core->media->getPostMedia($_ctx->posts->post_id,null,"attachment"));' . "\n" .
            "?>\n" .
            '<?php foreach ($_ctx->mediaInfos as $attach_i => $attach_f) : ' .
            '$GLOBALS[\'attach_i\'] = $attach_i; $GLOBALS[\'attach_f\'] = $attach_f;' .
            '$_ctx->file_url = $attach_f->file_url;' .
			'$m = mediaInfoTpl::MediaInfoSearch($attach_f->relname);' .
			' ?>' .
            $content .
            '<?php endforeach; $_ctx->mediaInfos = null; unset($attach_i,$attach_f,$_ctx->file_url); ?>' .

            "<?php } ?>\n";

        return $res;
    }
	
    public static function MediaInfoSearch($fi)
    {
		$mi = array(
			'RelUrl' => 'public/' . $fi,
			'Class' => 'Landscape',
			'ExposureTime' => '',
			'FNumber' => '',
			'FocalLength' => '',
			'ISOSpeedRatings' => '',
			'Make' => '',
			'Model' => '',
			'DateTimeOriginal' => '',
			'has_exif' => false,
			'ThumbnailUrl' => '',
			'Size' => '0',
			'MimeType' => '',
			'FileName' =>'',
			'has_thumbnail' =>false,
			'is_jpg' => false,
			'is_tiff' => false
		);
		if ( file_exists($mi['RelUrl']) )
		{
			$path_parts = pathinfo($mi['RelUrl']);
			$ThumbnailUrl = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_s.' . $path_parts['extension'];
			$ext = strtoupper ( $path_parts['extension'] );
			if ( 'JPG' != $ext && 'JPEG' != $ext && 'TIF' != $ext && 'TIFF' != $ext ) {
				return;
			}
			if ( 'JPG' == $ext || 'JPEG' == $ext ) {
				$mi['is_jpg'] = true;
			}
			else {
				$mi['is_tiff'] = true;
			}
			if ( file_exists($ThumbnailUrl) )
			{
				$mi['ThumbnailUrl'] = $ThumbnailUrl;
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
				if ( $exif[ 'COMPUTED'] && $exif[ 'COMPUTED']['Height'] && $exif[ 'COMPUTED']['Width'] )
				{
					$mi['Class'] = $exif[ 'COMPUTED']['Height'] > $exif[ 'COMPUTED']['Width'] ? "Portrait" : "Landscape";
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

    public static function MediaInfosHeader($attr, $content)
    {
        return
            "<?php if (\$attach_i == 0) : ?>" .
            $content .
            "<?php endif; ?>";
    }

    public static function MediaInfosFooter($attr, $content)
    {
        return
            "<?php if (\$attach_i+1 == count(\$_ctx->mediaInfos)) : ?>" .
            $content .
            "<?php endif; ?>";
    }

     public static function MediaInfoIf($attr, $content)
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

    public static function MediaInfoMimeType($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
       return '<?php echo ' . sprintf($f, '$m[\'MimeType\']') . '; ?>';
    }

    public static function MediaInfoFileName($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'FileName\']') . '; ?>';
    }

    public static function MediaInfoThumbnailUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'ThumbnailUrl\']') . '; ?>';
    }

    public static function MediaInfoUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$attach_f->file_url') . '; ?>';
    }
	
    public static function MediaInfoRelUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'RelUrl\']') . '; ?>';
    }
	
   public static function MediaInfoClass($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Class\']') . '; ?>';
    }
	
     public static function MediaInfoExposureTime($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'ExposureTime');
    }

    public static function MediaInfoFNumber($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'FNumber');
    }

    public static function MediaInfoFocalLength($attr)
    {
	   return mediaInfoTpl::MediaInfoFormat($attr, 'FocalLength');
    }

    public static function MediaInfoISOSpeedRatings($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'ISOSpeedRatings');
    }

	public static function MediaInfoFormat($attr, $tech)
	{
 		$format = '%s';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
       return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',$m[\'' . $tech . '\']) ') . '; ?>';
	}
	
    public static function MediaInfoSize($attr)
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
        return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',' . $var ) . ' ); ?>';
    }

	public static function MediaInfoAllExif($attr)
	{
 		$format = '%s %s %s %s %s';
 		$var = '$m[\'Model\'],$m[\'FocalLength\'],$m[\'FNumber\'],$m[\'ExposureTime\'],$m[\'ISOSpeedRatings\']';
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
        return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',' . $var ) . ' ) ; ?>';
	}
	
    public static function MediaInfoMake($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Make\']') . '; ?>';
    }

    public static function MediaInfoModel($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'Model\']') . '; ?>';
    }

    public static function MediaInfoDateTimeOriginal($attr)
    {
		$format = '%A %e %B %Y %H:%M:%S';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'dt::dt2str(\'' . $format . '\', $m[\'DateTimeOriginal\'])' ) . '; ?>';
    }

     public static function EntryMediaInfoCount($attr)
    {
        global $core;
        return $core->tpl->displayCounter(
            '$_ctx->posts->countMedia(\'attachment\')',
            array(
                'none' => 'no mediaInfos',
                'one'  => 'one mediaInfo',
                'more' => '%d mediaInfos'
            ),
            $attr,
            false
        );
    }
}

class mediaInfoBehavior
{
    public static function tplIfConditions($tag, $attr, $content, $if)
    {
        if ($tag == "EntryIf" && isset($attr['has_mediaInfo'])) {
            $sign = (boolean) $attr['has_mediaInfo'] ? '' : '!';
            $if[] = $sign . '$_ctx->posts->countMedia(\'attachment\')';
        }
    }
}
