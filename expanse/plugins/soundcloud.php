<?php
/*
Plugin Name: Vimeo
Plugin URL: http://expansecms.org/
Description: Easily include your Vimeo videos in your content. Simply add a URL in the format of <strong>{vimeo:4697849}</strong> in your description and the plugin will do the rest. Set height and width in the Expanse <a href="index.php?cat=admin&sub=prefs#appearanceSettings">Admin Preferences</a>.
Version: 1.0
Author: Mr. Ian Tearle
Author URL: http://iantearle.com/
*/

// Add some options to alter width and height
ozone_action('preferences_theme_menu', 'vimeo_add_prefs_field');

function vimeo_add_prefs_field(){
	?>
	<!-- /*  Vimeo Sizist Options   //===============================*/ -->
    <label for="vimeo_height">Vimeo Height</label>
    <input type="text" name="vimeo_height" id="vimeo_height" value="<?php echo getOption('vimeo_height'); ?>">
	<?php tooltip('Vimeo Height', 'Set the height option of your Vimeo Video.'); ?>
    <!-- /*  Vimeo Sizist Options   //===============================*/ -->
    <label for="vimeo_width">Vimeo Width</label>
    <input type="text" name="vimeo_width" id="vimeo_width" value="<?php echo getOption('vimeo_width'); ?>">
	<?php tooltip('Vimeo Width', 'Set the width option of your Vimeo Video.'); ?>
	<?php
}

/**
 * Vimeo
 *
 * Wraps Vimeo links (<a href="http://vimeo.com/4697849">Your Vimeo Link</a>)
 *
 */
function vimeo_code($text) {
    $text = preg_replace('~\{vimeo:(.*?)\}~i', '<object
		width="'.getOption('vimeo_width').'"
		height="'.getOption('vimeo_width').'"
		data="http://vimeo.com/moogaloop.swf?clip_id=$1&amp;server=vimeo.com"
		type="application/x-shockwave-flash">
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$1&amp;server=vimeo.com" />
		</object>', $text
	);
	return $text;
}
// Expanse Plugin Hooks

if(defined('EXPANSE')){
	// Item things
	ozone_filter('body', 'vimeo_code');
	ozone_filter('excerpt', 'vimeo_code');
	ozone_filter('descr', 'vimeo_code');
}

?>

<object height="18" width="100%">
  <param name="movie" value="https://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F{trackId}&player_type=tiny{widgetParams}"></param>
  <param name="allowscriptaccess" value="always"></param>
  <param name="wmode" value="transparent"></param>
  <embed wmode="transparent" allowscriptaccess="always" height="18" width="100%" src="https://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F{trackId}&player_type=tiny{widgetParams}"></embed>
</object>