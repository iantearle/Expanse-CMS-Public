<?php
/********* Expanse ***********/
/*   Do no edit below this line.   //---------------------------*/
define('IS_FRONTEND', true);
define('IS_BACKEND', false);
require(dirname(__FILE__) . '/common.functions.php');
turnOffGlobals();
//Grab config
$config_file = realpath(dirname(__FILE__).'/../config.php');
if ($config_file) {
	require($config_file);
}
require(dirname(__FILE__) . '/functions.php');
installFile('expanse/install.php');
require(dirname(__FILE__) . '/template.functions.php');
require(dirname(__FILE__) . '/output.class.php');
require_once(dirname(__FILE__) . '/database.class.php');
require_once(dirname(__FILE__) . '/expanse.class.php');
require(dirname(__FILE__) . '/session.class.php');
require(dirname(__FILE__) . '/comment.class.php');
require(dirname(__FILE__) . '/contact.class.php');
require(dirname(__FILE__) . '/mail.class.php');
require(dirname(__FILE__) . '/ozone.php');
require(dirname(__FILE__) . '/ozone.default.php');
require(dirname(__FILE__) . '/template.class.php');
require(dirname(__FILE__) . '/common.vars.php');

/*Turn off globals*/
turnOffGlobals();
/*Instatiate Objects*/
$outmess = new outputMessages;
$output = '';
/*Template Objects*/
$sections = new Expanse('sections');
$comments = new Expanse('comments');
$images = new Expanse('images');
$layout = new stdClass;
$items = new Expanse('items');
$users = new Expanse('users');
/*Do Not Change Below*/
$option = getAllOptions();
$header = new stdClass;
$main = new stdClass;
$menu = new stdClass;
$footer = new stdClass;
if(CLEAN_URLS) {
	$pinfo = isset($_SERVER['PATH_INFO']) ? explode('?', $_SERVER['PATH_INFO']) : array('');
	$pinfo = $pinfo[0];
	$request_uri = explode('?', $_SERVER['REQUEST_URI']);
	$request_uri = $request_uri[0];
	$self = $_SERVER['PHP_SELF'];
	$home_path = parse_url(YOUR_SITE);
	$home_path = $home_path['path'];
	$home_path = trim($home_path, '/');
	$request_uri = str_replace($pinfo, '', $request_uri);
	$request_uri = trim($request_uri, '/');
	$request_uri = preg_replace("|^$home_path|", '', $request_uri);
	$request_uri = trim($request_uri, '/');
	$pinfo = trim($pinfo, '/');
	$pinfo = preg_replace("|^$home_path|", '', $pinfo);
	$pinfo = trim($pinfo, '/');
	$self = trim($self, '/');
	$self = preg_replace("|^$home_path|", '', $self);
	$self = str_replace($home_path, '', $self);
	$self = trim($self, '/');
	if(preg_match('|^(search)(/[\w\d-]+)*$|', $request_uri, $matches)) {
		// is a search
		$_GET['search'] = trim($matches[2], '/');
	} elseif(preg_match('|^([\w\d-]+)$|', $request_uri, $matches)) {
		$check = $sections->GetList(array(array('dirtitle', '=', $matches[1]),array('pid', '=', 0)));
		if(!empty($check)) {
			//is a category
			$_GET['pcat'] = $check[0]->id;
		} else {
			$check = $items->GetList(array(array('dirtitle', '=',$matches[1])));
			if(!empty($check)) {
				//is a page
				$_GET['ucat']= $check[0]->id;
			} else {
				trigger_404();
				$_GET['ucat']= '';
			}
		}
	} elseif(preg_match('|^([\w\d-]+)/([\w\d-]+)$|', $request_uri, $matches)) {
		$check = $items->GetList(array(array('dirtitle','=',$matches[2])));
		if(!empty($check)){
		//is a single item
		$_GET['pcat'] = $check[0]->pid;
		$_GET['item'] = $check[0]->id;
		} else {
			$check_parent = $sections->GetList(array(array('dirtitle', '=', $matches[1])));
			$check_parent = $check_parent[0]->id;
			$check = $sections->GetList(array(array('dirtitle', '=', $matches[2]), array('pid','=',$check_parent)));
			if(!empty($check)){
				//is a subcategory
				$_GET['pcat'] = $check[0]->pid;
				$_GET['subcat'] = $check[0]->id;
			}
		}
	} elseif(preg_match('|^([\w\d-]+)/page/([\d]+)$|', $request_uri,$matches)) {
		//paging category
		$check = $sections->GetList(array(array('dirtitle','=',$matches[1])));
		if(!empty($check)){
			$_GET['pcat']= $check[0]->id;
			$_GET['page']=$matches[2];
		}
	} elseif(preg_match('|^([\w\d-]+)/([\w\d-]+)/page/([\d]+)$|', $request_uri,$matches)) {
		//paging subcat
		$check_sub = $sections->GetList(array(array('dirtitle','=',$matches[2])));

		if(!empty($check_sub)) {
			//$_GET['pcat']= $check->id;
			$_GET['pcat'] = $check_sub[0]->pid;
			$_GET['subcat'] = $check_sub[0]->id;
			$_GET['page'] = $matches[3];
		}
	}
}	// <- Ends if (CLEAN_URLS)
/*   Page sections   //-------------------------------*/
$search = check_get_alphanum('search');
$pid = check_get_id('pid');
$pcat = check_get_id('pcat');
$subcat = check_get_id('subcat');
$ucat = check_get_id('ucat');
$item_id = check_get_id('item');
$feed = strtolower(check_get_alphanum('feed'));
$page = check_get_id('page');
$test_theme = check_get_alphanum('theme');
$preview = check_get_alphanum('preview');

