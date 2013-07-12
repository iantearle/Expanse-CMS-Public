<?php
$files =  array('global', 'module', 'admin', 'bootstrap', 'bootstrap-responsive', 'bootstrap-wysihtml5','datetimepicker');
$extended = array('install' => 'installation css', 'mailer' => 'Custom mailer css');

/*   Do not edit below this line   //-------------------------------*/
header("Content-Type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
header("Last-Modified: " . date(DateTime::RFC1123));
header("Expires: " . date(DateTime::RFC1123, time() + 600));

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

echo $css_file;
ob_start("compress");
function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
}

$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';

/* your css files */
foreach($files as $include) {
	$include = trim($include);
	if(!empty($get_extended) && isset($extended[$get_extended])) {
		$the_file = $folder.$get_extended.'.css';
	} else {
		$the_file = $folder.$include.'.css';
	}
	if(empty($include) || !file_exists($the_file)) {
		continue;
	}
	include($the_file);
}
ob_end_flush();