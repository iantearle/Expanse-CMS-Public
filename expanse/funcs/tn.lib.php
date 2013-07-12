<?php
/****************************************************************

                    `-+oyhdNMMMMMMMNdhyo/-`
                .+ymNNmys+:::....-::/oshmNNdy/.
             :smMmy/-``.-:-:-:----:-::--..-+hNNdo.
          .smMdo-`.:::.`               `.-::-`:smMd/`
        .yMNy- -::`                         `-::`:hMmo`
      `yMNo``:/`                               `-/--yMN+
     /mMy.`:-                                  ```./--dMd.
    sMN/ //`                                    `..`-/`sMN/
   yMm-`s.                                       `.-.`+-/NN+
  yMm--y. ```.-/ooyoooo/:.                        `---`/::NN/
 +MN:.h--/sdNNNNMMMNNNmmmhdoo+:.                  `.-::`/:+MN.
`NMs`hyhNNMMMMMMMMMMMNNNmhyso+syy/:-.`          `.-/+o++:. hMh
+MN.`:ssdmmmmmmmmmmmmhyyyo++:.``   `.-:::://:::::.```````  -MN-
mMy    ````````....`````````                         ````  `dMo
MM+            ````                                  ````   yMy
MM:                                                  ````   yMd
MM+                                                  ````   yMy
dMy                                                  ````  `dM+
+Mm.       ``-://++oo+///-``    ``-::/ooooyhhddddddmmm+yo. -MN-
`NM+ -/+s.`ommmmmmmmmmmmmmddhyhyo+++oosyhhdddmmmNNNNMddmh+ hMh
 /MN-oNmds``sdmmmmNNNNNmmmdNmmdddhhyyyyyhhdddmmmNNmmy-+:s`+MN.
  sMm-sNmd+`.ydmmNNNNNNmmmNNNmdhysso+oosyssssso/:--:`.-o`:NN/
   yMm-+Nmds..ymmmNNNNNmNNNNNmdhyso++//::--...```..``:+ /NN+
    sNN/-hmdh+-ommNNNNmNNNNNNmdhyso+//::--..````.` .+:`oMN/
     /mMy.+mmddhhmNNNmmNMNNNNmdyso+//::--..````` `++`-dMd.
      `yMN+./hNmmmmmmmmmNNNNmmhyso+//:--..``..`-//`-yMN/
        .yMNy--odNNNmmmmmNNNmdhyso+/::--..`.://-`:hMmo`
          .smMdo-.+ydNNmmddmmdysso+/::::////.`:smMd/`
             :smMmy+---/oysydhhyyyo/+/:-``-+hNNdo.
                .+yNMNmhs+/::....-::/oshmNNdy/.
                    .-+oyhdNMMMMMMMNdhyo/-`

Expanse - Content Management For Web Designers, By A Web Designer
			  Extended by Ian Tearle, @iantearle
		Started by Nate Cavanaugh and Jason Morrison

****************************************************************/

/*require(dirname(__FILE__) . '/admin.php');*/
require(realpath(dirname(__FILE__) .'/../config.php'));
require(dirname(__FILE__) . '/class.database.php');
require(dirname(__FILE__) . '/class.expanse.php');
require(dirname(__FILE__) . '/ozone.php');
require(dirname(__FILE__) . '/functions.php');
require(dirname(__FILE__) . '/common.vars.php');
$option = getAllOptions();
$maxwidth = (isset($_GET['dim'])) ? $_GET['dim'] : (!empty($option->thumbsize) ? $option->thumbsize : 100);
$maxheight = (isset($_GET['dim'])) ? $_GET['dim'] : (!empty($option->thumbsize) ? $option->thumbsize : 100);
$item_img = '';
$item_id = check_get_id('id');
$image_id = check_get_id('image_id');
$file_name = check_get_alphanum('file_name');
if(!empty($item_id)) {
	$items = new Expanse('items');
	$items->Get($item_id);
	$item_img = rawurlencode($items->image);
	$item_img = !empty($item_img) ? $item_img : ERROR_IMG;
	$item_img = (!empty($items->width) || ERROR_IMG == $item_img) ? $item_img : FILE_IMG;
	if(isset($_GET['thumb'])) {
		$item_img = (isset($_GET['manual'])) ? $items->thumbnail : $item_img;
	}
	$crop_x = $items->crop_x;
	$crop_y = $items->crop_y;
	$thumb_w = $items->thumb_w;
	$thumb_h = $items->thumb_h;
	$thumb_max = $items->thumb_max;
	if($thumb_w == 0 && $thumb_h == 0 && $thumb_max != 0) {
		$maxwidth = $maxheight = $thumb_max;
	}
	if(isset($_GET['dim'])) {
		$maxwidth = $maxheight = $thumb_max = (int) $_GET['dim'];
	} elseif($items->use_default_thumbsize == 1) {
		$maxwidth = $maxheight = $thumb_max = (!empty($option->thumbsize) ? $option->thumbsize : 100);
	}
} elseif(!empty($image_id)) {
	$images = new Expanse('images');
	$images->Get($image_id);
	$item_img = rawurlencode($images->image);
	$item_img = !empty($item_img) ? $item_img : ERROR_IMG;
	$img_width = trim($images->width);
	$item_img = (!empty($img_width)) ? $item_img : FILE_IMG;
	if(isset($_GET['dim']) && is_numeric($_GET['dim'])) {
		$maxwidth = $maxheight = $thumb_max = (int) $_GET['dim'];
	} else {
		$maxwidth = $maxheight = $thumb_max = $option->thumbsize;
	}
} elseif(!empty($file_name)) {
	$item_img = rawurlencode($file_name);
	$item_img = !empty($item_img) ? $item_img : ERROR_IMG;
	$dims = getimagesize(UPLOADS . $file_name);
	$img_width = trim($dims[0]);
	$item_img = (!empty($img_width)) ? $item_img : FILE_IMG;
	$crop_x = (isset($_GET['cropx'])) ? $_GET['cropx'] : 0;
	$crop_y = (isset($_GET['cropy'])) ? $_GET['cropy'] : 0;
	$thumb_w = (isset($_GET['thumbw'])) ? $_GET['thumbw'] : '';
	$thumb_h = (isset($_GET['thumbh'])) ? $_GET['thumbh'] : '';
	$thumb_max = (isset($_GET['max'])) ? $_GET['max'] : $option->thumbsize;
	if($thumb_w == 0 && $thumb_h == 0 && $thumb_max != 0) {
		$maxwidth = $maxheight = $thumb_max;
	}
	if(isset($_GET['dim']) && is_numeric($_GET['dim'])) {
		$maxwidth = $maxheight = $thumb_max = (int) $_GET['dim'];
	} else {
		$maxwidth = $maxheight = $thumb_max = $option->thumbsize;
	}
}
$uploaddir = (!in_array($item_img, array(ERROR_IMG, FILE_IMG))) ? UPLOADS : EXPANSEPATH . '/images';

