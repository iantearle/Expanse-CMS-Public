<?php
header('Content-type: application/json');

require('../../../funcs/admin.php');

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}
if(LOGGED_IN) {
	$dir = UPLOADS.'editor/files/';
	$dirUrl = UPLOADS_DIR.'editor/files/';
	if(!is_dir($dir)) {
	    mkdir($dir);
	}
	move_uploaded_file($_FILES['file']['tmp_name'], $dir.$_FILES['file']['name']);

	$array = array(
	    'filelink' => $dirUrl.$_FILES['file']['name'],
	    'filename' => $_FILES['file']['name']
	);

	echo stripslashes(json_encode($array));
}
