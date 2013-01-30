<?php
$files =  array('global', 'module', 'admin', 'bootstrap', 'bootstrap-responsive', 'bootstrap-wysihtml5');
$extended = array('install' => 'installation css', 'mailer' => 'Custom mailer css');

/*   Do not edit below this line   //-------------------------------*/
$css_file = '/*
------------------------------------------------------------
Expanse
http://expansecms.org
Content management for the web deisgner, by the web designer
Written, with love, by Ian Tearle @iantearle
Based on original work by Nate Cavanaugh and Jason Morrison
All rights reserved.
============================================================
*/
';
$folder = dirname(__FILE__).'/';
foreach($files as $include) {
	$include = trim($include);
	$the_file = $folder.$include.'.css';
	if(empty($include) || !file_exists($the_file)) {
		continue;
	}
	$css_file .= (string) file_get_contents($the_file);
}
$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';
if(!empty($get_extended) && isset($extended[$get_extended])) {
	$css_file .= file_get_contents($folder.$get_extended.'.css');
}

header("Content-Type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
header("Last-Modified: " . date(DateTime::RFC1123));
header("Expires: " . date(DateTime::RFC1123, time() + 600));

echo $css_file;
