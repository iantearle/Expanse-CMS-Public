<?php
require('../funcs/admin.php');

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}
if(LOGGED_IN) {
	$uploaddir = UPLOADS;

	$xtra_img_uploads = checkFiles($_FILES, $uploaddir, true, '/^asset\d/i');

	if(!empty($uploads['errors']) || !empty($xtra_img_uploads['errors'])) {
		foreach($uploads['errors'] as $val) {
			foreach($val as $v) {
				$messages[] = "<li>$v</li>";
			}
		}
		foreach($xtra_img_uploads['errors'] as $val) {
			foreach($val as $v) {
				$messages[] = "<li>$v</li>";
			}
		}

		// Send a message if there are errors
		$messages = "<ul>".implode('', $messages)."</ul>";
		printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
	} else {
//		print_r($xtra_img_uploads['files']);
		$files = array();
	    $file = array(
	    	'name' => $xtra_img_uploads['files']['asset']['name'],
	    	'size' => $xtra_img_uploads['files']['asset']['size'],
	    	'url' => UPLOADS_DIR .'/'. $xtra_img_uploads['files']['asset']['name'],
	    	'delete_type' => 'DELETE',
	    	'width' => $xtra_img_uploads['files']['asset']['width'],
	    	'height' => $xtra_img_uploads['files']['asset']['height']
	    );


	    $files[] = $file;

		echo json_encode($files);

	}

}