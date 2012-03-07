<?php 
/********* Expanse ***********/
//Must be included at the top of all included files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');} 
$pagetitle = "Editing ";
 ?>
<!-- Begin page content -->
	   <?php 
		if($module_exists){
			if(empty($errors)){
				if((!empty($item_id) && !empty($items->id)) || (empty($item_id) && !empty($itemsList))){
				 include("$modules_dir/$cat_type/view.php");
				}
			}
		} else {
		?>
		<p><?php echo L_NOTHING_HERE ?></p>
		<?php
		}
?>
       </form>
<!-- End page content -->
