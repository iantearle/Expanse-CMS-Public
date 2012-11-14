<?php
/********* Expanse ***********/
define('IS_FRONTEND', false);
define('IS_BACKEND', true);
require(dirname(__FILE__).'/common.functions.php');
turnOffGlobals();
function is_home(){
	return LOGGED_IN == true && empty($_GET);
}
/*
------------------------------------------------------------
Dont Cache
============================================================
*/
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
// HTTP/1.0
header("Pragma: no-cache");
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

$pagetitle = '';
$output = (!empty($output)) ? $output : '';
$enteredusername = '';

/*
------------------------------------------------------------
Get required files
============================================================
*/
$config_file = realpath(dirname(__FILE__).'/../').'/config.php';
if (file_exists($config_file)) {
	require($config_file);
}
require(dirname(__FILE__) . '/functions.php');
require(dirname(__FILE__) . '/output.class.php');
require(dirname(__FILE__) . '/xajax.inc.php');
require(dirname(__FILE__) . '/ozone.php');
require(dirname(__FILE__) . '/ozone.default.php');
require(dirname(__FILE__) . '/pclzip.lib.php');
require_once(dirname(__FILE__) . '/database.class.php');
require_once(dirname(__FILE__) . '/expanse.class.php');
require(dirname(__FILE__).'/session.class.php');
require(dirname(__FILE__) . '/auth.class.php');
require(dirname(__FILE__) . '/snoopy.class.php');
require(dirname(__FILE__).'/common.vars.php');
require(dirname(__FILE__) . '/varsdef.php');
/*
------------------------------------------------------------
Check for the installation page
============================================================
*/
installFile('install.php');
/*   Global object instances (active-record)  //-------------------------------*/
$items = new Expanse('items');
$customfields = new Expanse('customfields');
$sections = new Expanse('sections');
$users = new Expanse('users');
$images = new Expanse('images');
$prefs = new Expanse('prefs');
/*--//--*/
$outmess = new outputMessages;

