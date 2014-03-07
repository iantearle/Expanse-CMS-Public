<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all module files.
if(!defined('EXPANSE')){ die('Sorry, but this file cannot be directly viewed.'); }

class ParseEvents extends Module {
	//This is the meta data for the category.
	var $name = 'Parse Events';
	var $description = L_EVENTS_DESCRIPTION;
	var $tableNameNew = 'parse';
	function add() {
		$envy = $_POST['related'];
		preg_match("^\[(.*?)\]^", $envy, $relatedProperty);
		date_default_timezone_set('UTC');
		include_once('modules/parseevents/parse.php');
		$parse = new parseRestClient(array(
		    'appid' => 'ByVcWVLEb8aME7OZKACz55OKy5RG9FbG7qUsD8Cz',
		    'restkey' => 'bTSlWMrxbtZepC4U2iVyvFdCMW1UERUmY0ErcGaa'
		));
		$params = array(
			'className' => 'events',
			'object' => array(
					'title' => $_POST['title'],
					'event_date_start' => date('cZ',strtotime($_POST['event_date_start'] .' '.$_POST['event_time_start'])),
					'event_date_end' => date('cZ',strtotime($_POST['event_date_end'] .' '.$_POST['event_time_end'])),
					'url' => $_POST['url'],
					'descr' => $_POST['descr'],
					'related' => $relatedProperty[1],
					'online' => $_POST['online']
				)
			);
		$request = $parse->create($params);

		$objId = json_decode($request);

		//Declare method vars
		$parseEvent = new Expanse('parse');
		$items =& $this->items;
		$cat_id = $this->cat_id;
		$item_id = $this->item_id;
		$itemvars = get_object_vars($parseEvent);
		$output = $this->output;
		//Loop over post
		foreach($_POST as $ind=>$val) {
			if(isset($itemvars[$ind])) {
				if(is_array($val)) {
					foreach($val as $k => $v) {
						$val[$k] = trim($v);
						if(empty($v)){
							unset($val[$k]);
						}
					}
					$parseEvent->{$ind} = !empty($val) ? serialize($val) : '';
				} else {
					$parseEvent->{$ind} = trim($val);
				}
			}
		}
		$parseEvent->event_date = strtotime($parseEvent->event_date_start);
		$parseEvent->event_date_start = strtotime($_POST['event_date_start'] .' '.$_POST['event_time_start']);
		$parseEvent->event_date_end = strtotime($_POST['event_date_end'] .' '.$_POST['event_time_end']);
		$parseEvent->event_time_start = strtotime($parseEvent->event_time_start);
		$parseEvent->event_time_end = strtotime($parseEvent->event_time_end);
		if(isset($relatedProperty[1])) {
		$parseEvent->related = $relatedProperty[1];
		}
		$parseEvent->objectId = $objId->{'objectId'};
		$parseEvent->created = dateTimeProcess();
		$parseEvent->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $cat_id;
		$parseEvent->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
		$items->descr = str_replace(array('&nbsp;','<p></p>'), '', $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
		//Add a subcat
		$parseEvent->cid = $this->addSubcat();
		if($parseEvent->SaveNew()) {
			$parseEvent = applyOzoneAction('item_add', $parseEvent);
			$this->manage_custom_fields($parseEvent);
			//Move or copy
			$new_item =& $this->new_item;
			$new_home =& $this->new_home;
			$this->moveOrCopy($parseEvent);
			$output->printOut(SUCCESS,vsprintf(L_ADD_SUCCESS, array($parseEvent->title, $cat_id, $parseEvent->id)));
		} else {
			$output->printOut(FAILURE, vsprintf(L_ADD_FAILURE,array($parseEvent->title, mysqli_error())));
		}
	}

	function edit() {
		date_default_timezone_set('UTC');
		$parseEvent = new Expanse('parse');
		$items =& $this->items;
		$outmess = $this->output;
		$catid = $this->cat_id;
		$item_id = $this->item_id;
		$parseEvent->Get($item_id);

		include_once('modules/parseevents/parse.php');
		$parse = new parseRestClient(array(
		    'appid' => 'ByVcWVLEb8aME7OZKACz55OKy5RG9FbG7qUsD8Cz',
		    'restkey' => 'bTSlWMrxbtZepC4U2iVyvFdCMW1UERUmY0ErcGaa'
		));
		$params = array(
		    'className' => 'events',
		    'objectId' => $parseEvent->objectId,
		    'object' => array(
		        'title' => $_POST['title'],
				'event_date_start' => date('cZ',strtotime($_POST['event_date_start'] .' '.$_POST['event_time_start'])),
				'event_date_end' => date('cZ',strtotime($_POST['event_date_end'] .' '.$_POST['event_time_end'])),
				'url' => $_POST['url'],
				'descr' => $_POST['descr'],
				'online' => $_POST['online']
		    )
		);
		$request = $parse->update($params);
		$object_vars = get_object_vars($parseEvent);
		//Loop through POST
		foreach($_POST as $ind=>$val){
			if(isset($object_vars[$ind])) {
				if(is_array($val)) {
					foreach($val as $k => $v) {
						$val[$k] = trim($v);
						if(empty($v)) {
							unset($val[$k]);
						}
					}
					$parseEvent->{$ind} = !empty($val) ? serialize($val) : '';
				} else {
					$parseEvent->{$ind} = trim($val);
				}
			}
		}
		$parseEvent->event_date = strtotime($parseEvent->event_date_start);
		$parseEvent->event_date_start = strtotime($_POST['event_date_start'] .' '.$_POST['event_time_start']);
		$parseEvent->event_date_end = strtotime($_POST['event_date_end'] .' '.$_POST['event_time_end']);
		$parseEvent->event_time_start = strtotime($parseEvent->event_time_start);
		$parseEvent->event_time_end = strtotime($parseEvent->event_time_end);
		$parseEvent->created = dateTimeProcess($parseEvent->created);
		$parseEvent->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
//		$parseEvent->dirtitle = set_dirtitle($items);
		$parseEvent->use_default_thumbsize = (!empty($_POST['use_default_thumbsize'])) ? 1 : 0;
		$title = empty($parseEvent->title) ? L_NO_TEXT_IN_TITLE : $parseEvent->title;
		$items->descr = str_replace(array('&nbsp;','<p></p>'), '', $items->descr);
		$items->descr = htmlspecialchars_decode(htmlentities($items->descr, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES);
		//Add a subcat
		$parseEvent->cid = $this->addSubcat();
		//Save the info
		if($parseEvent->Save()) {
			$parseEvent = applyOzoneAction('item_edit', $parseEvent);
			printOut(SUCCESS,vsprintf(L_EDIT_SUCCESS, array($title, $catid, $parseEvent->id)));
			//Reset POST
			$_POST = array();
		} else {
			printOut(FAILURE,vsprintf(L_EDIT_FAILURE, array($title, mysqli_error())));
		}
	}

	function get_single() {
		$parseEvent = new Expanse('parse');
		$items =& $this->items;
		$item_id = $this->item_id;
		$catid = $this->cat_id;
		$parseEvent->Get($item_id);
		if(empty($parseEvent->id)){
			printOut(FAILURE,sprintf(L_ENTRY_NOT_FOUND,$catid));
		}
		return $parseEvent;
	}

	function get_list() {
		$parseEvent = new Expanse('parse');
		$items =& $this->items;
		$item_id = $this->item_id;
		$catid = $this->cat_id;
		$itemsList = $this->itemsList;
		$auth = $this->auth;

		$sortoption = 'event_date_start';
		$ascending = getOption('sortdirection') == 'DESC' || $sortoption == 'order_rank' ? true : false;
		if(!($auth->SectionAdmin && $auth->Admin)) {
			$conditions[] = array('aid', '=', $auth->Id);
		}
		$all = check_get_alphanum('all');
		if($all == 'yes') {
			$itemsList = $parseEvent->GetList(array(array('id','>',0)), 'event_date', $ascending);
		} else {
			$itemsList = $parseEvent->GetList(array(array('id','>',0), array('event_date_end', '>=', time())), 'event_date_start', $ascending);
		}
		if(empty($itemsList)){
			printOut(FAILURE,sprintf(L_NO_ENTRIES,$catid));
		}
		return $itemsList;
	}

}
?>