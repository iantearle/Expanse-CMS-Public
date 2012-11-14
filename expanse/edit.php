<?php
/********* Expanse ***********/
//Must be included at the top of all included files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}

$pagetitle = "Editing ";
if($module_exists) {
	if(empty($errors)) {
		if((!empty($item_id) && !empty($items->id)) || (empty($item_id) && !empty($itemsList))){
			include("$modules_dir/$cat_type/view.php");
		}
	}
} else {
	echo '<p>'. L_NOTHING_HERE . '</p></form>';
}

