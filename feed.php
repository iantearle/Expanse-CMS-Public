<?php 
/********* Expanse ***********/
header('Content-type: application/xml');  
require_once("expanse/funcs/home.php");
echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
$xml = preg_replace("/&nbsp;/", ' ', $xml);
echo $xml;
?>