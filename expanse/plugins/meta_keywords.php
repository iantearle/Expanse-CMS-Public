<?php
/*
Plugin Name: Meta Keywords
Plugin URI: http://www.iantearle.com/
Description: Get your site higher on the search engine results. This plugin will allow you the option of managing your meta keywords without editing the template.
See General Settings to add your own.
Author: Ian Tearle
Version: 1.1
Author URI: http://www.iantearle.com/
*/

ozone_action('preferences_general_menu','mk_config_menu');

function mk_config_menu() {
	?>
	<!-- /*  Meta Keywords List   //===============================*/ -->
	<div class="span6">
		<div class="control-group">
			<label for="meta_keywords" class="control-label">Meta Keywords</label>
			<div class="controls">
				<textarea name="meta_keywords" cols="40" class="span12 formfields" id="meta_keywords" <?php popOver('bottom', 'Meta Keywords', 'Enter your keywords separated by a comma.'); ?>><?php echo getOption('meta_keywords'); ?></textarea>
			</div>
		</div>
	</div>
	<?php
}



$keywords = getOption('meta_keywords');

if(function_exists('add_variable')) {
	add_variable('keywords:'.$keywords, 'header');
}
?>