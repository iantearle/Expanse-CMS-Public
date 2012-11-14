<?php
/********* Expanse ***********/

class contactProcess {
	var $Template;
	var $ExtraVars = array();
	var $FromName;
	var $Subject;
	function contactHandle() {
		global $option;

		$check_for_url_dns = true;
		$allowed_protocols = array('http', 'https');

		$bannedwords = explode(',', $option->bannedwords);
		$bannedips = explode(',', $option->bannedips);
		$recipient = explode(',', str_replace(' ', '', $option->adminemail));
		$fromaddr = '<' . $recipient[0] . '>';
		$extravars = array();
		ozone_action('all', 'prepComment');
		foreach($bannedips as $value) {
			if($_SERVER['REMOTE_ADDR'] == $value) {
				return printOut(FAILURE, L_CONTACT_FAILURE);
			}
		}
		$RequiredFields = array('email_required_email','name_required','message_required');
		$checks = array('_email','_url','_alnum','_required');

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

		foreach ($_POST as $ind => $val) {
			$postArray = array();
			$val = trim($val);
			$post_index = strtolower($ind);

			foreach ($bannedwords as $value) {
				if (strpos(strtolower(" " . $val), trim(strtolower($value)))) {
					return printOut(FAILURE, L_CONTACT_FAILURE);
				}
			}
			switch(TRUE) {
			case strpos($post_index, '_email'):
				$ind = str_replace(array('_email', '_required'), '', $ind);
				if(!checkEmail($val)){
					$errors['wrong_format']['email'][] = $ind;
				}
			break;
			case strpos($post_index, '_url'):
				$ind = str_replace(array('_url', '_required'), '', $ind);
				if($val != ''){
					if(!preg_match("/^(http|https):/", $val)){ $val = 'http://'.$val; }
					if(!valid_uri($val, $domain_check_options)){
						$errors['wrong_format']['url'][] = $ind;
					}
				}
			break;
			case strpos($post_index, '_alnum'):
				$ind = str_replace('_alnum', '', $ind);
				if(!ctype_alnum($val)){
					$errors['wrong_format']['alnum'][] = $ind;
				}
			break;
			case strpos($post_index, '_required'):
				$ind = str_replace('_required', '', $ind);
				if(empty($val)){
					$errors['missing'][] = $ind;
				}
			break;
			}

			if(!strpos($post_index, '_allow_html')){
				$val = htmlentities(strip_tags($val), ENT_QUOTES);
			}
			if(is_array($val)) {
				foreach ($val as $k => $v) {
					$val[$k] = trim($v);
					if (empty($v)) {
						unset($val[$k]);
					}
				}
				$contact->{$ind} = !empty($val) ? serialize($val) : '';
			} else {
				$contact->{$ind} = trim($val);
			}
		} //end post loop

		$arrayKeys = array_keys($_POST);
		$array_diff = array_diff($RequiredFields, $arrayKeys);
		if($array_diff) {
			foreach($array_diff as $string) {
				$errors['missing'][] = str_replace($checks, '', $string);
			}

		}

		if(!empty($errors['missing'])){
			$errors['final'] .= sprintf(L_MISSING_FIELDS, proper_list($errors['missing']));
		}
		if(!empty($errors['wrong_format']['email'])){
			$errors['final'] .= '<p>'.sprintf(L_COMMENT_FORMAT_EMAIL,proper_list($errors['wrong_format']['email'], L_CONCAT_OR)).'</p>';
		}
		if(!empty($errors['wrong_format']['url'])){
			$errors['final'] .= '<p>'.sprintf(L_COMMENT_FORMAT_URL,proper_list($errors['wrong_format']['url'], L_CONCAT_OR)).'</p>';
		}
		if(!empty($errors['final'])){
			return printOut(FAILURE, $errors['final']);
		}
          $contact->created = (isset($_POST['created'])) ? $_POST['created'] : time();
          $contact->ip = $_SERVER['REMOTE_ADDR'];
          foreach ($option as $k => $v) {
              $contact->{$k} = $v;
          }
          foreach ($this->ExtraVars as $k => $v) {
              $contact->{$k} = $v;
          }
          $contact->date = gmdate($option->dateformat, $contact->created + ($option->timeoffset * 3600) + date('Z'));
          $contact->time = gmdate($option->timeformat, $contact->created + ($option->timeoffset * 3600) + date('Z'));
          $templatebody = sprintt($contact, $this->Template);
          $plaintext = trim(strip_tags($templatebody));
          $mail = new htmlMimeMail;
          $mail->setFrom($this->FromName . ' ' . $fromaddr);
          $mail->setSubject($this->Subject);
          $mail->setHTML($templatebody, $plaintext);
          if ($mail->send($recipient)) {
              printOut(SUCCESS, L_CONTACT_SUCCESS);
              return true;
          } else {
              printOut(FAILURE, L_CONTACT_FAILURE);
              return false;
          }
      }
  }
?>
