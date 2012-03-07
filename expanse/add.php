<?php 
/********* Expanse ***********/
//Must be included at the top of all included files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');} 
 
if($module_exists){
	include("$modules_dir/$cat_type/view.php");
} else {
	?>
	<p><?php echo L_NOTHING_HERE ?></p>
	<?php
}
?>