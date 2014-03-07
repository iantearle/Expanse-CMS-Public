<?php
/*
Plugin Name: Moderate Posts
Plugin URI: http://www.iantearle.com/
Description: Moderate all posts saved.
Author: Ian Tearle
Version: 1.0
Author URI: http://www.iantearle.com/
*/

ozone_action('item_edit', 'moderate_posts', 9);
ozone_action('item_add', 'moderate_posts', 9);

function moderate_posts($items) {
	if($items->online) {
		return;
	}
	require_once(dirname(__FILE__).'/../../funcs/class.template.php');
	require_once(dirname(__FILE__) . '/../../funcs/mail.class.php');
	global $sections;
	$option = getAllOptions();
	$users = new Expanse('users');
	$users->Get($_SESSION['id']);

	$mail->expanseurl = EXPANSE_URL;
	$mail->company_url = COMPANY_URL;
	$mail->cms_name = CMS_NAME;
	$mail->yoursite = YOUR_SITE;
	$mail->current_user = $users->display_name;
	$mail->title = $items->title;
	$mail->cat_id = $items->pid;
	$mail->id = $items->id;
	$mail->dirtitle = $items->dirtitle;
	$mail->descr = $items->descr;
	$mail->adminname = $option->yourname;
	$mail->image = $items->image;

	$current_cat = !empty($items->pid) ? $items->pid : $option->startcategory;
	$parent_name = $sections->sectionname;
	$parent_dirtitle = $sections->Get($current_cat);
	$parent_dirtitle = $sections->dirtitle;
	$mail->permalink = YOUR_SITE.((CLEAN_URLS) ? ((!$parent_dirtitle) ? "$items->dirtitle" : "$parent_dirtitle/$items->dirtitle") . '?preview=true' : INDEX_PAGE."?pcat={$items->pid}&amp;item={$items->id}");

	$adminemail = $option->adminemail;

	$templatebody = sprintt($mail, dirname(__FILE__).'/misc/@moderate_mailer.tpl.html');
	$plaintext = trim(strip_tags($templatebody));
	$fromaddr = '<' . $adminemail . '>';
	$fromname = CMS_NAME.' '.$adminemail;

	$mail = new PHPMailer();
	$mail->IsSendmail();
	$mail->SetFrom('noreply@expanse.io', CMS_NAME);
	$mail->AddReplyTo('noreply@expanse.io', CMS_NAME);
	$mail->AddAddress($usersEmail);
	$mail->Subject = CMS_NAME.' - Moderation Needed';
	$mail->MsgHTML($templatebody);
	$mail->AltBody = $plaintext;

	if(!$mail->Send()) {
		return printOut(FAILURE, 'Moderation mail not sent.');
	}
}