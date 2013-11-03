<?php

    if (!defined('SITE_ROOT')) define('SITE_ROOT', Application::getSitePath());

    function retainAspectRation($w, $h, $neww, $newh) {

        if ($w > $neww) $aspect = $w / $neww;
        else $aspect = $h / $newh;

        if ($h / $aspect > $newh) $aspect = $h / $newh;

        return array(round($w / $aspect), round($h / $aspect));
    }

    function resize($ext = 1, $sourceFile, $outFile, $width, $height, $watermark = false) {
        $info = getimagesize($sourceFile);
        if ($info[2] == IMAGETYPE_JPEG) {
            if (function_exists('imagecreatefromjpeg')) $img = imagecreatefromjpeg($sourceFile);
            else return false;
        } elseif ($info[2] == IMAGETYPE_GIF) $img = imagecreatefromgif($sourceFile);
        elseif ($info[2] == IMAGETYPE_PNG) $img = imagecreatefrompng($sourceFile);
        else return false;

        if ($info[0] == $width && $info[1] == $height && !$watermark) {
            copy($sourceFile, $outFile);
            return true;
        }

        if ($info[0] > $width || $info[1] > $height) $dims = retainAspectRation($info[0], $info[1], $width, $height);
        else $dims = array($info[0], $info[1]);

        $ne = imagecreatetruecolor($dims[0], $dims[1]);

        imagecopyresampled($ne, $img, 0, 0, 0, 0, $dims[0], $dims[1], $info[0], $info[1]);

        if ($watermark) $ne = createWatermark($ne);

        switch ($ext) {
        case 1:
            $func = 'imagejpeg';
            break;
        case 2:
            $func = 'imagegif';
            break;
        case 3:
            $func = 'imagepng';
            break;
        }

        $res = function_exists($func) ? call_user_func($func, $ne, $outFile) : false;

        imagedestroy($ne);
        return $res;
    }

    function createWatermark( $imgObj )
    {
        $width = imagesx( $imgObj );
        $height = imagesy( $imgObj );

        $classWatermark = Application :: getResourceClass( APP_RESOURCE_TYPE_OBJECT, 'watermark' );
        $watermark = call_user_func( array( $classWatermark, 'getWatermark' ), $width, $height );
        
        if( $watermark )
        	return $watermark->watermarkImage( $imgObj );
        elseif( defined( 'TEXT_WATERMARK' ) )
        {
        	$w = Application :: getEntityInstance( 'watermark' );
        	return $w->watermarkText( $imgObj, TEXT_WATERMARK );
        }
		else return $imgObj;
    }

    function watermarkText($main_img_obj) {
        $text = " www.Kauai.com ";
        $font = WATERMARK_FONT;
        $r = 249;
        $g = 249;
        $b = 249;
        $alpha_level = 70; // 0-128
        $length = strlen($text);

        $width = imagesx($main_img_obj);
        $height = imagesy($main_img_obj);

        if ($height > $width) {
            $angle = 90;
            $width_tmp = $height;
        } else {
            $angle = 0;
            $width_tmp = $width;
        }

        $margin = ceil($width_tmp / $length);
        $font_size = ceil(($width_tmp - $margin * 2) / $length);

        $box = imagettfbbox($font_size, 0, $font, $text);
        while ($box[2] < $width_tmp - $margin * 2) {
            $font_size++;
            $box = imagettfbbox($font_size, 0, $font, $text);
        }

        if ($height > $width) {
            $width_block_text = abs($box[5]) - abs($box[1]);
            $height_block_text = abs($box[2]) - abs($box[0]);
            $x = ceil(($width - $width_block_text + $width_block_text / 2));
            $y = ceil(($height - $height_block_text) / 2 + $height_block_text + $margin);
        } else {
            $width_block_text = abs($box[2]) - abs($box[0]);
            $height_block_text = abs($box[5]) - abs($box[1]);
            $x = ceil(($width - $width_block_text) / 2);
            $y = ceil(($height - $height_block_text));
        }
        $c = imagecolorallocatealpha($main_img_obj, $r, $g, $b, $alpha_level);
        imagettftext($main_img_obj, $font_size, $angle, $x, $y, $c, $font, $text);

        return $main_img_obj;
    }

    function uploadFile($resource, $format = 'image', $objectname, &$oldobject, $field_name, $sub_dir = '', $watermark = true, $renameTo = '' ) {
        $errors = array();
        global $photo_size;

        if ($format == 'image') {
            $valid_types1 = array('jpg', 'jpeg', 'JPG');
            $valid_types2 = array('gif', 'GIF');
            $valid_types3 = array('png', 'PNG');

            if (is_uploaded_file(@$resource['tmp_name']) || file_exists(@$resource['tmp_name'])) {
                $filename = $resource['tmp_name'];
                $acs = 0;
                $ext = substr($resource['name'], 1 + strrpos($resource['name'], '.'));
                if (in_array($ext, $valid_types1)) $acs = 1;
                if (in_array($ext, $valid_types2)) $acs = 2;
                if (in_array($ext, $valid_types3)) $acs = 3;

                if ($acs) {
					$send = $resource[ 'name' ];
					$name = $renameTo
						? $renameTo
						: getFileName($objectname, $sub_dir, $ext);

					$objectClassName = Application :: getResourceClass( APP_RESOURCE_TYPE_OBJECT, $objectname );
					$unique = method_exists( $objectClassName, 'checkUniqueImgName' )
						? call_user_func( array( $objectClassName, 'checkUniqueImgName' ), "{$name}.{$ext}" )
						: true;
                		
					if( !$unique )
					{
						$errors[] = "Image {$name}.{$ext} already exists";
						return $errors;
					}
                    	
                   $uploaddir = UPLOAD_PHOTOS.'tmp/';

                    if (!is_dir($uploaddir)) mkdir($uploaddir, 0777, true);

                    $object_dir = $sub_dir ? "{$objectname}/{$sub_dir}/"
                    : "{$objectname}/";

                    $uploaddirreal = UPLOAD_PHOTOS."{$object_dir}/";
                    if (!is_dir($uploaddirreal)) mkdir($uploaddirreal, 0777, true);

                    $uploaddirrealbig = UPLOAD_PHOTOS."{$object_dir}/big/";
                    if (!is_dir($uploaddirrealbig)) mkdir($uploaddirrealbig, 0777, true);

                    $uploaddirreallarge = UPLOAD_PHOTOS."{$object_dir}/large/";
                    if (!is_dir($uploaddirreallarge) && isset($photo_size[$objectname]['large_h'])) mkdir($uploaddirreallarge, 0777, true);

                    $uploaddirrealmedium = UPLOAD_PHOTOS."{$object_dir}/medium/";
                    if (!is_dir($uploaddirrealmedium)) mkdir($uploaddirrealmedium, 0777, true);

                    $uploaddirrealsuper = UPLOAD_PHOTOS."{$object_dir}/super/";
                    if (!is_dir($uploaddirrealsuper)) mkdir($uploaddirrealsuper, 0777, true);

                    $uploadfile = $uploaddir.$send;

                    if (@move_uploaded_file($filename, $uploadfile) || copy($filename, $uploadfile)) {
                        $photo = $oldobject->$field_name;
                        if ($photo) {
                            if (is_file($uploaddirreal.$photo)) unlink($uploaddirreal.$photo);
                            if (is_file($uploaddirrealbig.$photo)) unlink($uploaddirrealbig.$photo);
                            if (is_file($uploaddirreallarge.$photo)) unlink($uploaddirreallarge.$photo);
                            if (is_file($uploaddirrealmedium.$photo)) unlink($uploaddirrealmedium.$photo);
                            if (is_file($uploaddirrealsuper.$photo)) unlink($uploaddirrealsuper.$photo);
                        }

                        rename($uploadfile, "{$uploaddir}{$name}.{$ext}" );

                        if (@$photo_size[$objectname]['small_h'] && @$photo_size[$objectname]['small_w']) {
                            $result = resize($acs, "{$uploaddir}{$name}.{$ext}", "{$uploaddirreal}{$name}.{$ext}", $photo_size[$objectname]['small_w'], $photo_size[$objectname]['small_h']);
                            if (is_array($result)) foreach ($result as $r) $errors[] = $r;
                        }

                        if (@$photo_size[$objectname]['big_h'] && @$photo_size[$objectname]['big_w']) {
                            $result = resize($acs, "{$uploaddir}{$name}.{$ext}", "{$uploaddirrealbig}{$name}.{$ext}", $photo_size[$objectname]['big_w'], $photo_size[$objectname]['big_h'], $watermark);
                            if (is_array($result)) foreach ($result as $r) $errors[] = $r;
                        }

                        if (@$photo_size[$objectname]['large_h'] && @$photo_size[$objectname]['large_w']) {
                            $result = resize($acs, "{$uploaddir}{$name}.{$ext}", "{$uploaddirreallarge}{$name}.{$ext}", $photo_size[$objectname]['large_w'], $photo_size[$objectname]['large_h'], $watermark);
                            if (is_array($result)) foreach ($result as $r) $errors[] = $r;
                        }

                        if (@$photo_size[$objectname]['medium_h'] && @$photo_size[$objectname]['medium_w']) {
                            $result = resize($acs, "{$uploaddir}{$name}.{$ext}", "{$uploaddirrealmedium}{$name}.{$ext}", $photo_size[$objectname]['medium_w'], $photo_size[$objectname]['medium_h'], $watermark);
                            if (is_array($result)) foreach ($result as $r) $errors[] = $r;
                        }

                        if (@$photo_size[$objectname]['super_h'] && @$photo_size[$objectname]['super_w']) {
                            $result = resize($acs, "{$uploaddir}{$name}.{$ext}", "{$uploaddirrealsuper}{$name}.{$ext}", $photo_size[$objectname]['super_w'], $photo_size[$objectname]['super_h'], $watermark);
                            if (is_array($result)) foreach ($result as $r) $errors[] = $r;
                        }

                        unlink("{$uploaddir}{$name}.{$ext}" );

                        $oldobject->$field_name = "{$name}.{$ext}";
                    } else $errors[] = 'Photo is not upload!';
                } else $errors[] = "Photo Invalid file format {$ext}!";
            }
        } elseif ($format == 'pdf') {
            $valid_types1 = array('pdf', 'PDF');
            if (is_uploaded_file(@$resource['tmp_name'])) {
                $filename = $resource['tmp_name'];
                $acs = 0;
                $ext = substr($resource['name'], 1 + strrpos($resource['name'], '.'));
                if (in_array($ext, $valid_types1)) $acs = 1;
                if ($acs > 0) {
                    $send = $resource['name'];

                    $uploaddir = UPLOAD_PHOTOS."{$objectname}/pdf/";
                    if (!is_dir($uploaddir)) mkdir($uploaddir, 0777, true);

                    $uploadfile = $uploaddir.$send;
                    if (@move_uploaded_file($filename, $uploadfile)) {
                        $file = $oldobject->$field_name;
                        if ($file != $send) {
                            if (is_file($uploaddir.$file)) unlink($uploaddir.$file);
                        }

                        $oldobject->$field_name = $send;
                    } else $errors[] = 'File is not upload!';
                } else $errors[] = "File Invalid file format {$ext}!";
            }
        }
        return $errors;
    }

    function getFileName($object_table, $object_id, $ext) {
        $default_name = mt_rand(5, 1000000000).time();

        if (!$object_id) return $default_name;

        $object = Application::getEntityInstance($object_table);
        if (!$object = $object->load($object_id)) return $default_name;

        if( method_exists( get_class( $object ), 'getPhotoAttributes' ) )
        {
        	$imgAttr = $object->getPhotoAttributes();
        	$imgBaseName = $imgAttr[ 'filename' ];
            if( !$imgBaseName )
            	return $default_name;
        }
        else
        	return $default_name;
        
        $imgBaseName = str_replace(' ', '-', strip_tags(strtolower($imgBaseName)));
        $imgBaseName = str_replace(array('"', "'", ',', '.', '|', '@', '!', '#', '$', '%', '^', '*', '(', ')', '+', '/', '\\'), '', $imgBaseName);
		$imgName = $imgBaseName; 
		
        $ext = $ext ? ".{$ext}" : '';
        $path = UPLOAD_PHOTOS."{$object_table}/{$object_id}/{$imgName}{$ext}";

        $counter = 1;
        while (is_file($path)) {
            $imgName = "{$imgBaseName}-{$counter}";
            $path = UPLOAD_PHOTOS."{$object_table}/{$object_id}/{$imgName}{$ext}";
            $counter++;
        }

        return $imgName;
    }

