<?php
/*
Plugin Name: Multi Contact
Plugin URL: http://expanse.io/
Description: Allows a contact form to be sent to different email addresses from an option on the form.
Version: 1.0
Author: Mr. Ian Tearle
Author URL: http://iantearle.com/
*/

if(function_exists('is_contacting') && is_contacting()){
	if(!$_POST['recipient_required']){return;}
	dropOzoneAction('is_contacting','handle_contact');
}

function multi_handle_contact() {
	if (!is_contacting()) { return; }
	global $option, $themetemplates, $tplext, $themecss;
	if(!isFlooding($option->floodcontrol)) {
		$contact = new stdClass();
		$contact = new multicontactProcess();
		$contact->Subject = L_CONTACT_SUBJECT;
		$contact->FromName = sprintf(L_CONTACT_FROM,$option->sitename);
		$contact->RequiredFields = "name,email,url,message";
		$contact->Template = "$themetemplates/@contactmailer{$tplext}";
		$contact->ExtraVars = array('themecss' => YOUR_SITE. $themecss, 'pageurl' => $_SERVER['REQUEST_URI'], 'ip' => $_SERVER['REMOTE_ADDR'], 'hostmask' => gethostbyaddr($_SERVER['REMOTE_ADDR']), );
		register_flooding();
		$contact->multicontactHandle();
	} else {
		printOut(FAILURE, sprintf(L_FLOODING_MESSAGE, $option->floodcontrol));
	}
}

class multicontactProcess {
	var $RequiredFields = '';
	var $Template;
	var $ExtraVars = array();
	var $FromName;
	var $Subject;
	function multicontactHandle() {
		if(!is_contacting()) { return; }

		$author = $_POST['email_required_email'];
		$email = $_POST['name_required'];
		$website = $_POST['url_url'];
		$comment = $_POST['message_required'];

		//	$recipient1 = $_POST['recipient_required'];

		global $option;

		$check_for_url_dns = true;
		$allowed_protocols = array('http', 'https');

		$bannedwords = explode(',', $option->bannedwords);
		$bannedips = explode(',', $option->bannedips);
		$recipient1 = explode(',', str_replace(' ', '', $_POST['recipient_required']));
		$fromaddr = '<' . $recipient1[0] . '>';
		$extravars = array();
		ozone_action('all', 'prepComment');
		foreach($bannedips as $value) {
			if($_SERVER['REMOTE_ADDR'] == $value) {
				return printOut(FAILURE, L_CONTACT_FAILURE);
			}
		}

		$errors = array(
			'missing' => array(),
			'wrong_format' => array(
			'alnum' => array(),
			'email' => array(),
			'phone_number' => array(),
			'ssn' => array()
		),
			'final' => ''
		);
		$domain_check_options = array('allowed_schemes' => $allowed_protocols, 'domain_check' => $check_for_url_dns);
		foreach($_POST as $ind => $val) {
			$val = trim($val);
			$post_index = strtolower($ind);
			foreach($bannedwords as $value) {
				if(strpos(strtolower(" " . $val), trim(strtolower($value)))) {
					return printOut(FAILURE, L_CONTACT_FAILURE);
				}
			}
			switch(TRUE) {
				case strpos($post_index, '_email'):
					$ind = str_replace(array('_email', '_required'), '', $ind);
					if(!checkEmail($val)) {
						$errors['wrong_format']['email'][] = $ind;
					}
					break;
				case strpos($post_index, '_url'):
					$ind = str_replace(array('_url', '_required'), '', $ind);
					if(strpos(' '.strtolower($val),'http') !== 1) {
						$val = 'http://'.$val;
					}
					if(!valid_uri($val, $domain_check_options)) {
						$errors['wrong_format']['url'][] = $ind;
					}
					break;
				case strpos($post_index, '_alnum'):
					$ind = str_replace('_alnum', '', $ind);
					if(!ctype_alnum($val)) {
						$errors['wrong_format']['alnum'][] = $ind;
					}
					break;
				case strpos($post_index, '_required'):
					$ind = str_replace('_required', '', $ind);
					if(empty($val)) {
						$errors['missing'][] = $ind;
					}
					break;
			}
			if(!strpos($post_index, '_allow_html')) {
				$val = htmlentities(strip_tags($val), ENT_QUOTES);
			}
			if(is_array($val)) {
				foreach ($val as $k => $v) {
					$val[$k] = trim($v);
					if(empty($v)) {
						unset($val[$k]);
					}
				}
				$contact->{$ind} = !empty($val) ? serialize($val) : '';
			} else {
				$contact->{$ind} = trim($val);
			}
		} //end post loop
		if(!empty($errors['missing'])) {
			$errors['final'] .= sprintf(L_MISSING_FIELDS, proper_list($errors['missing']));
		}
		if(!empty($errors['wrong_format']['email'])) {
			$errors['final'] .= '<p>'.sprintf(L_COMMENT_FORMAT_EMAIL,proper_list($errors['wrong_format']['url'], L_CONCAT_OR)).'</p>';
		}
		if(!empty($errors['final'])) {
			return printOut(FAILURE, $errors['final']);
		}
		$contact->created = (isset($_POST['created'])) ? $_POST['created'] : time();
		$contact->ip = $_SERVER['REMOTE_ADDR'];
		foreach($option as $k => $v) {
			$contact->{$k} = $v;
		}
		foreach($this->ExtraVars as $k => $v) {
			$contact->{$k} = $v;
		}
		$contact->date = gmdate($option->dateformat, $contact->created + ($option->timeoffset * 3600) + date('Z'));
		$contact->time = gmdate($option->timeformat, $contact->created + ($option->timeoffset * 3600) + date('Z'));
		$templatebody = sprintt($contact, $this->Template);
		$plaintext = trim(strip_tags($templatebody));

		$mail = new PHPMailer();
		$mail->IsSendmail();
		$mail->SetFrom('noreply@expanse.io', CMS_NAME);
		$mail->AddReplyTo('noreply@expanse.io', CMS_NAME);
		$mail->AddAddress($recipient1);
		$mail->Subject = $this->Subject;
		$mail->MsgHTML($templatebody);
		$mail->AltBody = $plaintext;

		if($mail->Send()) {
			return true;
		} else {
			printOut(FAILURE, L_CONTACT_FAILURE);
			return false;
		}
	}
}