define('SEARCH', $search);
define('CAT_ID', $pcat);
define('PID', $pid);
define('ITEM_ID', $item_id);
define('UCAT', $ucat);
define('SUBCAT', $subcat);
define('PAGE', $page);
define('FEED', $feed);
define('TEST_THEME', $test_theme);
define('LOGGED_IN', isLoggedIn());
define('PREVIEW', ($preview == 'true' && LOGGED_IN ? true : false));

if(LOGGED_IN){
	error_reporting(E_ALL | E_STRICT);
}

$option->yoursite = checkTrailingSlash($option->yoursite);
$use_theme = (!is_preview()) ? $option->theme : $test_theme;
$themefilepath = EXPANSEPATH ."/themes/$use_theme";
$themedir = EXPANSE_FOLDER."/themes/$use_theme";
$themetemplates = "$themefilepath/templates";
define('THEME_FOLDER', $themefilepath.'/');
define('THEME_URL', YOUR_SITE.$themedir.'/');
define('TEMPLATES', $themetemplates.'/');
$themecss = file_exists("$themedir/css/$use_theme.css") ? "$themedir/css/$use_theme.css" : "$themedir/css/styles.css";
$themejs = file_exists("$themedir/javascript/$use_theme.js") ? "$themedir/javascript/$use_theme.js" : "$themedir/javascript/javascript.js";
$themejquery = file_exists("$themedir/javascript/jquery.js") ? "$themedir/javascript/jquery.js" : "$themedir/javascript/jquery.$use_theme.js";
$thememodernizr = file_exists("$themedir/javascript/modernizr.js") ? "$themedir/javascript/modernizr.js" : "$themedir/javascript/modernizr.$use_theme.js";
$themeimages = "$themedir/images";
$css_link = YOUR_SITE.$themecss;
$javascript_link = YOUR_SITE.$themejs;
$jquery = YOUR_SITE.$themejquery;
$modernizr = YOUR_SITE.$thememodernizr;
$images_link = YOUR_SITE.$themeimages;
$uploads_url = EXPANSE_URL.'uploads/';
$rss_feed = YOUR_SITE."feed.php?feed=rss";
$rss_feed .= !empty($pcat) ? "&amp;pcat=$pcat" : '';
$rss_feed .= !empty($subcat) ? "&amp;subcat=$subcat" : '';
$atom_feed = YOUR_SITE."feed.php?feed=atom";
$atom_feed .= !empty($pcat) ? "&amp;pcat=$pcat" : '';
$atom_feed .= !empty($subcat) ? "&amp;subcat=$subcat" : '';
$theme_suffix = ((!is_preview()) ? '' : (CLEAN_URLS ? '?' : '&amp;')."theme={$test_theme}");

