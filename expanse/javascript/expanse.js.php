<?php
$files =  array('jquery-ui.min', 'jquery-cookie', 'jquery.nearest.min', 'underscore.min', 'backbone.min', 'bootstrap.min', 'bootstrap-datepicker', 'bootstrap-timepicker', 'chart.min', 'expanse.main');
if(isset($_GET['full'])) {
	$files = array_merge($files, array());
}
$extended = array();
/*   Do not edit below this line   //-------------------------------*/
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = ((60 * 60) * 24) * 14 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);

$folder = dirname(__FILE__).'/';

ob_start("compress");
function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $buffer);
    /* remove other spaces before/after ) */
    $buffer = preg_replace(array('(( )+\))','(\)( )+)'), ')', $buffer);
    return $buffer;
}

$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';

/* your css files */
foreach($files as $include) {
	$include = trim($include);
	if(!empty($get_extended) && isset($extended[$get_extended])) {
		$the_file = $folder.$get_extended.'.js';
	} else {
		$the_file = $folder.$include.'.js';
	}
	if(empty($include) || !file_exists($the_file)) {
		continue;
	}
	include($the_file);
}
ob_end_flush();