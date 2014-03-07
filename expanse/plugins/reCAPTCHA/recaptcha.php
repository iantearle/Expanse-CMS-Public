<?php
/*
Plugin Name: reCaptcha
Plugin URL: http://expanse.io/
Description: Comment spam just sucks, so using reCAPTCHA, you can try and keep the comment spam down. Once you install, go to <a href="http://recaptcha.net" target="_blank">http://recaptcha.net</a> and signup for an account. You will be given an API key. Once you have that, go to the Expanse <a href="index.php?cat=admin&sub=prefs#commentSettings">Admin Preferences</a> and enter in the reCAPTCHA API key.
Version: 1.1
Author: Mr. Ian Tearle
Author URL: http://iantearle.com/
*/

/* Reset the variable to blank */
if(function_exists('add_variable')) {
	add_variable('recaptcha_theme:', 'main', 'content');
}

$publickey = trim(getOption('recaptcha_public_key'));
$privatekey = trim(getOption('recaptcha_private_key'));

define('RECAPTCHA_PUBLIC_KEY', ($publickey != false && !empty($publickey) ? $publickey : ''));
define('RECAPTCHA_PRIVATE_KEY', ($privatekey != false && !empty($privatekey) ? $privatekey : ''));

ozone_action('preferences_filter_menu', 'recaptcha_add_prefs_field');

if(function_exists('is_commenting') && is_commenting()) {
	if(!RECAPTCHA_PUBLIC_KEY) {
		return;
	}
	dropOzoneAction('is_commenting', 'handle_comment');
}
if(function_exists('is_contacting') && is_contacting()) {
	if(!RECAPTCHA_PUBLIC_KEY) {
		return;
	}
	dropOzoneAction('is_contacting', 'handle_contact');
}

function recaptcha_add_prefs_field() {
	?>
	<!-- /*   reCAPTCHA key   //===============================*/ -->
	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<label for="recaptcha_public_key" class="control-label">reCAPTCHA Public Key</label>
				<div class="controls">
					<input type="text" name="recaptcha_public_key" id="recaptcha_public_key" class="span12" value="<?php echo getOption('recaptcha_public_key'); ?>">
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="control-group">
				<label for="recaptcha_private_key" class="control-label">reCAPTCHA Private Key</label>
				<div class="controls">
					<input type="text" name="recaptcha_private_key" id="recaptcha_private_key" class="span12" value="<?php echo getOption('recaptcha_private_key'); ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<label for="recaptcha_theme" class="control-label">reCAPTCHA Public Key</label>
				<div class="controls">
					<select name="recaptcha_theme" id="recaptcha_theme" class="span12">
						<option value="red" <?php echo (getOption('recaptcha_theme') === 'red') ? 'selected="selected"' : '' ?>>Red (Default)</option>
						<option value="white" <?php echo (getOption('recaptcha_theme') === 'white') ? 'selected="selected"' : '' ?>>White</option>
						<option value="blackglass" <?php echo (getOption('recaptcha_theme') === 'blackglass') ? 'selected="selected"' : '' ?>>Blackglass</option>
						<option value="clean" <?php echo (getOption('recaptcha_theme') === 'clean') ? 'selected="selected"' : '' ?>>Clean</option>
						<option value="custom" <?php echo (getOption('recaptcha_theme') === 'custom') ? 'selected="selected"' : '' ?>>Custom</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function recaptcha_handle_comment() {
	if(!RECAPTCHA_PRIVATE_KEY || !is_commenting()) {
		return;
	}
	require_once(dirname(__FILE__).'/recaptchalib.php');

	$resp = null;

	if($_POST["recaptcha_response_field"]) {
		$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		if(!$resp->is_valid) {
		    // What happens when the CAPTCHA was entered incorrectly
		    printOut(FAILURE, 'The CAPTCHA you entered was incorrect or missing.');
		} else {
		    handle_comment();
		}
	}
}

function recaptcha_handle_contact() {
	if(!RECAPTCHA_PRIVATE_KEY || !is_contacting()) {
		return;
	}
	require_once(dirname(__FILE__).'/recaptchalib.php');

	$resp = null;

	if($_POST["recaptcha_response_field"]) {
		$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
		if(!$resp->is_valid) {
		    // What happens when the CAPTCHA was entered incorrectly
		    printOut(FAILURE, 'The CAPTCHA you entered was incorrect or missing. ');
		} else {
		    handle_contact();
		}
	}
}
if(defined('RECAPTCHA_PUBLIC_KEY') && RECAPTCHA_PUBLIC_KEY && defined('RECAPTCHA_PRIVATE_KEY') && RECAPTCHA_PRIVATE_KEY) {
	require_once(dirname(__FILE__).'/recaptchalib.php');
	$recaptcha = recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);
	$theme = getOption('recaptcha_theme');
	$custom = ($theme === 'custom') ? "custom_theme_widget: 'recaptcha_widget'" : '';
	$recaptchaTheme = "<script type=\"text/javascript\">var RecaptchaOptions = { theme: '$theme' $custom }; </script>";

	if(function_exists('add_variable')) {
		add_variable('recaptcha:'.(string) safe_tpl($recaptcha), 'main', 'content');
		add_variable('recaptcha_theme:'.(string) safe_tpl($recaptchaTheme), 'main', 'content');
	}
	ozone_action('is_commenting', 'recaptcha_handle_comment', 9);
	ozone_action('is_contacting', 'recaptcha_handle_contact', 9);
}
