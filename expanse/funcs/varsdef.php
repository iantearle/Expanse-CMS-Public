<?php 
/********* Expanse ***********/
	
	/*   Do no edit below this line.   //---------------------------*/
	
	$max_file_upload = (intval(ini_get('upload_max_filesize')) * 1024 * 1024);
	$max_size = 0;
	if ($max_file_upload < 1024) {
		$max_size = "$max_size bytes";
	} else if ($max_file_upload < 1048576) {
		$max_size = number_format($max_file_upload/1024) .' KB';
	} else {
		$max_size = number_format($max_file_upload/1048576) .' MB';
	}
	define('MAX_UPLOAD', $max_size);
	$userip		= USER_IP;
	$cmsname	= CMS_NAME;
	$version	= CMS_VERSION;
	if(!ini_get('sendmail_from') && isInstalled()){
		$sendmail_from = getOption('adminemail');
		ini_set('sendmail_from', $sendmail_from);		
	}
	if(basename($_SERVER['PHP_SELF']) == 'main.php' && !isset($_SESSION['upgrade_available'])){
		$upgrade_results = getRemoteFile('http://expansecms.org/upgrade.php?version='.CMS_VERSION);
		$upgrade_results = explode('|',$upgrade_results->results);
		$_SESSION['upgrade_available'] = $upgrade_available = (bool) $upgrade_results[0];
		$_SESSION['upgrade_version'] = $upgrade_version = $upgrade_results[1];
	}
	
	define('UPGRADE_AVAILABLE', (isset($_SESSION['upgrade_available']) ? $_SESSION['upgrade_available'] : FALSE));
	define('UPGRADE_VERSION', (isset($_SESSION['upgrade_available']) ? $_SESSION['upgrade_version'] : ''));
	
	$headerDate	= date("l, F jS, Y");
	$output = isset($output) ? $output : '';


/****************************
* Main Variables			*
****************************/
$postsubmit = (isset($_POST['submit'])) ? $_POST['submit'] : '';
$page_meta = array('crumbs' => array(), 'title' => array());
$themesdir = 'themes';
$report = array(
'error' => array(),
'success' => array(),
'alert' => array(),
);
$months = array(
'01' => L_FIRST_MONTH,
'02' => L_SECOND_MONTH,
'03' => L_THIRD_MONTH,
'04' => L_FOURTH_MONTH,
'05' => L_FIFTH_MONTH,
'06' => L_SIXTH_MONTH,
'07' => L_SEVENTH_MONTH,
'08' => L_EIGHTH_MONTH,
'09' => L_NINTH_MONTH,
'10' => L_TENTH_MONTH,
'11' => L_ELEVENTH_MONTH,
'12' => L_TWELFTH_MONTH
);
$currencysymbols = array(
'USD' => '&#36;',
'AUD' => '&#36;',
'GBP' => '&pound;',
'CAD' => '&#36;',
'EUR' => '&euro;',
'JPY' => '&yen;',
);
$modules_dir = 'modules';
$module = (object) array('action'=>'');

$editors = array(
'CKEditor' => 'fck',
'Quick Tags' => 'quicktags',
'None' => 'none',
);
$special_chars = array('../','./','\\', ';', '!', '%');
$blocked_dirs = array('images', 'cache');
$category_action = false;
$admin_menu = array(
'sections' => array(),
'details' => array()
);

$the_type = check_get_alphanum('type');
$catid = check_get_id('cat_id');
$cat = check_get_alphanum('cat');
$action = check_get_alphanum('action');
$admin_sub = check_get_alphanum('sub');
$item_id = check_get_id('id');
$sort_by_subcats = check_get_id('sort_by_subcat');
$module_exists = false;
$module_css = '';
$module_js = '';
$category_clean_name = '';
define('CAT_ID', $catid);
define('CAT', $cat);
define('ACTION', $action);
define('ITEM_ID', $item_id);
define('ADDING',$the_type == 'add' ? true : false);
define('EDITING',$the_type == 'edit' ? true : false);
define('EDIT_SINGLE', (EDITING == true && !empty($item_id)) ? true : false);
define('EDIT_LIST', (EDITING == true && EDIT_SINGLE == false) ? true : false);
define('ADMIN_SUB', $admin_sub);
define('SORT_BY_SUBCATS', (!empty($sort_by_subcats) ? $sort_by_subcats : false));
define('PENDING_UPDATE',(getOption('expanseversion') < CMS_VERSION));
?>