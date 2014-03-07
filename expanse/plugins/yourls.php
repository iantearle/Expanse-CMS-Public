<?php
/*
Plugin Name: YOURL's API for Expanse
Plugin URI: http://yourls.org/
Description: Add your own short links to every post. Simply save and edit to activate.
Version: 1.0
Author: Ian Tearle
Author URL: http://www.iantearle.com/
*/
ozone_action('preferences_menu','yourls_menu');
ozone_action('do_sharing','do_yourls');

function yourls_menu() {
  ?>
	<!-- /*   yourls_menu Menu   //===============================*/ -->
	<div class="accordion-group" title="yourls">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#yourls">YOURL's</a>
		</div>
		<div id="yourls" class="accordion-body collapse">
			<div class="accordion-inner">
				<div class="control-group">
					<label for="yourls_api_url" class="control-label">API URL</label>
					<div class="controls">
						<input type="text" name="yourls_api_url" id="yourls_api_url" value="<?php echo getOption('yourls_api_url'); ?>" <?php popOver('bottom', 'YOURL\'s API URL', 'Enter only your YOURL\'s API domain here (http://yourls.org/).'); ?>>
					</div>
				</div>
				<div class="control-group">
					<label for="yourls_signature" class="control-label">API Signature</label>
					<div class="controls">
						<input type="text" name="yourls_signature" id="yourls_signature" value="<?php echo getOption('yourls_signature'); ?>" <?php popOver('bottom', 'YOURL\'s Signature', 'Enter your YOURL\'s signature here.'); ?>>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}



function do_yourls() {
	$mod = new Module();

	$timestamp = time();
	$savedSignature = getOption("yourls_signature");
	$savedAPI = getOption("yourls_api_url");
	$signature = md5($timestamp . $savedSignature);

	$items = $mod->items;
	$sections = $mod->sections;
	$expanseurl = EXPANSE_URL;
	$yoursite = YOUR_SITE;
	if(CLEAN_URLS) {
		$sections->Get($items->pid);
		$section_id = $sections->dirtitle;
		$the_item_id = $items->dirtitle;
	} else {
		$section_id = $sections->id;
		$the_item_id = $items->id;
	}
	$dynamic_url = $yoursite.((CLEAN_URLS) ? "$section_id/$the_item_id" : INDEX_PAGE."?pcat=$section_id&amp;item=$the_item_id");
	$static_url = $yoursite.((CLEAN_URLS) ? $the_item_id : INDEX_PAGE."?ucat=$items->id");
	$page_link = ($items->type !== 'static') ? $dynamic_url : $static_url;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $savedAPI . 'yourls-api.php');
	curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
	curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
	curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
			'url'      => $page_link,
			'keyword'  => '',
			'format'   => 'simple',
			'action'   => 'shorturl',
			'timestamp' => $timestamp,
			'signature' => $signature
	));

	$yourls = curl_exec($ch);
	curl_close($ch);

	?>
		<div class="control-group">
			<label for="pageLink" class="control-label">Short URL</label>
			<div class="controls">
				<input type="text" class="span8 shareField" id="hrtgs_url" value="<?php echo $yourls; ?>" />
				<input type="hidden" class="span8 shareField" id="hrtgs" name="hrtgs" value="<?php echo str_replace($savedAPI,'',$yourls); ?>" />
			</div>
		</div>
	<?php
}

function yourls_config() {
	if(isset($_POST["yourls_api_url"]) || isset($_POST["yourls_signature"])) {
		if(empty($_POST["yourls_api_url"])) {
			$output .= "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("API root url cleared. Links will not be shortened.") . "</strong></p></div>";
		} elseif(empty($_POST["yourls_signature"])) {
			$output .= "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("API Signature cleared. A valid signature is required.") . "</strong></p></div>";
		} else {
			$output .= "<div id=\"message\" class=\"updated fade\"><p><strong>" . __("Saved Changes.") . "</strong></p></div>";
		}

		update_option("yourls_api_url", trim($_POST["yourls_api_url"]));
		update_option("yourls_signature", trim($_POST["yourls_signature"]));
	}
}