/*System Vars*/
$xmlpage = 'feed.php';
define('XML_PAGE', $xmlpage);
$tplext = TPL_EXT;

	if (file_exists("$themetemplates/logic.php")) {
		include("$themetemplates/logic.php");
	}

	/*Comment & Contact hooks*/
	applyOzoneAction('is_commenting');
	applyOzoneAction('is_contacting');

	$addExtras = new stdClass();
	$addExtras->copyrightdate = isset($addExtras->copyrightdate) ? $addExtras->copyrightdate : date('Y');
	$addExtras->logged_in = LOGGED_IN;
	$addExtras->themedir = $themedir;
	$addExtras->themefilepath = $themefilepath;
	$addExtras->themetemplates = $themetemplates;
	$addExtras->themecss = $themecss;
	$addExtras->themejs = $themejs;
	$addExtras->themeimages = $themeimages;
	$addExtras->css_link = $css_link;
	$addExtras->javascript_link = $javascript_link;
	$addExtras->jquery = $jquery;
	$addExtras->modernizr = $modernizr;
	$addExtras->theme_folder = YOUR_SITE."$themedir/";
	$addExtras->images_folder = YOUR_SITE."$themedir/images/";
	$addExtras->smilies_folder = YOUR_SITE."$themedir/images/smilies/";
	$addExtras->templates_folder = YOUR_SITE."$themedir/templates/";
	$addExtras->css_folder = YOUR_SITE."$themedir/css/";
	$addExtras->javascript_folder = YOUR_SITE."$themedir/javascript/";
	$addExtras->images_link = $images_link;
	$addExtras->uploads_url = $uploads_url;
	$addExtras->rss_feed = $rss_feed;
	$addExtras->atom_feed = $atom_feed;
	$addExtras->cms_name = CMS_NAME;
	$addExtras->company_url = COMPANY_URL;
	$addExtras->expanse_url = EXPANSE_URL;

	foreach ($option as $optname => $optval) {
		$header->{$optname} = $optval;
		$footer->{$optname} = $optval;
		$menu->{$optname} = $optval;
		//$userpage->{$optname} = $optval;
		$xmlvars[$optname] = $optval;
		$layout->{$optname} = $optval;
	}
	foreach ($addExtras as $xp => $xv) {
		$header->{$xp} = $xv;
		$footer->{$xp} = $xv;
		$menu->{$xp} = $xv;
		//$userpage->{$xp} = $xv;
		$xmlvars[$xp] = $xv;
		$user_vars['main'][$xp] = $xv;
		$layout->{$xp} = $xv;
	}

  // =================== HTML ================
	if (!is_feed()) {
		$header->pcat = $pcat;
		$header->subcat = $subcat;
		$footer->pcat = $pcat;
		$footer->subcat = $subcat;
		$header = make_header();
		$footer = make_footer();
		$menu = make_menu();
		$fof = '';

		if(is_search()) { //performing a search
			$tplfile = file_exists("$themetemplates/search{$tplext}") ? "search{$tplext}" : '';
			$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
			$main = expanse("search:$search|template:$tplfile|ignore_paging:true", $user_vars['main'], true);
		} elseif(is_home()) { //on the home page
			$start_cat = $option->startcategory;
			if(strpos($start_cat, ':P') === FALSE) { //using a (sub)category for the home page
				$sections->Get($start_cat);
				if ($sections->pid != 0) {
					$subname = $sections->sectionname;
					$sections->Get($sections->pid);
					$parentname = $sections->sectionname;
					$cleanedname = $sections->dirtitle;
					$cat_type = $sections->cat_type;
					$tplfile = file_exists("$themetemplates/$cleanedname{$tplext}") ? $cleanedname.$tplext : $cat_type.$tplext;
					$tplfile = (!has_homepage()) ? $tplfile : "home{$tplext}";
					$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
					$main = expanse("category:$parentname|subcategory:$subname|template:$tplfile", $user_vars['main'], true);
				} else {
					$tplfile = file_exists("$themetemplates/$sections->dirtitle{$tplext}") ? $sections->dirtitle.$tplext : $sections->cat_type.$tplext;
					$tplfile = (!has_homepage()) ? $tplfile : "home{$tplext}";
					$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
					$main = expanse("category:$sections->sectionname|template:$tplfile", $user_vars['main'], true);
				}
			} else { //using a user page for the home page
				$items->Get($start_cat);
				$cleanedname = $items->dirtitle;
				$cat_type = 'page';
				$tplfile = file_exists("$themetemplates/$cleanedname{$tplext}") ? $cleanedname.$tplext : $cat_type.$tplext;
				$tplfile = (!has_homepage()) ? $tplfile : "home{$tplext}";
				$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
				$main = expanse("type:static|id:{$items->id}|template:$tplfile", $user_vars['main'], true);
			}
		} elseif (is_userpage()) { //On a user page
			$items->Get($ucat);
			if (!empty($items->id) && $items->type == 'static') {
				$tplfile = file_exists("$themetemplates/{$items->dirtitle}{$tplext}") ? "{$items->dirtitle}{$tplext}" : "page{$tplext}";
				$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
				$main = expanse("type:static|id:$ucat|template:@$tplfile", $user_vars['main'], true);
			} else {
				trigger_404();
			}
		} elseif(is_category()) { //Inside a category
			$sections->Get($pcat);
			if(is_subcat()){ // In the subcategory
				$parentname = $sections->sectionname;
				$cleanedname = $sections->dirtitle;
				$cat_type = $sections->cat_type;
				$sections->Get($subcat);
				$tplfile = file_exists("$themetemplates/sub_{$sections->dirtitle}{$tplext}") ? "sub_{$sections->dirtitle}{$tplext}" : (file_exists("$themetemplates/$cleanedname{$tplext}") ? $cleanedname.$tplext : $cat_type.$tplext);
				$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
				$main = expanse("category:$parentname|subcategory:$sections->sectionname|template:$tplfile", $user_vars['main'], true);
			} elseif(is_single()) { //Viewing a single item
				$tplfile = file_exists("$themetemplates/{$sections->dirtitle}_full{$tplext}") ? "{$sections->dirtitle}_full{$tplext}" : "{$sections->cat_type}_full{$tplext}";
				$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
				$main = expanse("category:$sections->sectionname|id:$item_id|template:$tplfile", $user_vars['main'], true);
			} else { // Viewing just the category
				if($sections->pid != 0) {
					$sections->Get($sections->pid);
				}
				if(empty($sections->id)) {
					trigger_404();
				}
				$tplfile = file_exists("$themetemplates/$sections->dirtitle{$tplext}") ? $sections->dirtitle.$tplext : $sections->cat_type.$tplext;
				$tplfile = (isset($tplfile)) ? safe_tpl($tplfile) : trigger_404();
				$main = expanse("category:$sections->sectionname|template:$tplfile", $user_vars['main'], true);
			}
		}
		if(!empty($fof)) {
			printOut(FAILURE, L_PAGE_NOT_FOUND);
			$userpage->output = $output;
			$tplfile = safe_tpl("$themetemplates/@misc{$tplext}");
			$main = sprintt($userpage, $tplfile);
		}

// ============== FEED =============
	} else {
		$extraVars = array();
		if ($feed != 'atom') {
			$feedtemplate = "rss{$tplext}";
			$xmlvars['copyrightdate'] = date('Y');
			$xmlvars['lastmodified'] = date('Y');
			$feedtype = 'rss';
		} else {
			$feedtemplate = "atom{$tplext}";
			$feedtype = 'atom';
		}
		$feedtemplate = safe_tpl($feedtemplate);

		if (is_search()) {
			$xml = expanseXML("search:$search|template:$feedtemplate|ignore_paging:true|feedtype:$feedtype", $xmlvars, true);
		} elseif (is_home()) {
			$sections->Get($option->startcategory);
			if ($sections->pid == 0) {
				$catname = $sections->sectionname;
				$subname = '';
			} else {
				$subname = "|subcategory:$sections->sectionname";
				$sections->Get($sections->pid);
				$catname = $sections->sectionname;
			}
			$xml = expanseXML("category:$catname{$subname}|template:$feedtemplate|feedtype:$feedtype", $xmlvars, true);
		} else {
			$sections->Get($pcat);
			if (!is_subcat()) {
				$xml = expanseXML("category:$sections->sectionname|template:$feedtemplate|feedtype:$feedtype", $xmlvars, true);
			} else {
				$parentname = $sections->sectionname;
				$sections->Get($subcat);
				$xml = expanseXML("category:$parentname|subcategory:$sections->sectionname|template:$feedtemplate|feedtype:$feedtype", $xmlvars, true);
			}
		}
	}

	if($feed) {
		$layout->header = $header;
		$layout->main_content = $main;
		$layout->menu = $menu;
		$layout->footer = $footer;
		$layout_file = "layout{$tplext}";
		$layout = (file_exists("$themetemplates/".$layout_file)) ? sprintt($layout, "$themetemplates/@".$layout_file) : $main;
	} else {
	  	$layout->header = $header;
	  	$layout->main_content = $main;
	  	$layout->menu = $menu;
	  	$layout->footer = $footer;
	  	$layout_file = "layout{$tplext}";
	  	$layout = (file_exists("$themetemplates/".$layout_file)) ? sprintt($layout, "$themetemplates/@".$layout_file) : $header . $menu . $main . $footer;
	}
?>