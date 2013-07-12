<?php
require('../../../funcs/admin.php');

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}
if(LOGGED_IN) {

	$uploaddir = UPLOADS.'editor/';
	if(!is_dir($uploaddir)) {
	    mkdir($uploaddir);
	}

	$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

	if ($_FILES['file']['type'] == 'image/png'
	|| $_FILES['file']['type'] == 'image/jpg'
	|| $_FILES['file']['type'] == 'image/gif'
	|| $_FILES['file']['type'] == 'image/jpeg'
	|| $_FILES['file']['type'] == 'image/pjpeg') {
		$file = checkFiles($_FILES, $uploaddir, true, '/^asset\d/i');

		if(!empty($xtra_img_uploads['errors'])) {
			foreach($xtra_img_uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}

			// Send a message if there are errors
			$messages = "<ul>".implode('', $messages)."</ul>";
			printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));

		} else {

			$array = array(
		        'filelink' => UPLOADS_DIR.'editor/'.$file['files']['file']['name']
		    );

			echo stripslashes(json_encode($array));
		}
	}
}
