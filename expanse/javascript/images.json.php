<?php

require('../funcs/admin.php');

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}
if(LOGGED_IN) {

	/*   Do not edit below this line   //-------------------------------*/
	$files = array();
	$dims = array(1,2);
	$dir = opendir(UPLOADS);
	$i = 1;
	while($file = readdir($dir)) {
	    if($file == '.' || $file == '..') {
	        continue;
	    }

	    if(getimagesize(UPLOADS.'/'.$file)) {
			$dims = getimagesize(UPLOADS.'/'.$file);
		}

	    $file = array(
	    	'name' => $file,
	    	'size' => filesize(UPLOADS . $file),
	    	'url' => UPLOADS_DIR .'/'. $file,
	    	'delete_type' => 'DELETE',
	    	'width' => $dims[0],
	    	'height' => $dims[1]
	    );


	    $files[] = $file;
	}

	header('Content-type: application/json');
	echo json_encode($files);

}
