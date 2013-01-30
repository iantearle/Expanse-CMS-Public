<?php
$files =  array('jquery.min', 'jquery-ui.min', 'jquery-cookie', 'jquery.nearest.min', 'underscore.min', 'backbone.min', 'bootstrap.min', 'wysihtml5-0.3.0', 'bootstrap-wysihtml5', 'expanse.main');
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
foreach($files as $include) {
	$include = trim($include);
	$the_file = $folder.$include.'.js';
	if(empty($include) || !file_exists($the_file)){
		continue;
	}
	include($the_file);
	echo "\n";
}
$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';
if(!empty($get_extended) && isset($extended[$get_extended])) {
	include($folder.$get_extended.'.js');
	echo "\n";
}
