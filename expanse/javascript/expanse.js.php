<?php 
$files =  array(
'jquery','prototype', 'prototype.plugins','moo.fx', 'moo.fx.pack','autosuggest','expanse.main','bootstrap-alerts','bootstrap-dropdown','bootstrap-tabs','bootstrap-modal'
);
if(isset($_GET['full'])){
$files = array_merge($files, array('effects','dragdrop','controls','slider'));
}
$extended = array();
/*   Do not edit below this line   //-------------------------------*/
ob_start ("ob_gzhandler");
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = ((60 * 60) * 24) * 14 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
$folder = dirname(__FILE__).'/';
foreach($files as $include){
$include = trim($include);
$the_file = $folder.$include.'.js';
if(empty($include) || !file_exists($the_file)){continue;}
include($the_file);
echo "\n";
}
$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';
if(!empty($get_extended) && isset($extended[$get_extended])){
include($folder.$get_extended.'.js');
echo "\n";
}
?>