//Constants
define('IMAGE_BASE', $uploaddir);
define('MAX_WIDTH', $maxwidth);
define('MAX_HEIGHT', $maxheight);

//Get image location
$image_file = (isset($_GET['pic'])) ? $_GET['pic'] : (!empty($item_id) || !empty($image_id) || !empty($file_name) ? $item_img : str_replace('..', '', $_SERVER['QUERY_STRING']));
$image_path = IMAGE_BASE . rawurldecode($image_file);

//Load image
$img = null;
$ext = strtolower($image_path);
$ext = strrchr($ext, '.');
$ext = ($ext !== false) ? str_replace('.','',$ext) : '';
if($ext == 'jpg' || $ext == 'jpeg') {
	$img = @imagecreatefromjpeg($image_path);
} elseif($ext == 'png') {
	$img = @imagecreatefrompng($image_path);

	//Only if your version of GD includes GIF support
} elseif($ext == 'gif') {
	$img = @imagecreatefromgif($image_path);
}

//If an image was successfully loaded, test the image for size
if($img) {

	//Get image size and scale ratio
	$width = imagesx($img);
	$height = imagesy($img);
	$scale = min(MAX_WIDTH / $width, MAX_HEIGHT / $height);

	//If the image is larger than the max shrink it
	if($scale < 1 || isset($_GET['ignore_scale'])) {
		$new_width = floor($scale * $width);
		$new_height = floor($scale * $height);

		//Create a new temporary image
		$tmp_img = imagecreatetruecolor($new_width, $new_height);

		//Copy and resize old image into new image
		if(isset($_GET['thumb']) && $_GET['thumb'] == 1 && !isset($_GET['manual'])) {
			$x = (isset($crop_x) && $crop_x != 0) ? $crop_x : 0;
			$y = (isset($crop_y) && $crop_y != 0) ? $crop_y : 0;
			$w = (isset($thumb_w) && $thumb_w != 0) ? $thumb_w : $new_width;
			$h = (isset($thumb_h) && $thumb_h != 0) ? $thumb_h : $new_height;
			$ow = (isset($thumb_w) && $thumb_w != 0) ? $thumb_w : $width;
			$oh = (isset($thumb_h) && $thumb_h != 0) ? $thumb_h : $height;
			$nw = (isset($thumb_max) && $thumb_max != 0) ? $thumb_max : $new_width;
			$nh = round(($thumb_h * $nw) / $ow);
			if($w && $h && $nw && $nh) {
				$tmp_img = imagecreatetruecolor($nw, $nh);
				imagecopyresampled($tmp_img, $img, 0, 0, $x, $y, $nw, $nh, $w, $h);
			} else {
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($tmp_img, $img, 0, 0, $x, $y, $new_width, $new_height, $width, $height);
			}
		} else {
			imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		}
		imagedestroy($img);
		$img = $tmp_img;
	}
}

//Create error image if necessary
if(!$img) {
	$img = $error_img = @imagecreatefrompng(EXPANSEPATH . '/images/' . ERROR_IMG);
	if(!$error_img) {
		$img = imagecreate(MAX_WIDTH, MAX_HEIGHT);
		imagecolorallocate($img, 255, 255, 255);
		$c2 = imagecolorallocate($img, 0, 0, 0);
		imageline($img, 0, 0, MAX_WIDTH, MAX_HEIGHT, $c2);
		imageline($img, MAX_WIDTH, 0, 0, MAX_HEIGHT, $c2);
	}
}

//Display the image
if($ext == 'jpg' || $ext == 'jpeg') {
	header('Content-type: image/jpeg');
} elseif($ext == 'png' || isset($error_img)) {
	header('Content-type: image/png');
} elseif($ext == 'gif') {
	header('Content-type: image/gif');
} else {
	header('Content-type: image/jpeg');
}

imagepng($img);