ozone_action('preferences_menu','multi_contact');

function multi_contact() {
	?>
	<!-- /*   Multi Contact   //===============================*/ -->
	<h3 class="stretchToggle" title="multi_contact"><a href="#multi_contact"><span>Multiple Contacts</span></a></h3>
	<div class="stretch" id="multi_contact">
	<label for="multi_contact_1">First</label>
	<input type="text" name="multi_contact_1" id="multi_contact_1" value="<?php echo getOption('multi_contact_1'); ?>">
	<?php tooltip('Enter Multiple Contacts', 'Enter up to three additional contacts for use in conjunction with your contact form.'); ?>
	<label for="multi_contact_2">Second</label>
	<input type="text" name="multi_contact_2" id="multi_contact_2" value="<?php echo getOption('multi_contact_2'); ?>">
	<label for="multi_contact_3">Third</label>
	<input type="text" name="multi_contact_3" id="multi_contact_3" value="<?php echo getOption('multi_contact_3'); ?>">
	</div>
	<?php
}

$multi_contact_1 = getOption('multi_contact_1');
$multi_contact_2 = getOption('multi_contact_2');
$multi_contact_3 = getOption('multi_contact_3');

if(function_exists('add_variable')) {
	add_variable('multi_contact_1:'.$multi_contact_1, 'main');
	add_variable('multi_contact_2:'.$multi_contact_2, 'main');
	add_variable('multi_contact_3:'.$multi_contact_3, 'main');
}

ozone_action('is_contacting', 'multi_handle_contact', 9);