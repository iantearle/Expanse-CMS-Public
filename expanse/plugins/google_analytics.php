<?php
/*
Plugin Name: Google Analytics for Expanse
Plugin URI: http://google.com/analytics/
Description: Google Analytics tracking plugin for Expanse. Once installed, go to <a href="http://google.com/analytics" target="_blank">http://google.com/analytics</a> and signup for an account. When you have added your site, go to the Expanse <a href="index.php?cat=admin&sub=prefs#googleanalytics">Admin Preferences</a> and enter your unique ID. You will also have to add the {ga} variable in your footer template.
Version: 1.0
Author: Ian Tearle
Author URL: http://www.iantearle.com/
*/
ozone_action('preferences_menu','ga_config_menu');

function ga_config_menu() {
?>
	<!-- /*   Google Analytics Menu   //===============================*/ -->
	<div class="accordion-group" title="googleanalytics">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#googleanalytics">Google Analytics</a>
		</div>
		<div id="googleanalytics" class="accordion-body collapse">
			<div class="accordion-inner">
				<div class="control-group">
					<label for="ga_tracking_id" class="control-label">Account ID</label>
					<div class="controls">
						<input type="text" name="ga_tracking_id" id="ga_tracking_id" value="<?php echo getOption('ga_tracking_id'); ?>" <?php popOver('bottom', 'Google Analytics Account ID', 'Sign-in to your Google Analytics account.<br/><a href=\'http://google.com/analytics\'>http://google.com/analytics</a>. Copy and paste the provided Account ID into the input box.'); ?>>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}


function ga_config() {
	if(isset($_POST["ga_tracking_id"])) {
		if(empty($_POST["ga_tracking_id"])) {
			$output = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("Account ID cleared. Stats will not be logged.") . "</strong></p></div>";
		} else {
			$output = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("Saved Changes.") . "</strong></p></div>";
		}

		update_option("ga_tracking_id", trim($_POST["ga_tracking_id"]));
	}
}

function ga_include_code() {
	$ga_id = getOption("ga_tracking_id");
	if(empty($ga_id)) {
		return;
	}
	$print = "\n<!-- Google Analytics Expanse Plugin -->\n";
	$print .= "<script type=\"text/javascript\">\n";
	$print .= "var _gaq = _gaq || [];\n";
	$print .= "_gaq.push(['_setAccount', '$ga_id']);\n";
	$print .= "_gaq.push(['_trackPageview']);\n";
	$print .= "(function() {\n";
	$print .= "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
	$print .= "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
	$print .= "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
	$print .= "})();\n";
	$print .= "</script>\n";
	$print .= "<!-- End -->\n\n";

	return $print;
}

$gainclude = ga_include_code();
if(function_exists('add_variable')) {
	add_variable('google_analytics:'.(string) safe_tpl($gainclude), 'footer');
}
