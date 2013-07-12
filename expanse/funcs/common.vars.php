<?php
/****************************************************************

                    `-+oyhdNMMMMMMMNdhyo/-`
                .+ymNNmys+:::....-::/oshmNNdy/.
             :smMmy/-``.-:-:-:----:-::--..-+hNNdo.
          .smMdo-`.:::.`               `.-::-`:smMd/`
        .yMNy- -::`                         `-::`:hMmo`
      `yMNo``:/`                               `-/--yMN+
     /mMy.`:-                                  ```./--dMd.
    sMN/ //`                                    `..`-/`sMN/
   yMm-`s.                                       `.-.`+-/NN+
  yMm--y. ```.-/ooyoooo/:.                        `---`/::NN/
 +MN:.h--/sdNNNNMMMNNNmmmhdoo+:.                  `.-::`/:+MN.
`NMs`hyhNNMMMMMMMMMMMNNNmhyso+syy/:-.`          `.-/+o++:. hMh
+MN.`:ssdmmmmmmmmmmmmhyyyo++:.``   `.-:::://:::::.```````  -MN-
mMy    ````````....`````````                         ````  `dMo
MM+            ````                                  ````   yMy
MM:                                                  ````   yMd
MM+                                                  ````   yMy
dMy                                                  ````  `dM+
+Mm.       ``-://++oo+///-``    ``-::/ooooyhhddddddmmm+yo. -MN-
`NM+ -/+s.`ommmmmmmmmmmmmmddhyhyo+++oosyhhdddmmmNNNNMddmh+ hMh
 /MN-oNmds``sdmmmmNNNNNmmmdNmmdddhhyyyyyhhdddmmmNNmmy-+:s`+MN.
  sMm-sNmd+`.ydmmNNNNNNmmmNNNmdhysso+oosyssssso/:--:`.-o`:NN/
   yMm-+Nmds..ymmmNNNNNmNNNNNmdhyso++//::--...```..``:+ /NN+
    sNN/-hmdh+-ommNNNNmNNNNNNmdhyso+//::--..````.` .+:`oMN/
     /mMy.+mmddhhmNNNmmNMNNNNmdyso+//::--..````` `++`-dMd.
      `yMN+./hNmmmmmmmmmNNNNmmhyso+//:--..``..`-//`-yMN/
        .yMNy--odNNNmmmmmNNNmdhyso+/::--..`.://-`:hMmo`
          .smMdo-.+ydNNmmddmmdysso+/::::////.`:smMd/`
             :smMmy+---/oysydhhyyyo/+/:-``-+hNNdo.
                .+yNMNmhs+/::....-::/oshmNNdy/.
                    .-+oyhdNMMMMMMMNdhyo/-`

Expanse - Content Management For Web Designers, By A Web Designer
			  Extended by Ian Tearle, @iantearle
		Started by Nate Cavanaugh and Jason Morrison

****************************************************************/

/*
------------------------------------------------------------
Common variables
============================================================
*/

$private_label 	= false;
$company_name	= '';
$company_url	= '';
$company_logo	= '';
$cms_name		= '';
$custom_news_feed = '';
$expanse_folder = 'expanse';
$custom_install_file = dirname(__FILE__).'/custom_install.php';
if(file_exists($custom_install_file)) {
	include($custom_install_file);
}
$company_name		= trim($company_name);
$company_url		= trim($company_url);
$company_logo		= trim($company_logo);
$cms_name			= trim($cms_name);
$cms_name			= empty($cms_name) ? $company_name : $cms_name;
$custom_news_feed 	= trim($custom_news_feed);
$expanse_folder 	= trim($expanse_folder);

/*
-------------------------------------------------
Reusable Variables
=================================================
*/
define('EXPANSE', true);
define('CMS_NAME', (empty($cms_name) || !$private_label ? 'Expanse' : $cms_name));
define('COMPANY_NAME', empty($company_name) || !$private_label ? 'Expanse Content Management System' : $company_name);
define('COMPANY_URL', empty($company_url) || !$private_label ? 'http://expanse.io/' : $company_url);
define('COMPANY_LOGO', (empty($company_logo) ? false : $company_logo));
<<<<<<< HEAD
define('CMS_VERSION', '2.3');
=======
define('CMS_VERSION', '2.0');
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
define('EXPANSE_FOLDER', (empty($expanse_folder) ? 'expanse' : $expanse_folder));
define('USER_IP', $_SERVER['REMOTE_ADDR']);
define('PREFIX', (isset($Database) && is_object($Database) ? $Database->Prefix : (isset($CONFIG) ? $CONFIG['prefix'] : '')));
define('EXPANSE_NEWS_URL', (empty($custom_news_feed) ? 'http://expanse.io/feed.php?feed=rss&pcat=21' : $custom_news_feed));
define('ERROR_IMG', 'error.png');
define('FILE_IMG', 'attached_file.png');
define('SUCCESS', '<div class="alert alert-block alert-success fade in" data-alert="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><p>%s</p></div>');
define('FAILURE', '<div class="alert alert-block alert-error fade in" data-alert="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><p>%s</p></div>');
define('ALERT', '<div class="alert alert-block alert-warning fade in" data-alert="alert"><a href="#" type="button" class="close" data-dismiss="alert">&times;</a><p>%s</p></div>');
define('NOTE', '<div class="alert alert-block alert-info fade in" data-alert="alert"><a href="#" type="button" class="close" data-dismiss="alert">&times;</a><p>%s</p></div>');
define('TPL_EXT', '.tpl.html');
define('XML_PAGE', 'feed.php');

