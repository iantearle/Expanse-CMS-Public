<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all def files.
if(!defined('EXPANSE')){
die('Sorry, but this file cannot be directly viewed.');
}
class Press extends Module{
//This is the meta data for the category.
var $name = L_PRESS_NAME;
var $description = L_PRESS_DESCRIPTION;
//Inherit the rest of the category meta-data
/**/


function add()
{
    $outmess = $this->output;
	$catid = $this->cat_id;
	$items =& $this->items;
    //Process the files
    $uploads = checkFiles($_FILES, UPLOADS, true);
    //Check for errors
    if (!empty($uploads['errors'])) {
        foreach($uploads['errors'] as $val){
            foreach($val as $v){
                $messages[] = "<li>$v</li>";
            }
        }
        //Send a message if there are errors
        $messages = "<ul>".implode('', $messages)."</ul>";
       printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
    }
    //Everything looks good
    else{

		$object_vars = get_object_vars($items);
        //Loop through POST
        foreach($_POST as $ind=>$val){
            if (isset($object_vars[$ind])) {
                $items->{$ind} = trim($val);
            }
        }
        //Set individual fields
        if (!empty($uploads['files'])) {
            $items->image = $uploads['files']['image']['name'];
            $items->width = $uploads['files']['image']['width'];
            $items->height = $uploads['files']['image']['height'];
        }
        $items->created = dateTimeProcess();
        $items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
		$items->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
		$items->descr = str_replace(array('&nbsp;','<p></p>'), '', $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
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
            printOut(FAILURE,vsprintf(L_ADD_FAILURE, array($items->title, mysqli_error())));
        }
    }
}

function edit()
{
    //Grab the global message object, and the global category id
    $outmess = $this->output;
	$catid = $this->cat_id;
	$item_id = $this->item_id;
	$items =& $this->items;

	$uploaddir = UPLOADS;
    //Process the files
    $uploads = checkFiles($_FILES, $uploaddir, true);
    //Check for errors
    if (!empty($uploads['errors'])) {
        foreach($uploads['errors'] as $val){
            foreach($val as $v){
                $messages[] = "<li>$v</li>";
            }
        }
        //Send a message if there are errors
        $messages = "<ul>".implode('', $messages)."</ul>";
       printOut(L_UPLOAD_FAILURE, $messages);
    }
    //Everything looks good
    else{
		$items->Get($item_id);
      	$object_vars = get_object_vars($items);
        //Loop through POST
        foreach($_POST as $ind=>$val){
            if (isset($object_vars[$ind])) {
                $items->{$ind} = trim($val);
            }
        }
        //Set individual fields
        if (!empty($uploads['files'])) {
			if($items->image != $uploads['files']['image']['name'] && file_exists($uploaddir.'/'.$uploads['files']['image']['name'])){
			if(!empty($items->image) && file_exists($uploaddir.'/'.$items->image)){
				unlink($uploaddir.'/'.$items->image);
				}
			}
            $items->image = $uploads['files']['image']['name'];
            $items->width = $uploads['files']['image']['width'];
            $items->height = $uploads['files']['image']['height'];
        }
		$items->title = $items->title;
		$items->descr = str_replace(array('&nbsp;','<p></p>'), '', $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
        $items->created = dateTimeProcess($items->created);
        $items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
		$items->dirtitle = set_dirtitle($items);
		//Add a subcat
		$items->cid = $this->addSubcat();
        //Save the info
        if ($items->Save()) {
			$items = applyOzoneAction('item_edit', $items);
			$this->manage_custom_fields($items);
            //Move or copy
			if(!$this->moveOrCopy($items)){
			printOut(SUCCESS,vsprintf(L_EDIT_SUCCESS, array($items->title, $catid, $items->id)));
			} else {
			printOut(SUCCESS,vsprintf(L_EDIT_MOVE_SUCCESS, array($new_item->title, $new_home, $new_item->id)));
			}
            //Reset POST
            $_POST = array();
        } else {
           printOut(FAILURE,vsprintf(L_EDIT_FAILURE, array($items->title, mysqli_error())));
        }
    }
}
}
 ?>

