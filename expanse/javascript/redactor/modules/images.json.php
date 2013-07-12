<?php

header('Content-type: application/json');

require('../../../funcs/admin.php');

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}
if(LOGGED_IN) {

	$imagesDir = UPLOADS.'editor/';
	if(!is_dir($imagesDir)) {
	    mkdir($imagesDir);
	}
	$images = glob($imagesDir . '*.{jpg,jpeg,png,gif,JPG}', GLOB_BRACE);
	foreach($images as $key => $image) {
		$info = pathinfo($image);
		$title = basename($image, '.'.$info['extension']);
		$title = substr($image, strpos($image, "_") + 1);

		$array = array(
			'thumb' => EXPANSE_URL . 'funcs/tn.lib.php?file_name=editor/' . $info['basename'] . '&amp;thumb=1',
			'image' => UPLOADS_DIR.'editor/'.$info['basename'],
			'title' => $title
	    );
	    $new_array[$key] = $array;

	}

	echo stripslashes(json_encode($new_array));
}
