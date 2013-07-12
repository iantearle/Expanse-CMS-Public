<?php
/****************************************************************

                    `-+oyhdNMMMMMMMNdhyo/-`
                .+ymNNmys+:::....-::/oshmNNdy/.
             :smMmy/-``.-:-:-:----:-::--..-+hNNdo.
          .smMdo-`.:::.`               `.-::-`:smMd/`
        .yMNy- -::`                         `-::`:hMmo`
      `yMNo``:/`                               `-/--yMN+
     /mMy.`:-                                  ```./--dMd.
    sMN/ //`                                    `..`-/`sMN/
   yMm-`s.                                       `.-.`+-/NN+
  yMm--y. ```.-/ooyoooo/:.                        `---`/::NN/
 +MN:.h--/sdNNNNMMMNNNmmmhdoo+:.                  `.-::`/:+MN.
`NMs`hyhNNMMMMMMMMMMMNNNmhyso+syy/:-.`          `.-/+o++:. hMh
+MN.`:ssdmmmmmmmmmmmmhyyyo++:.``   `.-:::://:::::.```````  -MN-
mMy    ````````....`````````                         ````  `dMo
MM+            ````                                  ````   yMy
MM:                                                  ````   yMd
MM+                                                  ````   yMy
dMy                                                  ````  `dM+
+Mm.       ``-://++oo+///-``    ``-::/ooooyhhddddddmmm+yo. -MN-
`NM+ -/+s.`ommmmmmmmmmmmmmddhyhyo+++oosyhhdddmmmNNNNMddmh+ hMh
 /MN-oNmds``sdmmmmNNNNNmmmdNmmdddhhyyyyyhhdddmmmNNmmy-+:s`+MN.
  sMm-sNmd+`.ydmmNNNNNNmmmNNNmdhysso+oosyssssso/:--:`.-o`:NN/
   yMm-+Nmds..ymmmNNNNNmNNNNNmdhyso++//::--...```..``:+ /NN+
    sNN/-hmdh+-ommNNNNmNNNNNNmdhyso+//::--..````.` .+:`oMN/
     /mMy.+mmddhhmNNNmmNMNNNNmdyso+//::--..````` `++`-dMd.
      `yMN+./hNmmmmmmmmmNNNNmmhyso+//:--..``..`-//`-yMN/
        .yMNy--odNNNmmmmmNNNmdhyso+/::--..`.://-`:hMmo`
          .smMdo-.+ydNNmmddmmdysso+/::::////.`:smMd/`
             :smMmy+---/oysydhhyyyo/+/:-``-+hNNdo.
                .+yNMNmhs+/::....-::/oshmNNdy/.
                    .-+oyhdNMMMMMMMNdhyo/-`

Expanse - Content Management For Web Designers, By A Web Designer
			  Extended by Ian Tearle, @iantearle
		Started by Nate Cavanaugh and Jason Morrison

****************************************************************/

class commentProcess {
	var $Template;
	var $ExtraVars = array();
	var $FromName;
	var $Subject;

