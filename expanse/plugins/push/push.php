<?php
/*
Plugin Name: Parse.com Push Scheduler
Plugin URI: http://www.iantearle.com/
Description: Push notification to iOS your content items.
Author: Ian Tearle
Version: 1.0
Author URI: http://www.iantearle.com/
*/

ozone_action('more_options','parse_push_options');
ozone_action('preferences_menu','parse_config_menu');

function parse_config_menu() {
?>
	<!-- /*   Parse API Menu   //===============================*/ -->
	<div class="accordion-group" title="parseconfig">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#parseconfig">Parse API</a>
		</div>
		<div id="parseconfig" class="accordion-body collapse">
			<div class="accordion-inner">
				<div class="control-group">
					<label for="parse_application_id" class="control-label">Application ID</label>
					<div class="controls">
						<input type="text" name="parse_application_id" id="parse_application_id" value="<?php echo getOption('parse_application_id'); ?>">
					</div>
				</div>
				<div class="control-group">
					<label for="parse_rest_API_key" class="control-label">REST API Key</label>
					<div class="controls">
						<input type="text" name="parse_rest_API_key" id="parse_rest_API_key" value="<?php echo getOption('parse_rest_API_key'); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

function sendPush($postData) {
	date_default_timezone_set('UTC');

	$APPLICATION_ID = getOption('parse_application_id');
	$REST_API_KEY = getOption('parse_rest_API_key');

	if(!$APPLICATION_ID || !$REST_API_KEY) {
		printOut(FAILURE, 'You need to set your API keys.');
		return;
	}

	$url = 'https://api.parse.com/1/push';
	$data = array(
	    'channel' => $postData['parse_channel'],
	    'type' => 'ios',
	    "push_time" => gmdate("Y-m-d\TH:i:s\Z", strtotime($_POST['parse_push_date'] . ' ' . $_POST['parse_push_time'])),
	    'data' => array(
	        'alert' => $postData['parse_alert'],
	        'hatype' => $postData['parse_hatype'],
	        'id' => $postData['parse_id'],
	        'sound' => 'push.caf',
	    ),
	);
	$_data = json_encode($data);
	$headers = array(
	    'X-Parse-Application-Id: ' . $APPLICATION_ID,
	    'X-Parse-REST-API-Key: ' . $REST_API_KEY,
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($_data),
	);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	curl_exec($curl);

	printOut(SUCCESS, 'Your message has been queued.');
}

// Usage:
if(isset($_POST['parse_send'])) {
	sendPush($_POST);
}

function parse_push_options() {
	global $item_id;
	if(EDITING):
	?>
	<script type="text/javascript">
		$(function () {
			$('#parse_push_date').datepicker();
			$('.timepicker-default').timepicker();
		});
	</script>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#stretchContainer" href="#parsepush">Send a push notification</a>
		</div>
		<div id="parsepush" class="accordion-body collapse">
			<div class="accordion-inner">
					<input type="hidden" name="parse_id" id="parse_id" value="<?php echo $item_id; ?>">
					<input type="hidden" name="parse_hatype" id="parse_hatype" value="property">
					<h3>Parse Push Notification</h3>
					<p>Schedule a push notification about this item.</p>

					<div class="row-fluid">
						<div class="span4">
							<div class="control-group">
								<label for="parse_push_time" class="control-label">Schedule Time</label>
								<div class="controls">
									<input type="text" name="parse_push_date" class="span8" id="parse_push_date" value="" />
									<input type="text" name="parse_push_time" class="span4 timepicker-default" id="parse_push_time" value="" />
								</div>
							</div>
						</div>
						<div class="span4">
							<div class="control-group">
								<label for="parse_channel" class="control-label">Channel</label>
								<div class="controls">
									<input type="text" name="parse_channel" id="parse_channel" class="span12" value="">
								</div>
							</div>
						</div>
						<div class="span4">
							<div class="control-group">
								<label for="parse_alert" class="control-label">Alert</label>
								<div class="controls">
									<textarea name="parse_alert" id="parse_alert" class="span12"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" name="parse_send" class="btn btn-warning" value="Send">
					</div>
			</div>
		</div>
	</div>
	<?php
	endif;
}