/*
------------------------------------------------------------
Auth Logic
============================================================
*/
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
	$auth->logout();
	$headerID = $userip;
}
if (isset($_POST['login'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		printOut(FAILURE, 'Please enter a username and/or a password');
	} else {
		$auth->login();
	}
}
if (isset($_POST['get_password'])) {
	$auth->retrievePassword();
}
if ((ACTION == 'forgot') && isset($_GET['reset_key'])) {
	$auth->resetPassword();
}
define('LOGGED_IN', $auth->isLoggedIn());
define('IS_AUTHORIZED', $auth->Authorized);
define('DISPLAY_NAME', $auth->DisplayName);
define('USER_NAME', $auth->Username);
$headerID = (isset($_SESSION['displayname'])) ? $_SESSION['displayname'] : $_SERVER['REMOTE_ADDR'];
define('HEADER_ID', $headerID);
$menu = '';
if(LOGGED_IN){
	$menu = $_SESSION['menu'] = $auth->createMenu();
}
if(isset($auth) && is_object($auth) && 0){
	if(isset($_SESSION['menu']) && !empty($_SESSION['menu'])) {
		$authlevels = count($auth->Permissions);
		$sessionlevels = isset($_SESSION['permissions']) ? count($_SESSION['permissions']) : 0;
		if($authlevels == $sessionlevels){
			$menu = $_SESSION['menu'] != '<ul></ul>' ? $_SESSION['menu'] : $auth->createMenu();
		} else {
			$menu = $_SESSION['menu'] = $auth->createMenu();
			$_SESSION['permissions'] = $auth->Permissions;
		}

	} elseif (isset($_SESSION['username'])) {
		$menu = $_SESSION['menu'] = $auth->createMenu();
	}
}
if(LOGGED_IN && CAT == 'admin'){
	include(EXPANSEPATH.'/funcs/admin/users.php');
	include(EXPANSEPATH.'/funcs/admin/plugins.php');
	include(EXPANSEPATH.'/funcs/admin/menu_builder.php');
	include(EXPANSEPATH.'/funcs/admin/categories.php');
	include(EXPANSEPATH.'/funcs/admin/comments.php');
	include(EXPANSEPATH.'/funcs/admin/dbcnx.php');
	include(EXPANSEPATH.'/funcs/admin/theme_editor.php');
	include(EXPANSEPATH.'/funcs/admin/prefs.php');
}

/*
------------------------------------------------------------
(x)AJAX Functionality
============================================================
*/
  if (class_exists('xajax')) {
      $xajax = new xajax();
      $xajax->registerFunction("returnDate");
      $xajax->registerFunction("returnTime");
      $xajax->registerFunction("saveCrop");
      $xajax->registerFunction("updateOrder");
	  $xajax->registerFunction("updateMenuOrder");
	  $xajax->registerFunction("update_option");
      function returnDate($format, $offset = 0)
      {
          $format = str_replace('\\\\', '\\', $format);
          $zone = (3600 * $offset) + date('Z');
          $date = gmdate($format, time() + $zone);
          $objResponse = new xajaxResponse();
          $objResponse->addAssign("retdateformat", "value", $date);
          return $objResponse->getXML();
      }
      function returnTime($format, $offset = 0)
      {
          $format = str_replace('\\\\', '\\', $format);
          $zone = (3600 * $offset) + date('Z');
          $date = gmdate($format, time() + $zone);
          $objResponse = new xajaxResponse();
          $objResponse->addAssign("rettimeformat", "value", $date);
          return $objResponse->getXML();
      }
      function saveCrop($f)
      {
          $f = (object)$f;
          $item_id = ITEM_ID;
          $expanse = new Expanse('items');
          $expanse->Get($item_id);
          $expanse->crop_x = $f->crop_x;
          $expanse->crop_y = $f->crop_y;
          $expanse->thumb_w = $f->thumb_w;
          $expanse->thumb_h = $f->thumb_h;
          $expanse->thumb_max = $f->thumb_max;
          $expanse->use_default_thumbsize = isset($f->use_default_thumbsize) ? $f->use_default_thumbsize : 0;
          $expanse->Save();
          $objResponse = new xajaxResponse();
          $objResponse->addAppend("scaleImg", "src", "&time=" . time());
          $objResponse->addAppend("thumbNailThumb", "src", "&time=" . time());
          return $objResponse->getXML();
      }
	function updateOrder($arg) {
		global $Database;
		$items = new Expanse('items');
		$errors = array();
		parse_str($arg);
		ob_start();
		$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
		$multiplier = EDIT_LIMIT != 0 ? (EDIT_LIMIT * $page - EDIT_LIMIT) : 0;
		foreach($item as $k => $v) {
			(int) $k++;
			$k += $multiplier;
			if(!is_numeric($v)) {
				return;
			}
			$items->Get($v);
			$items->order_rank = $k;
			if($items->Save()) {
				$errors[] = 1;
			}
		}
		$arg = ob_get_contents();
		ob_end_clean();
		$arg = (!empty($errors)) ? sprintf(SUCCESS, L_REORDER_SUCCESS) : sprintf(FAILURE, L_REORDER_FAILURE);
		$objResponse = new xajaxResponse();

		$script = "var opacity = new fx.Opacity($('responseText') , {duration: 800});
		opacity.setOpacity(0);
		opacity.custom(0,1);
		";
		$objResponse->addAssign("responseText", "innerHTML", $arg);
		$objResponse->addScript($script);

		return $objResponse->getXML();
	}
	  function updateMenuOrder($arg, $table)
      {
          global $Database;
          $items = get_dao($table);
          $errors = array();
          parse_str($arg);
          ob_start();
		  $is_items = isset($items->menu_order) ? true : false;
		  $menu_order = $is_items ? 'menu_order=0' : 'public=0, order_rank = 0';
		  $Database->Query("UPDATE ".PREFIX."$table SET {$menu_order}");
		  $final = array();
		  $keepMenu = isset($keepMenu) ? $keepMenu : array();
          foreach ($keepMenu as $k => $v) {
              if (!is_numeric($v)) {
                  continue;
              }
              $items->Get($v);

			  if(isset($items->public)){
			  $items->public = 1;
			  $items->order_rank = $k;
			  $final[] = $items->sectionname;
			  } elseif(isset($items->menu_order)){
			  $items->menu_order = $k;
			  $final[] = $items->title;
			  }
              if (!$items->Save()) {
                  $errors[$v] = 1;
              }
          }
          $arg = ob_get_contents();
          ob_end_clean();
		 $debug = '<pre>'.print_r($final, 1).date('F d Y, g:i:s a').'</pre>';
		  $debug = '';
          $arg = (empty($errors)) ? sprintf(SUCCESS, L_REORDER_MENU_SUCCESS).$debug : sprintf(FAILURE, L_REORDER_MENU_FAILURE).$debug;
          $objResponse = new xajaxResponse();

			$script = "
			if(getCookie('already_shown') != 'yes'){

			setCookie('already_shown', 'yes');
			//if($('responseText').innerHTML != ''){
			var opacity = new fx.Opacity($('responseText') , {duration: 800});
			opacity.setOpacity(0);
			opacity.custom(0,1);
			//}
			} else {
			deleteCookie('already_shown');
			}
			";
		  $objResponse->addAssign("responseText", "innerHTML", $arg);
          $objResponse->addScript($script);
          return $objResponse->getXML();
  }
  function update_option($name, $value){
  setOption($name, $value);
  $objResponse = new xajaxResponse();
  return $objResponse->getXML();
  }
      $xajax->processRequests();
  }
  ?>