	function commentHandle() {
		global $comments, $option;

		if($this->isFlooding($option->floodcontrol)) {
			return printOut(FAILURE, sprintf(L_FLOODING_MESSAGE, $option->floodcontrol));
		}
		$moderate = (isset($option->moderate_comments) && $option->moderate_comments == 1) ? true : false;
		$check_for_url_dns = true;
		$allowed_protocols = array('http', 'https');
		$bannedwords = explode(',', $option->bannedwords);
		$bannedips = explode(',', $option->bannedips);
		$adminEmail = explode(',', str_replace(' ', '', $option->adminemail));
		$fromaddr = '<' . $adminEmail[0] . '>';
		$extravars = array();
		$commentvars = get_object_vars($comments);
		foreach($bannedips as $value) {
			if($_SERVER['REMOTE_ADDR'] == $value) {
				return printOut(FAILURE, L_ADD_COMMENT_FAILURE);
			}
		}
		$RequiredFields = explode(',',$this->RequiredFields);
		$checks = array('_email','_url','_alnum','_required', '_phone_number');
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
					return printOut(FAILURE, L_ADD_COMMENT_FAILURE);
				}
			}
			$val = htmlentities(strip_tags(trim($val)), ENT_QUOTES);
			switch(TRUE) {
				case strpos($post_index, '_email'): {
					$ind = str_replace(array('_email', '_required'), '', $ind);
					if(!checkEmail($val)) {
						$errors['wrong_format']['email'][] = $ind;
					}
					break;
				}
				case strpos($post_index, '_url'): {
					$ind = str_replace(array('_url', '_required'), '', $ind);
					if($val != '') {
						if(!preg_match("/^(http|https):/", $val)){ $val = 'http://'.$val; }
						if(!valid_uri($val, $domain_check_options)) {
							$errors['wrong_format']['url'][] = $ind;
						}
					}
					break;
				}
				case strpos($post_index, '_alnum'): {
					$ind = str_replace('_alnum', '', $ind);
					if(!ctype_alnum($val)){
						$errors['wrong_format']['alnum'][] = $ind;
					}
					break;
				}
				case strpos($post_index, '_required'): {
					$ind = str_replace('_required', '', $ind);
					if(empty($val)){
						$errors['missing'][] = $ind;
					}
					break;
				}
				case strpos($post_index, '_phone_number') : {
					$ind = str_replace('_phone_number', '', $ind);
					if(!checkPhone($val)) {
						$errors['wrong_format']['phone_number'][] = $ind;
					}
					break;
				}
			}
			if(!strpos($post_index, '_allow_html')) {
				$val = strip_tags($val);
			}
			if(array_key_exists($ind, $commentvars)) {
				if(is_array($val)) {
				foreach($val as $k => $v) {
					$val[$k] = trim($v);
					if(empty($v)) {
						unset($val[$k]);
					}
				}
					$comments->{$ind} = !empty($val) ? serialize($val) : '';
				} else {
					$comments->{$ind} = trim($val);
				}
			}
		}

		$arrayKeys = array();
		foreach($_POST as $ind => $val) {
			$ind = str_replace($checks, '', $ind);
			$arrayKeys[] = $ind;
		}

		$array_diff = array_diff($RequiredFields, $arrayKeys);

		if($array_diff) {
			foreach($array_diff as $string) {
				$errors['missing'][] = str_replace($checks, '', $string);
			}

		}
		if(!empty($errors['missing'])) {
			$errors['final'] .= sprintf(L_MISSING_FIELDS, '<strong>'.proper_list($errors['missing']).'</strong>');
		}
		if(!empty($errors['wrong_format']['email'])) {
			$errors['final'] .= '<p>'.sprintf(L_COMMENT_FORMAT_EMAIL,proper_list($errors['wrong_format']['email'], L_CONCAT_OR)).'</p>';
		}
		if(!empty($errors['wrong_format']['url'])) {
			$errors['final'] .= '<p>'.sprintf(L_COMMENT_FORMAT_URL,proper_list($errors['wrong_format']['url'], L_CONCAT_OR)).'</p>';
		}
		if(!empty($errors['final'])) {
			applyOzoneAction('comment_failure',$_POST);
			return printOut(FAILURE, $errors['final']);
		}
		$comments->online = ($moderate == true) ? 0 : 1;
		$comments->created = time();
		$comments->ip = $_SERVER['REMOTE_ADDR'];
		if($comments->SaveNew()) {
			applyOzoneAction('comment_success',$comments);
			foreach($option as $k => $v) {
				$comments->{$k} = $v;
			}
			foreach($this->ExtraVars as $k => $v) {
				$comments->{$k} = $v;
			}
			$comments->date = gmdate($option->dateformat, $comments->created + ($option->timeoffset * 3600) + date('Z'));
			$comments->time = gmdate($option->timeformat, $comments->created + ($option->timeoffset * 3600) + date('Z'));
			if($moderate) {
				$comments->needs_moderation = true;
			}
			$templatebody = sprintt($comments, $this->Template);
			$plaintext = trim(strip_tags($templatebody));

			$mail = new PHPMailer();
			$mail->IsSendmail();
			$mail->SetFrom('noreply@expanse.io', CMS_NAME);
			foreach($adminEmail as $email) {
				$mail->AddAddress($email);
			}
			$mail->Subject = $this->Subject;
			$mail->MsgHTML($templatebody);
			$mail->AltBody = $plaintext;

			if($mail->Send()) {
				$comment_success = ($moderate == true) ? L_ADD_COMMENT_PENDING : L_ADD_COMMENT_SUCCESS;
				printOut(SUCCESS, $comment_success);
			}
			return true;

		} else {
			printOut(FAILURE, L_ADD_COMMENT_FAILURE);
			applyOzoneAction('comment_failure',$_POST);
			return false;
		}
	}

	function isFlooding($flood, $currtime = '') {
		$currtime = (empty($currtime) && isset($_SESSION['current_time'])) ? $_SESSION['current_time'] : $_SESSION[$currtime];
		$latest_post = time();
		$currtime = applyOzoneAction('comment_current_time', $currtime);
		$latest_post = applyOzoneAction('comment_current_time', $latest_post);
		applyOzoneAction('is_flooding', $currtime, $latest_post);
		$diff = ($latest_post != $currtime) ? $latest_post - $currtime : $flood;
		return($diff < $flood) ? true : false;
	}
}