//Detect the server OS
$using_apache = (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== FALSE || strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'litespeed') !== FALSE) ? TRUE : FALSE;
define('_APACHE',$using_apache);
$using_iis = (_APACHE == FALSE && strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'microsoft-iis') !== FALSE ) ? TRUE :  FALSE;
define('_IIS', $using_iis);
define('WORDWRAP', 75);
define('DESCR_LENGTH', 80);
if(isset($CONFIG)) {
	define('EXPANSEPATH', realpath($CONFIG['home']));
	define('HOMEPATH', realpath(EXPANSEPATH.'/..'));
	$uploads = (isset($CONFIG['site']) && is_dir(EXPANSEPATH.'/uploads/'.$CONFIG['site'])) ? EXPANSEPATH.'/uploads/'.$CONFIG['site'] .'/' : EXPANSEPATH.'/uploads/';
	define('UPLOADS', $uploads);
	define('THEMES', EXPANSEPATH.'/themes');
	define('PLUGINS', EXPANSEPATH.'/plugins');
	$clean_urls = getOption('use_clean_urls') == 1 ? TRUE : FALSE;
	define('CLEAN_URLS', $clean_urls);
	define('CAN_REWRITE', mod_rewrite());
	define('YOUR_SITE', checkTrailingSlash(getOption('yoursite')));
	if(isset($CONFIG['site'])) {
		define('SITE', $CONFIG['site']);
	}
	if(!defined('EXPANSE_URL')) {
		define('EXPANSE_URL', YOUR_SITE.checkTrailingSlash(EXPANSE_FOLDER));
	}
	$uploadsDir = (isset($CONFIG['site']) && is_dir(EXPANSEPATH.'/uploads/'.$CONFIG['site'])) ? EXPANSE_URL.'uploads/'.$CONFIG['site'] .'/' : EXPANSE_URL.'uploads/';
	define('UPLOADS_DIR', $uploadsDir);
	$index_file = getOption('index_file');
	define('INDEX_PAGE', empty($index_file) ? 'index.php' : $index_file);
	$howmany_edit = trim(getOption('howmany_edit'));
	$howmany_edit = !empty($howmany_edit) || $howmany_edit == '0' ? $howmany_edit : 20;
	define('EDIT_LIMIT', $howmany_edit);
}
define('CUSTOM_INSTALL', $private_label);

/*
-------------------------------------------------
Plugins
=================================================
*/

$all_plugins = isset($CONFIG) ? getOption('active_plugins') : false;
if($all_plugins !== false) {
	$all_plugins = is_array($all_plugins) ? $all_plugins : array();
	foreach($all_plugins as $plugin) {
		if(!empty($plugin) && file_exists(PLUGINS.'/'. $plugin)) {
			include_once(PLUGINS.'/'. $plugin);
		}
	}
}

/* Language  //-------------------------------*/
$LEX = array();
$LEX_JS = array();
define('LEXICON',realpath(dirname(__FILE__)).'/lexicon/');
$lang = isset($CONFIG) ? getOption('language') : '';
$lang = !empty($lang) && file_exists(LEXICON.$lang.'.php') ? $lang : 'en_us';
define('LANG',$lang);
require(LEXICON.LANG.'.php');
$LEX = applyOzoneAction('language', $LEX);
foreach($LEX as $flag => $term) {
	$constant_name = strtoupper('l_'.$flag);
	define($constant_name, $term);
	if(strpos(strtolower($flag),'js_') === 0) {
		$LEX_JS[] = '<p><input type="hidden" id="'.$constant_name.'" value="'.$term.'" /></p>';
	}
}

/* End Language   //-------------------------------*/

applyOzoneAction('common_vars');
$output = isset($output) ? $output : '';

/****************************
* Main Variables			*
****************************/
$themesdir = 'themes';
$report = array(
	'error' => array(),
	'success' => array(),
	'alert' => array(),
);
$admin_install = false;
$admin_uninstall = false;
