<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of MediaInfo, a plugin for Dotclear 2.
# 
# Copyright (c) 2019 wwwouaiebe
# contact: http://www.ouaie.be/contact
# this plugin is inspired by the entryPhotoExifWidget plugin  from Jean-Christian Denis
# and the attachments plugin from Olivier Meunier
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

$core->tpl->addValue('MediaInfoMimeType', array('mediaInfoTpl', 'MediaInfoMimeType'));
$core->tpl->addValue('MediaInfoType', array('mediaInfoTpl', 'MediaInfoType'));
$core->tpl->addValue('MediaInfoFileName', array('mediaInfoTpl', 'MediaInfoFileName'));
$core->tpl->addValue('MediaInfoSize', array('mediaInfoTpl', 'MediaInfoSize'));
$core->tpl->addValue('MediaInfoThumbnailURL', array('mediaInfoTpl', 'MediaInfoThumbnailURL'));
$core->tpl->addValue('MediaInfoURL', array('mediaInfoTpl', 'MediaInfoURL'));

$core->tpl->addValue('MediaInfoRelUrl', array('mediaInfoTpl', 'MediaInfoRelUrl'));
$core->tpl->addValue('MediaInfoClass', array('mediaInfoTpl', 'MediaInfoClass'));
$core->tpl->addValue('MediaInfoExposure', array('mediaInfoTpl', 'MediaInfoExposure'));
$core->tpl->addValue('MediaInfoAperture', array('mediaInfoTpl', 'MediaInfoAperture'));
$core->tpl->addValue('MediaInfoLens', array('mediaInfoTpl', 'MediaInfoLens'));
$core->tpl->addValue('MediaInfoIso', array('mediaInfoTpl', 'MediaInfoIso'));
$core->tpl->addValue('MediaInfoManufacturer', array('mediaInfoTpl', 'MediaInfoManufacturer'));
$core->tpl->addValue('MediaInfoModel', array('mediaInfoTpl', 'MediaInfoModel'));
$core->tpl->addValue('MediaInfoDateTime', array('mediaInfoTpl', 'MediaInfoDateTime'));
$core->tpl->addValue('MediaInfoPhoto', array('mediaInfoTpl', 'MediaInfoPhoto'));

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
			'rel_url' => 'public/' . $fi,
			'class_name' => '',
			'exposure' => '',
			'aperture' => '',
			'lens' => '',
			'iso' => '',
			'manufacturer' => '',
			'model' => '',
			'dateTime' => '',
			'has_exif' => false,
			'thumbnailUrl' => ''
		);
		if ( file_exists($mi['rel_url']) )
		{
			
			$infos = getimagesize($mi['rel_url']);
			$mi['class_name'] = $infos[1] > $infos[0] ? "Portrait" : "Landscape";
			$im = imageMeta::readMeta($mi['rel_url']);
			
			$path_parts = pathinfo($mi['rel_url']);
			$mi['thumbnailUrl'] = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_s.' . $path_parts['extension'];
			
			if (!empty($im['Exposure']))
			{
				$mi['has_exif'] = true;
				$mi['exposure'] = $im['Exposure'];
			}
			if (!empty($im['FNumber']))
			{
				$mi['has_exif'] = true;
				$fl = sscanf($im['FNumber'],'%d/%d');
				$mi['aperture'] = $fl ? $fl[0]/$fl[1].'' : $im['FNumber'];
			}
			if (!empty($im['FocalLength']))
			{
				$mi['has_exif'] = true;
				$fl = sscanf($im['FocalLength'],'%d/%d');
				$mi['lens'] = $fl ? $fl[0]/$fl[1].'' : $im['FocalLength'];
			}
			if (!empty($im['ISOSpeedRatings']))
			{
				$mi['has_exif'] = true;
				$mi['iso'] = $im['ISOSpeedRatings'];
			}
			if (isset($im['Make']))
			{
				$mi['has_exif'] = true;
				$mi['manufacturer'] = $im['Make'];
			}
			if (isset($im['Model']))
			{
				$mi['has_exif'] = true;
				$mi['model'] = $im['Model'];
			}
			if (!empty($im['DateTimeOriginal']))
			{
				$mi['dateTime'] = $im['DateTimeOriginal'];
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

        if (isset($attr['is_image'])) {
            $sign = (boolean) $attr['is_image'] ? '' : '!';
            $if[] = $sign . '$attach_f->media_image';
        }

        if (isset($attr['has_thumb'])) {
            $sign = (boolean) $attr['has_thumb'] ? '' : '!';
            $if[] = $sign . 'isset($attach_f->media_thumb[\'sq\'])';
        }

        if (isset($attr['is_mp3'])) {
            $sign = (boolean) $attr['is_mp3'] ? '==' : '!=';
            $if[] = '$attach_f->type ' . $sign . ' "audio/mpeg3"';
        }
		
        if (isset($attr['has_exif'])) {
            $sign = (boolean) $attr['has_exif'] ? '' : '!';
            $if[] = $sign . '$m[\'has_exif\']';
        }

        if (isset($attr['is_flv'])) {
            $sign = (boolean) $attr['is_flv'] ? '==' : '!=';
            $if[] = '$attach_f->type ' . $sign . ' "video/x-flv"';
        }

        if (isset($attr['is_audio'])) {
            $sign = (boolean) $attr['is_audio'] ? '==' : '!=';
            $if[] = '$attach_f->type_prefix ' . $sign . ' "audio"';
        }

        if (isset($attr['is_video'])) {
            $sign = (boolean) $attr['is_video'] ? '==' : '!=';
            $if[] = '$attach_f->type_prefix ' . $sign . ' "video"';
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
        return '<?php echo ' . sprintf($f, '$attach_f->type') . '; ?>';
    }

    public static function MediaInfoType($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$attach_f->media_type') . '; ?>';
    }

    public static function MediaInfoFileName($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$attach_f->basename') . '; ?>';
    }

    public static function MediaInfoSize($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        if (!empty($attr['full'])) {
            return '<?php echo ' . sprintf($f, '$attach_f->size') . '; ?>';
        }
        return '<?php echo ' . sprintf($f, 'files::size($attach_f->size)') . '; ?>';
    }

     public static function MediaInfoThumbnailURL($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'thumbnailUrl\']') . '; ?>';
    }

    public static function MediaInfoURL($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$attach_f->file_url') . '; ?>';
    }
	
    public static function MediaInfoRelUrl($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'rel_url\']') . '; ?>';
    }
	
   public static function MediaInfoClass($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'class_name\']') . '; ?>';
    }
	
     public static function MediaInfoExposure($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'exposure');
    }

    public static function MediaInfoAperture($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'aperture');
    }

    public static function MediaInfoLens($attr)
    {
	   return mediaInfoTpl::MediaInfoFormat($attr, 'lens');
    }

    public static function MediaInfoIso($attr)
    {
 	   return mediaInfoTpl::MediaInfoFormat($attr, 'iso');
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
	
	public static function MediaInfoPhoto($attr)
	{
 		$format = '%s %s %s %s %s';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
       return '<?php echo ' . sprintf($f, 'sprintf(\'' . $format . '\',$m[\'model\'],$m[\'lens\'],$m[\'aperture\'],$m[\'exposure\'],$m[\'iso\'] ) ') . '; ?>';
	}
	
    public static function MediaInfoManufacturer($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'manufacturer\']') . '; ?>';
    }

    public static function MediaInfoModel($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, '$m[\'model\']') . '; ?>';
    }

    public static function MediaInfoDateTime($attr)
    {
		$format = '%A %e %B %Y %H:%M:%S';
        if (isset($attr['format'])) {
			$format = addslashes($attr['format']);
		}
		$f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'dt::dt2str(\'' . $format . '\', $m[\'dateTime\'])' ) . '; ?>';
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
