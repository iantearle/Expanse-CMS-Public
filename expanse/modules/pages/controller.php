<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all def files.
if(!defined('EXPANSE')){
die('Sorry, but this file cannot be directly viewed.');
}
class Pages extends Module{
var $name = L_PAGE_NAME;
var $description = L_PAGE_DESCRIPTION;
var $Exclude = true;
//Inherit the rest of the category meta-data
/**/

function add()
{
    $outmess = $this->output;
    $catid = $this->cat_id;
    $items =& $this->items;
    //Process the files
	$uploaddir = UPLOADS;
  	$uploads = checkFiles($_FILES, $uploaddir, true, '/^additional_images\d/i');
	$xtra_img_uploads = checkFiles($_FILES, $uploaddir, false, array('img_main', 'img_thumb'));
    //Check for errors
    if (!empty($uploads['errors']) || !empty($xtra_img_uploads['errors'])) {
        foreach($uploads['errors'] as $val){
            foreach($val as $v){
                $messages[] = "<li>$v</li>";
            }
        }
		foreach($xtra_img_uploads['errors'] as $val){
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
        //Get class variables
		$object_vars = get_object_vars($items);
        //Loop through POST
        foreach($_POST as $ind=>$val){
            if (isset($object_vars[$ind])) {
                $items->{$ind} = trim($val);
            }
        }
        //Set individual fields
		$items->title = $items->title;
		$items->descr = $items->descr;
		$items->descr = str_replace(array('&nbsp;','<p></p>'), '', $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
        $items->created = dateTimeProcess();
        $items->pid = $items->cid = (isset($_POST['pid'])) ? $_POST['pid'] : 0;
        $items->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
		$items->type = 'static';
		//Add a subcat
		$items->cid = $this->addSubcat();
        //Save the info
        if ($items->SaveNew()) {
			$items = applyOzoneAction('item_add', $items);
			$this->manage_custom_fields($items);

			if(!empty($xtra_img_uploads['files'])){
				$images = new Expanse('images');
				$caption = isset($_POST['caption']) ? $_POST['caption'] : array();
				foreach($xtra_img_uploads['files'] as $xi => $xv){
					$images->image = $xv['name'];
					$images->width = isset($xv['width']) ?  $xv['width'] : '';
					$images->height = isset($xv['width']) ?  $xv['height'] : '';
					$images->caption = isset($caption[$xi]) ? trim(strip_tags($caption[$xi])) : '';
					$images->itemid = $items->id;
					$images->SaveNew();
				}
		}
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
    $outmess = $this->output;
	$catid = $this->cat_id;
	$item_id = $this->item_id;
	$items =& $this->items;
	$images = new Expanse('images');
	$uploaddir = UPLOADS;
  	$uploads = checkFiles($_FILES, $uploaddir, true, '/^additional_images\d/i');
	$xtra_img_uploads = checkFiles($_FILES, $uploaddir, false, array('img_main', 'img_thumb'));
    //Check for errors
    if (!empty($uploads['errors']) || !empty($xtra_img_uploads['errors'])) {
        foreach($uploads['errors'] as $val){
            foreach($val as $v){
                $messages[] = "<li>$v</li>";
            }
        }
		foreach($xtra_img_uploads['errors'] as $val){
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

		$items->Get($item_id);

        //Loop through POST
        foreach($_POST as $ind=>$val){
            if (array_key_exists($ind, get_object_vars($items))) {
                $items->{$ind} = trim($val);
            }
        }
        //Set individual fields
        if(!empty($xtra_img_uploads['files'])){

			$caption = isset($_POST['caption']) ? $_POST['caption'] : array();
			foreach($xtra_img_uploads['files'] as $xi => $xv){
				$images->image = $xv['name'];
				$images->width = isset($xv['width']) ?  $xv['width'] : '';
				$images->height = isset($xv['width']) ?  $xv['height'] : '';
				$images->caption = isset($caption[$xi]) ? trim(strip_tags($caption[$xi])) : '';
				$images->itemid = $item_id;
				$images->SaveNew();
			}
		}
		$delete_images = isset($_POST['delete_additional']) ? $_POST['delete_additional'] : array();
		foreach($delete_images as $di){
			$images->Get($di);
			unlink($uploaddir.'/'.$images->image);
			$images->Delete();
		}
		$items->created = dateTimeProcess($items->created);
        $items->pid = $items->cid = (isset($_POST['pid'])) ? $_POST['pid'] : 0;
        $items->dirtitle = set_dirtitle($items);
        $items->template = dirify(trim($_POST['template']));
		$items->type = 'static';
		$items->descr = str_replace(array('&nbsp;','<p></p>'), array(' ', ''), $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
		//Add a subcat
		$items->cid = $this->addSubcat();
        //Save the info
        if ($items->Save()) {
			$items = applyOzoneAction('item_edit', $items);
			$this->manage_custom_fields($items);

            //Move or copy
			$new_item =& $this->new_item;
			$new_home =& $this->new_home;
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
function get_single(){

					$items =& $this->items;
					$item_id = $this->item_id;
					$catid = $this->cat_id;
					$items->Get($item_id);
					if(empty($items->id)){
						printOut(FAILURE,sprintf(L_ENTRY_NOT_FOUND,$catid));
					}
					return $items;
}
function get_list(){

				$items =& $this->items;
				$item_id = $this->item_id;
				$catid = $this->cat_id;
				$itemsList =& $this->itemsList;
				$auth = $this->auth;

				$sortoption = getOption('sortcats');
				$ascending = getOption('sortdirection') == 'ASC' || $sortoption == 'order_rank' ? true : false;
				$conditions = array(array('pid', '=', 0), array('type', '=', 'static'));
				if(!($auth->SectionAdmin && $auth->Admin)) {
					$conditions[] = array('aid', '=', $auth->Id);
				}
				$itemsList = $items->GetList($conditions, $sortoption, $ascending);

				if(empty($itemsList)){
					printOut(FAILURE,sprintf(L_NO_ENTRIES,$catid));
				}
				return $itemsList;
}
function doSort(){}
}
 ?>
