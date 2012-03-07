<?php
/*
Plugin Name: Akismet
Plugin URL: http://expansecms.com/
Description: Comment spam just sucks, so using Akismet, you can try and keep the comment spam down. Once you install, go to <a href="http://wordpress.com" target="_blank">http://wordpress.com</a> and signup for an account. You will be emailed an API key. Once you have that, go to the Expanse <a href="index.php?cat=admin&sub=prefs#commentSettings">Admin Preferences</a> and enter in the Wordpress API key.
Version: 1.1
Author: Mr. Nate Cavanaugh
Author URL: http://alterform.com/
*/
$api_key = trim(getOption('akismet_wp_key'));

define('AKISMET_API', ($api_key != false && !empty($api_key) ? $api_key : false));
ozone_action('preferences_filter_menu', 'akismet_add_prefs_field');

if(function_exists('is_commenting') && is_commenting()){
	if(!AKISMET_API){return;}
	dropOzoneAction('is_commenting','handle_comment');
}
if(function_exists('is_contacting') && is_contacting()){
	if(!AKISMET_API){return;}
	dropOzoneAction('is_contacting','handle_contact');
}

function akismet_add_prefs_field(){
	?>
	<!-- /*   Akismet API key   //===============================*/ -->
    <label for="akismet_wp_key">Wordpress.com API Key</label>
    <input type="text" name="akismet_wp_key" id="akismet_wp_key" value="<?php echo getOption('akismet_wp_key'); ?>">
	<?php tooltip('Wordpress.com API Key', 'If you want to try and cut down on comment spam, you can enter in your API key from Wordpress.com and the Akismet plugin will try and cut down on your spam.'); ?>
	<?php
}

function akismet_handle_comment(){
	require_once(dirname(__FILE__).'/Akismet.class.php');
	if(!AKISMET_API || !is_commenting()){return;}
	$akismet = new Akismet(YOUR_SITE, AKISMET_API);
	
	$author = $_POST['email_required_email'];
	$email = $_POST['name_required'];
	$website = $_POST['url_required_url'] || $_POST['url_url'];
	$comment = $_POST['message_required'];
	
	$akismet->setAuthor($author);
	$akismet->setAuthorEmail($email);
	$akismet->setAuthorURL($website);
	$akismet->setContent($comment);
	$akismet->setPermalink(YOUR_SITE.INDEX_PAGE.'?pcat='.CAT_ID.'&amp;item='.ITEM_ID);
	if($akismet->isSpam()) {
		printOut(FAILURE, 'Something smells like spam...');
	} else {
		handle_comment();
	}
}

function akismet_handle_contact(){
	require_once(dirname(__FILE__).'/Akismet.class.php');
	if(!AKISMET_API || !is_contacting()){return;}
	$akismet = new Akismet(YOUR_SITE, AKISMET_API);
	
	$author = $_POST['email_required_email'];
	$email = $_POST['name_required'];
	$website = $_POST['url_required_url'] || $_POST['url_url'];
	$comment = $_POST['message_required'];
	
	$akismet->setAuthor($author);
	$akismet->setAuthorEmail($email);
	$akismet->setAuthorURL($website);
	$akismet->setContent($comment);
	$akismet->setPermalink(YOUR_SITE.INDEX_PAGE.'?ucat='.ITEM_ID);
	if($akismet->isSpam()) {
		printOut(FAILURE, 'Something smells like spam...');
	} else { 
		handle_contact();
	}
}

function moderate_comments($comment){
	require(dirname(__FILE__).'/Akismet.class.php');
	if(!AKISMET_API){return;}
	$akismet = new Akismet(YOUR_SITE, AKISMET_API);
	$akismet->setAuthor($comment->name);
	$akismet->setAuthorEmail($comment->email);
	$akismet->setAuthorURL($comment->url);
	$akismet->setContent($comment->message);
	$akismet->setPermalink(YOUR_SITE.INDEX_PAGE.'?pcat='.$comment->cid.'&amp;item='.$comment->itemid);
	if(is_posting('Report')){

		$spam_ham = isset($_POST['ham_or_spam']) ? $_POST['ham_or_spam'] : '';
		if($spam_ham == 'spam'){
			$akismet->submitSpam();
			printOut(SUCCESS, 'Thanks, this comment has been reported as spam');
		}
		elseif($spam_ham == 'ham'){
			$akismet->submitHam();
			printOut(SUCCESS, 'Thanks, this comment has been reported as good.');
		}
	}
	global $output;
	?>
	<fieldset>
	<legend>Akismet Options <?php tooltip('Akismet Options', 'Akismet is the Comment Spam Database, provided by the folks at Wordpress. Here you can mark whether or not a comment is spam and submit it to the database. This helps everyone out, so we highly recommend it.'); ?></legend>
	<?php echo $output; ?>
	<label for="ham">This is not spam</label><input type="radio" name="ham_or_spam" id="ham" value="ham" /><br />
	<label for="spam">This is some bad spam</label><input type="radio" name="ham_or_spam" id="spam" value="spam" /><br />
	<input type="submit" name="submit" id="report_spam" value="Report" />
	</fieldset>
	<?php
	return $comment;
}

	ozone_action('manage_comment', 'moderate_comments', 9);
	ozone_action('is_commenting', 'akismet_handle_comment', 9);
	ozone_action('is_contacting', 'akismet_handle_contact', 9);
	
?>