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

class userAuth {
	var $Username;
	var $ProtectedPages = array();
	var $Admin = 0;
	var $Permissions = array();
	var $Authorized = false;
	var $DisplayName = '';

	function userAuth() {
		$this->users = get_dao('users');
		$this->sections = get_dao('sections');
	}

	function isLoggedIn() {
		$users = $this->users;
		if((isset($_SESSION['id']) && isset($_SESSION['displayname']) && isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['email']))) {
			$userarray =& $_SESSION;
		} elseif (isset($_COOKIE['baked'])) {
			$userarray = unserialize(base64_decode($_COOKIE['baked']));
		} else {
			$this->sanitizePost();
			return false;
		}
		$user = $users->GetList(array(array('id', '=', $userarray['id']), array('username', '=', $userarray['username']), array('password', '=', $userarray['password']), array('email', '=', $userarray['email'])));
		if(!empty($user)) {
			$user = $user[0];
			$this->Id = $_SESSION['id'] = $user->id;
			$this->Username = $_SESSION['username'] = $user->username;
			$this->DisplayName = $_SESSION['displayname'] = $user->displayname;
			$this->Email = $_SESSION['email'] = $user->email;
			$this->Admin = $user->admin;
			$this->SectionAdmin = $user->section_admin;
			$this->Permissions = unserialize($user->permissions);
			$this->Disabled = $user->disabled;
			$_SESSION['password'] = $user->password;
			$this->Authorized = $this->isAuthorized();
			if($this->Disabled) {
				$this->sanitizePost();
				return false;
			}
			return true;
		}
		$this->sanitizePost();
		return false;
	}

	function isAuthorized() {
		$cat = check_get_alphanum('cat');
		$cat_id = check_get_id('cat_id');
		if(empty($cat) && empty($cat_id)) {
			return true;
		}
 		if(ctype_digit((string) $cat_id)) {
 			if(in_array($cat_id, $this->Permissions)) {
				return true;
			}
		}
		if($cat == 'admin' && $this->Admin == 1) {
			return true;
		}
		return false;
	}

	function login() {
		$expiration = time() + 3600 * 24 * 14;
		$users = new Expanse('users');
		$userarray = $_POST;
		$password = md5($userarray['password']);
		$user = $users->GetList(array(array('username', '=', $userarray['username']), array('password', '=', $password)));
		if(!empty($user)) {
			$user = $user[0];
			$_SESSION['id'] = $user->id;
			$_SESSION['username'] = $user->username;
			$_SESSION['password'] = $user->password;
			$_SESSION['displayname'] = $user->displayname;
			$_SESSION['email'] = $user->email;
			$_SESSION['permissions'] = unserialize($user->permissions);
			if(isset($_POST['rememberme'])) {
				$baked['id'] = $user->id;
				$baked['username'] = $user->username;
				$baked['password'] = $user->password;
				$baked['email'] = $user->email;
				$baked = serialize($baked);
				$baked = base64_encode($baked);
				setcookie('baked', $baked, $expiration);
			}
			$user = applyOzoneAction('admin_login', $user);
			$redirect = 'index.php';
			if(strpos(strtolower($_SERVER['REQUEST_URI']),'action=logout')) {
				header("Location: $redirect");
			}
			setcookie('entry_page', '', time() - 3600);
		} else {
			$this->sanitizePost();
			printOut(FAILURE, L_BAD_LOGIN);
		}
	}

	function createMenu() {
		$sections = $this->sections;
		$auth = $this->Permissions;
		$menu = '<ul class="dropdown-menu">';
		$menu_list = $sections->GetList('SELECT * FROM *table* WHERE id='.implode(' OR id=',$auth));
		$menu_list = applyOzoneAction('admin_menu',$menu_list);
		foreach($menu_list as $val) {
			$menu .= '
				<li class="dropdown-submenu '.$val->cat_type.'">
					<a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">'.$val->sectionname.'</a>
					<ul class="dropdown-menu>
						<li><a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">' . L_MENU_EDIT . '</a></li>
						<li><a href="index.php?type=add&amp;cat_id=' . $val->id . '" class="addTo">'.L_MENU_ADD.'</a></li>
					</ul>
				</li>';
		}
		$menu .= '</ul>';
		$menu = applyOzoneAction('admin_menu_html',$menu, $menu_list);
		return $menu;
	}
	function createSummary() {
		global $Database;
		$sections = get_dao('sections');
		$auth = $this->Permissions;
		$ov_list = $sections->GetList('SELECT * FROM *table* WHERE id='.implode(' OR id=',$auth));
		$ov_list = applyOzoneAction('admin_summary',$ov_list);
		$ov = '<ul class="nav nav-pills nav-stacked">';
		foreach($ov_list as $val) {
			if($val->cat_type == 'pages') {
				$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE type='static'");
				$total_items = $Database->Result(0, 'itemcount');
				$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE type='static' AND online=1");
				$online_items = $Database->Result(0, 'itemcount');
				$ov .= '<li>
				<h3>' . ucwords($val->sectionname) . '</h3>
				<p>'.sprintf(L_OVER_PAGES_ONLINE, $online_items, $total_items).'</p></li>';
				continue;
			}
			$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE pid=$val->id");
			$total_items = $Database->Result(0, 'itemcount');
			$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE pid=$val->id AND online=1");
			$online_items = $Database->Result(0, 'itemcount');
			$ov .= '<li>
			<h3>' . ucwords($val->sectionname) . '</h3>
			<p>'.sprintf(L_OVER_ITEMS_ONLINE, $online_items, $total_items).'</p></li>';
		}
		$ov .= '</ul>';
		$ov = applyOzoneAction('admin_summary_html',$ov,$ov_list);
		return $ov;
	}
	function createSummaryChart() {
		global $Database;
		$sections = get_dao('sections');
		$auth = $this->Permissions;
		$ov_list = $sections->GetList('SELECT * FROM *table* WHERE id='.implode(' OR id=',$auth));
		$ov_list = applyOzoneAction('admin_summary',$ov_list);
		$ov = 'var data = {';
		$labels = array();
		$dataTotal = array();
		$dataOnline = array();
		foreach($ov_list as $val) {
			if($val->cat_type == 'pages') {
				$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE type='static'");
				$total_items = $Database->Result(0, 'itemcount');
				$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE type='static' AND online=1");
				$online_items = $Database->Result(0, 'itemcount');
				array_push($labels, '"'.ucwords($val->sectionname).'",');
				array_push($dataTotal, $total_items.',');
				array_push($dataOnline, $online_items.',');
				continue;
			}
			$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE pid=$val->id");
			$total_items = $Database->Result(0, 'itemcount');
			$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE pid=$val->id AND online=1");
			$online_items = $Database->Result(0, 'itemcount');
			array_push($labels, '"'.ucwords($val->sectionname).'",');
			array_push($dataTotal, $total_items.',');
			array_push($dataOnline, $online_items.',');
		}
		$ov .= 'labels : [ ';
		foreach($labels as $k => $v) {
			$ov .= $v;
		}
		$ov .= '],';
		$ov .= 'datasets : [ { fillColor : "#42758d", strokeColor : "#ffffff",';
		$ov .= 'data : [';
		foreach($dataTotal as $k => $v) {
			$ov .= $v;
		}
		$ov .= ']';
		$ov .= '}, { fillColor : "#d8e6d3", strokeColor : "#ffffff",';
		$ov .= 'data : [';
		foreach($dataOnline as $k => $v) {
			$ov .= $v;
		}
		$ov .= ']';
		$ov .= '} ]};';
		$ov = applyOzoneAction('admin_summary_html',$ov,$ov_list);
		return $ov;
	}
	function getTotals() {
		global $Database;
		$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items WHERE aid=$this->Id");
		$user_count = $Database->Result(0, 'itemcount');
		$Database->Query("SELECT COUNT(id) as itemcount FROM ".PREFIX."items");
		$total_count = $Database->Result(0, 'itemcount');
		return array('total_count' => $total_count, 'user_count' => $user_count);
	}
	function updateAdmins() {
		$sections = get_dao('sections');
		$users = get_dao('users');
		$ids = array();
		$allsections = $sections->GetList(array(array('pid', '=', 0)));
		$alladmins = $users->GetList(array(array('admin', '=', 1)));
		foreach($allsections as $k => $v) {
			$ids[] = $v->id;
		}
		$_SESSION['permissions'] = $ids;
		unset($_SESSION['menu']);
		$ids = serialize($ids);
		foreach($alladmins as $i => $val) {
			$users->Get($val->id);
			$users->permissions = $ids;
			$users->Save();
		}
	}
	function logout() {
		if(isset($_SESSION['id']) || isset($_COOKIE['baked'])) {
			setcookie('baked', '', time() - 3600);
			$_SESSION = array();
			if(isset($_COOKIE[session_name()])) {
				setcookie(session_name(), '', time() - 3600, '/');
			}
			@session_destroy();
			applyOzoneAction('admin_logout');
			return printOut(SUCCESS, L_LOGGED_OUT);
		} else {
			return printOut(FAILURE, L_NOT_LOGGED_IN);
		}
	}

	function sanitizePost() {
		$_POST = array();
	}

	function retrievePassword() {
		$username = isset($_POST['username']) ? preg_replace('([^[:alnum:]_])', '', $_POST['username']) : '';
		$email = isset($_POST['email']) ? htmlentities($_POST['email'], ENT_QUOTES) : '';
		if(empty($username) || empty($email)) {
			return printOut(FAILURE, L_MISSING_RETRIEVE_FIELD);
		}
		$users = new Expanse('users');
		$user = $users->GetList(array(array('username', '=', $username), array('email', '=', $email)));

		if(empty($user)) {
			return printOut(FAILURE, L_CANT_RETRIEVE);
		}
		$users->Get($user[0]->id);
		$key = substr(md5(uniqid(microtime())), 0, 50);
		$users->reset_key = $key;
		$reset = new stdClass();
		$reset->request = true;
		$reset->expanseurl = EXPANSE_URL;
		$reset->company_url = COMPANY_URL;
		$reset->cms_name = CMS_NAME;
		$reset->user = $users->username;
		$reset->reset_key = $users->reset_key;
		$reset_url = EXPANSE_URL.'?action=forgot&amp;reset_key='.$users->reset_key;
		$reset->L_MAILER_REQUEST_TITLE = sprintf(L_MAILER_REQUEST_TITLE,$users->username);
		$reset->L_MAILER_REQUEST_BODY = sprintf(L_MAILER_REQUEST_BODY,$reset_url, $reset_url);
		$reset->L_MAILER_POWERED_BY = sprintf(L_MAILER_POWERED_BY,COMPANY_URL, CMS_NAME);
		$usersEmail = $users->email;
		require(dirname(__FILE__) . '/class.template.php');
		require(dirname(__FILE__) . '/class.phpmailer.php');
		$templatebody = sprintt($reset, dirname(__FILE__) . '/misc/@reset_pass_mailer.tpl.html');
		$plaintext = trim(strip_tags($templatebody));
		$fromaddr = $usersEmail;
		$fromname = CMS_NAME.' '.L_MAIL_PASSWORD_FROM;

		$mail = new PHPMailer();
		$mail->IsSendmail();
		$mail->SetFrom('noreply@expanse.io', CMS_NAME);
		$mail->AddReplyTo('noreply@expanse.io', CMS_NAME);
		$mail->AddAddress($usersEmail);
		$mail->Subject = CMS_NAME.' - '.L_MAIL_PASSWORD_REQUEST;
		$mail->MsgHTML($templatebody);
		$mail->AltBody = $plaintext;

		if(!$mail->Send()) {
			return printOut(FAILURE, L_PASSWORD_MAIL_FAILED);
		}
		if(!$users->Save()) {
			return printOut(FAILURE, L_PASSWORD_INSTRUCTIONS_NOT_SENT);
			applyOzoneAction('password_request_sent');
		}
		return printOut(SUCCESS, L_PASSWORD_INSTRUCTIONS_SENT);
	}

	function resetPassword() {
		$reset_key = preg_replace('([^[:alnum:]])', '', $_GET['reset_key']);
		$users = new Expanse('users');
		$user = $users->GetList(array(array('reset_key', '=', $reset_key)));
		if(empty($user)) {
			return printOut(FAILURE, L_PASSWORD_INVALID_KEY);
		}
		$users->Get($user[0]->id);
		$new_pass = substr(md5(uniqid(microtime())), 0, 6);
		$users->password = md5($new_pass);
		$users->reset_key = '';
		$reset->expanseurl = EXPANSE_URL;
		$reset->company_url = COMPANY_URL;
		$reset->cms_name = CMS_NAME;
		$reset->user = $users->username;
		$reset->new_pass = $new_pass;
		$reset->L_MAILER_CHANGED_TITLE = L_MAILER_CHANGED_TITLE;
		$reset->L_MAILER_CHANGED_BODY = sprintf(L_MAILER_CHANGED_BODY, $users->username);
		$reset->L_MAILER_CHANGED_DETAILS = sprintf(L_MAILER_CHANGED_DETAILS, $users->username, $new_pass);
		$reset->L_MAILER_CHANGED_DETAILS_TITLE = L_MAILER_CHANGED_DETAILS_TITLE;
		$reset->L_MAILER_POWERED_BY = sprintf(L_MAILER_POWERED_BY,COMPANY_URL, CMS_NAME);
		$usersEmail = $users->email;
		require(dirname(__FILE__) . '/class.template.php');
		require(dirname(__FILE__) . '/class.phpmailer.php');
		$templatebody = sprintt($reset, dirname(__FILE__) . '/misc/@reset_pass_mailer.tpl.html');
		$plaintext = trim(strip_tags($templatebody));
		$fromaddr = '<' . $usersEmail . '>';
		$fromname = CMS_NAME.' '.L_MAIL_PASSWORD_FROM;

		$mail = new PHPMailer();
		$mail->IsSendmail();
		$mail->SetFrom('noreply@expanse.io', CMS_NAME);
		$mail->AddReplyTo('noreply@expanse.io', CMS_NAME);
		$mail->AddAddress($usersEmail);
		$mail->Subject = CMS_NAME.' - '.L_MAIL_PASSWORD_CHANGED;
		$mail->MsgHTML($templatebody);
		$mail->AltBody = $plaintext;

		if(!$mail->Send()) {
			return printOut(FAILURE, L_PASSWORD_MAIL_FAILED);
		}
		if(!$users->Save()) {
			return printOut(FAILURE, L_PASSWORD_NOT_CHANGED);
			applyOzoneAction('password_changed');
		}
		return printOut(SUCCESS, L_PASSWORD_CHANGED);
	}
}

$auth = new userAuth;
