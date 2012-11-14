<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all def files.
if(!defined('EXPANSE')) { die('Sorry, but this file cannot be directly viewed.'); }

class Links extends Module {
	//This is the meta data for the category.
	var $name = L_LINKS_NAME;
	var $description = L_LINKS_DESCRIPTION;
	//Inherit the rest of the category meta-data
	/**/
	function add() {
		$outmess = $this->output;
		$catid = $this->cat_id;
		$items =& $this->items;
		//Process the files
		$uploads = checkFiles($_FILES, UPLOADS, true);
		//Check for errors
		if(!empty($uploads['errors'])) {
			foreach($uploads['errors'] as $val) {
				foreach($val as $v){
					$messages[] = "<li>$v</li>";
				}
			}
			//Send a message if there are errors
			$messages = "<ul>".implode('', $messages)."</ul>";
			printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
		} else {
			//Get class variables
			$object_vars = get_object_vars($items);
			//Loop through POST
			foreach($_POST as $ind=>$val) {
				if(isset($object_vars[$ind])) {
					$items->{$ind} = trim($val);
				}
			}
			//Set individual fields
			if(!empty($uploads['files'])) {
				$items->image = $uploads['files']['image']['name'];
				$items->width = $uploads['files']['image']['width'];
				$items->height = $uploads['files']['image']['height'];
			}
			$items->title = html_entity_decode($items->title, ENT_QUOTES);
			$items->descr = html_entity_decode($items->descr, ENT_QUOTES);
			$items->url = (strpos(strtolower($items->url),'http://') === false && strpos(strtolower($items->url),'https://') === false) ? 'http://'.$items->url : $items->url;
			$items->created = dateTimeProcess();
			$items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
			$items->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
			//Add a subcat
			$items->cid = $this->addSubcat();
			//Save the info
			if ($items->SaveNew()) {
				$items = applyOzoneAction('item_add', $items);
				$this->manage_custom_fields($items);
				//Move or copy
				$new_item =& $this->new_item;
				$new_home =& $this->new_home;
				$this->moveOrCopy($items);
				printOut(SUCCESS,vsprintf(L_ADD_SUCCESS, array($items->title, $catid, $items->id)));
				//Reset POST
				$_POST = array();
			} else {
				printOut(FAILURE,vsprintf(L_ADD_FAILURE, array($items->title, mysql_error())));
			}
		}
	}

	function edit() {
		$outmess = $this->output;
		$catid = $this->cat_id;
		$item_id = $this->item_id;
		$items =& $this->items;
		$uploaddir = UPLOADS;
		//Process the files
		$uploads = checkFiles($_FILES, $uploaddir, true);
		//Check for errors
		if(!empty($uploads['errors'])) {
			foreach($uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}
			//Send a message if there are errors
			$messages = "<ul>".implode('', $messages)."</ul>";
			printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
		} else {
			//Get the current item
			$items->Get($item_id);
			if(empty($uploads['files']) && isset($_POST['remove_image'])) {
				deleteFile($items->image);
				$items->image = $items->width =  $items->height = '';
			}
			//Loop through POST
			$object_vars = get_object_vars($items);
			foreach($_POST as $ind=>$val) {
				if(isset($object_vars[$ind])) {
					$items->{$ind} = trim($val);
				}
			}
			//Set individual fields
			if(!empty($uploads['files'])) {
				if($items->image != $uploads['files']['image']['name'] && file_exists($uploaddir.'/'.$uploads['files']['image']['name'])) {
					if(!empty($items->image) && file_exists($uploaddir.'/'.$items->image)) {
					unlink($uploaddir.'/'.$items->image);
					}
				}
				$items->image = $uploads['files']['image']['name'];
				$items->width = $uploads['files']['image']['width'];
				$items->height = $uploads['files']['image']['height'];
			}
			$items->url = (strpos(strtolower($items->url),'http://') === false && strpos(strtolower($items->url),'https://') === false) ? 'http://'.$items->url : $items->url;
			$items->created = dateTimeProcess($items->created);
			$items->pid = (isset($_POST['pid']) && ctype_digit($_POST['pid'])) ? (int) $_POST['pid'] : $catid;
			$items->dirtitle = set_dirtitle($items);
			//Add a subcat
			$items->cid = $this->addSubcat();
			//Save the info
			if ($items->Save()) {
				$items = applyOzoneAction('item_edit', $items);
				$this->manage_custom_fields($items);
				//Move or copy
				if(!$this->moveOrCopy($items)) {
					printOut(SUCCESS,vsprintf(L_EDIT_SUCCESS, array($items->title, $catid, $items->id)));
				} else {
					printOut(SUCCESS,vsprintf(L_EDIT_MOVE_SUCCESS, array($new_item->title, $new_home, $new_item->id)));
				}
				//Reset POST
				$_POST = array();
			} else {
				printOut(FAILURE,vsprintf(L_EDIT_FAILURE, array($items->title, mysql_error())));
			}
		}
	}
}
