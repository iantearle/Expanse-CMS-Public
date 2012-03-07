<?php
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}
/*   Users   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=users&amp;action=edit">'.L_ADMIN_MANAGE_USERS.'</a>','', 'users');
global $admin_menu, $admin_sub;
if($admin_sub !== 'users'){return;}
$users = isset($users) && is_object($users) ? $users : new Expanse('users');
if(ACTION == 'add') {
	add_breadcrumb('<a href="index.php?cat=admin&sub=users&action=edit">'.L_USER_EDIT_TITLE.'</a>');
	add_breadcrumb(L_USER_ADD_TITLE);
} elseif(ACTION == 'edit') {
	if(empty($item_id)) {
		add_breadcrumb(L_USER_EDIT_TITLE);
		add_title(L_USER_EDIT_TITLE);
	} else {
		add_breadcrumb('<a href="index.php?cat=admin&sub=users&action=edit">'.L_USER_EDIT_TITLE.'</a>');
		add_title(L_USER_EDIT_TITLE);
		$users->Get($item_id);
		$bc_username = !empty($users->username) ? $users->username : '';
		add_breadcrumb(sprintf(L_CURRENTLY_EDITING, $bc_username));
		add_title(sprintf(L_CURRENTLY_EDITING, $bc_username));
	}
}
ozone_action('admin_page', 'users_content');

function users_content() {
	global $output, $auth, $item_id, $users;
	if (ACTION == 'add') {
		if (is_posting(L_BUTTON_ADD)) {
			$the_username = preg_replace('([^[:alnum:]_])', '',$_POST['username']);
			$the_displayname = preg_replace('([^[:alnum:]_[:space:].])', '',$_POST['displayname']);
			$url = htmlentities($_POST['url'], ENT_QUOTES);
			$password = md5(trim($_POST['password']));
			$confirmpassword = trim(md5($_POST['confirmpassword']));
			$email = htmlentities($_POST['email'], ENT_QUOTES);
			$admin = isset($_POST['admin']) ? 1: 0;
			$disabled = isset($_POST['disabled']) ? 1: 0;
			$section_admin = (int) $_POST['section_admin'];
			$created = isset($_POST['created']) ? $_POST['created'] : time();
			$users = get_dao('users');
			if(($password == $confirmpassword) && !empty($password) && strlen($password) >= 6) {
				if(isset($_POST['permissions']) && !empty($_POST['permissions'])) {
					if(!empty($the_username)) {
						$user = $users->GetList(array(array('username','=', $the_username)));
						if(empty($user)) {
							$users->username = $the_username;
							$users->displayname = $the_displayname;
							$users->url = $url;
							$users->password = $password;
							$users->email = $email;
							$users->permissions = is_array($_POST['permissions']) ? serialize($_POST['permissions']) : serialize(array());
							$users->admin = $admin;
							$users->disabled = $disabled;
							$users->created = $created;
							$users->section_admin = $section_admin;
							if($users->SaveNew()) {
								printOut(SUCCESS, vsprintf(L_USER_ADDED, array($users->username, $users->id)));
								$_POST = array();
							} else {
								printOut(FAILURE, vsprintf(L_USER_NOT_ADDED, array($users->username, mysql_error())));
							}
						} else {
						printOut(FAILURE,sprintf(L_USER_DUPLICATE_USERNAME, $user[0]->username));
						}
					} else {
						printOut(FAILURE, sprintf(L_MISSING_FIELDS, 'username'));
					}
				} else {
					printOut(FAILURE,L_USER_MISSING_PERMISSIONS);
				}
			} else {
				printOut(FAILURE, L_USER_INVALID_PASSWORD);
			}
		}
		$sections = new Expanse('sections');
		$cats = $sections->GetList(array(array('pid', '=', 0)));
		?>
		<div class="row">
			<?php echo $output; ?>
			<div class="span8">
				<p class="contentnote"><?php echo L_USER_PASSWORD_NOTE ?></p>
				<div class="clearfix">
					<label for="username"><?php echo L_USER_USERNAME ?></label>
					<div class="input">
						<input name="username" value="<?php echo view(@$_POST['username']) ?>" type="text" class="formfields" id="username" />
					</div>
				</div>
				<div class="clearfix">
					<label for="displayname"><?php echo L_USER_DISPLAYNAME ?></label>
					<div class="input">
						<input value="<?php echo view(@$_POST['displayname']) ?>" name="displayname" type="text" class="formfields" id="displayname" />
					</div>
				</div>
				<div class="clearfix">
					<label for="email"><?php echo L_USER_EMAIL ?></label>
					<div class="input">
						<input name="email" value="<?php echo view(@$_POST['email']) ?>" type="text" class="formfields" id="email" />
					</div>
				</div>
				<div class="clearfix">
					<label for="url"><?php echo L_USER_URL ?></label>
					<div class="input">
						<input name="url" value="<?php echo view(@$_POST['url']) ?>" type="text" class="formfields" id="url" />
					</div>
				</div>
				<div class="clearfix">
					<label for="password"><?php echo L_USER_PASSWORD ?></label>
					<div class="input">
						<input value="<?php echo view(@$_POST['password']) ?>" name="password" type="password" class="formfields" id="password" />
					</div>
				</div>
				<div class="clearfix">
					<label for="confirmpassword"><?php echo L_USER_CONFIRM_PASSWORD ?></label>
					<div class="input">
						<input value="<?php echo @$_POST['confirmpassword'] ?>" name="confirmpassword" type="password" class="formfields" id="confirmpassword" />
					</div>
				</div>
			</div>
			<div class="span8">
				<fieldset id="categoryBoxes">
					<legend><?php echo L_USER_PRIVILEGES ?></legend>
					<input type="hidden" name="permissions" value="" />
					<div class="clearfix">
						<label for="adminCheck" class="permBox">
							<span><?php echo L_USER_ADMIN ?></span>
							<input type="checkbox" name="admin" id="adminCheck" value="1" />
						</label>
					</div>
					<p id="unCheck">&nbsp;</p>

					<h3><?php echo L_USER_MODERATOR ?></h3>
					<div class="clearfix">
						<label id="section_admin_label" for="section_admin_admin">
							<input name="section_admin" type="radio" id="section_admin_admin" value="1" />
							<span><?php echo L_USER_MODERATOR_ADMIN ?></span>
						</label>
					</div>
					<div class="clearfix">
						<label id="section_user_label" for="section_admin_user">
							<input name="section_admin" type="radio" id="section_admin_user" checked="checked" value="0" />
							<span><?php echo L_USER_MODERATOR_USER ?></span>
						</label>
					</div>
					<?php
					foreach($cats as $s){?>
						<div class="clearfix">
							<label for="perm_<?php echo $s->sectionname ?>" class="permBox">
								<input type="checkbox" name="permissions[]" id="perm_<?php echo $s->sectionname ?>" value="<?php echo $s->id ?>" />
								<span><?php echo $s->sectionname ?></span>
							</label>
						</div>
					<?php } ?>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<fieldset>
					<legend><?php echo L_USER_DISABLE_ACCOUNT ?></legend>
					<p><?php echo L_USER_DISABLE_ACCOUNT_NOTE ?></p>
					<div class="clearfix">
						<label for="disabled" class="permBox">
							<input type="checkbox" name="disabled" id="disabled" value="1" />
							<span><?php echo L_USER_DISABLE_LABEL ?></span>
						</label>
					</div>
				</fieldset>
				<div class="actions">
					<input name="submit" type="submit" class="btn primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
				</div>
			</div>
		</div>
	<?php } elseif (ACTION == 'edit') {
		if (!empty($item_id)) {
			$users = get_dao('users');
			$users->Get($item_id);
			if (is_posting(L_BUTTON_EDIT)) {
				$the_username = preg_replace('([^[:alnum:]_])', '',$_POST['username']);
				$the_displayname = preg_replace('([^[:alnum:]_[:space:].])', '',$_POST['displayname']);
				$url = htmlentities($_POST['url'], ENT_QUOTES);
				$opass = trim($_POST['password']);
				$password = md5($opass);
				$confirmpassword = trim(md5($_POST['confirmpassword']));
				$email = htmlentities($_POST['email'], ENT_QUOTES);
				$admin = isset($_POST['admin']) ? 1: 0;
				$disabled = isset($_POST['disabled']) ? 1: 0;
				$section_admin = (int) $_POST['section_admin'];
				$created = isset($_POST['created']) ? $_POST['created'] : time();

				if(!empty($opass)){
					if(($password == $confirmpassword) && !empty($password) && strlen($password) >= 6){
						$passwordok = true;
					} else {
						$passwordok = false;
					}
				} else {
					$passwordok = true;
				}
				if($passwordok) {
					if(isset($_POST['permissions']) && !empty($_POST['permissions'])){
						if(!empty($the_username)){
							$user = $users->GetList(array(array('username','=', $the_username)));
							if(empty($user)  || $the_username == $users->username){
								$users->username = $the_username;
								$users->displayname = $the_displayname;
								$users->password =  ($passwordok && !empty($opass)) ? md5(trim($_POST['password'])) : $users->password;
								$users->email = $email;
								$users->url = $url;
								$users->section_admin = $section_admin;
								if($users->primary_user){
									$users->permissions = $users->permissions;
									$users->admin = 1;
									$users->disabled = 0;
								} else {
									$users->permissions = serialize($_POST['permissions']);
									$users->admin = $admin;
									$users->disabled = $disabled;
								}
								if($users->Save()){
									printOut(SUCCESS, sprintf(L_EDIT_SUCCESS, $users->username));
									$_POST = array();
								} else {
									printOut(FAILURE, vsprintf(L_EDIT_FAILURE, array($users->username,mysql_error())));
								}
							} else {
								printOut(FAILURE, sprintf(L_DUPLICATE_USERNAME, $user[0]->username));
							}
						} else {
							printOut(FAILURE, sprintf(L_MISSING_FIELDS, 'username'));
						}
					} else {
						printOut(FAILURE, L_USER_MISSING_PERMISSIONS);
					}
				} else {
					printOut(FAILURE, L_USER_INVALID_PASSWORD);
				}
			}
			$user = $users->Get($item_id);
			$sections = get_dao('sections');
			$cats = $sections->GetList(array(array('pid', '=', 0)));
			$hasitems = !empty($user) ? true : false; ?>
			<?php echo $output ;?>
			<?php if($user->primary_user) {?>
				<div class="alert-message info" data-alert="alert"><a class="close" href="#">×</a><p><?php echo L_USER_PRIMARY_NOTE ?></p></div>
			<?php }
			if($hasitems){
				$user->permissions = unserialize($user->permissions); ?>
				<div class="row">
					<div class="span8">
						<input type="hidden" value="1" id="edit_user" />
						<div class="clearfix">
							<label for="username"><?php echo L_USER_USERNAME ?></label>
							<div class="input">
								<input name="username" type="text" class="formfields" id="username" value="<?php echo $user->username; ?>" />
							</div>
						</div>
						<div class="clearfix">
							<label for="displayname"><?php echo L_USER_DISPLAYNAME ?></label>
							<div class="input">
								<input name="displayname" type="text" class="formfields" id="displayname" value="<?php echo $user->displayname; ?>" />
							</div>
						</div>
						<div class="clearfix">
							<label for="email"><?php echo L_USER_EMAIL ?></label>
							<div class="input">
								<input name="email" type="text" class="formfields" id="email" value="<?php echo $user->email; ?>" />
							</div>
						</div>
						<div class="clearfix">
							<label for="url"><?php echo L_USER_URL ?></label>
							<div class="input">
								<input name="url" value="<?php echo $user->url; ?>" type="text" class="formfields" id="url" size="30" />
							</div>
						</div>
						<div class="clearfix">
							<label for="password"><?php echo L_USER_NEW_PASSWORD ?></label>
							<div class="input">
								<input name="password" type="password" class="formfields" id="password" />
							</div>
						</div>
						<div class="clearfix">
							<label for="confirmpassword"><?php echo L_USER_CONFIRM_NEW_PASSWORD ?></label>
							<div class="input">
								<input name="confirmpassword" type="password" class="formfields" id="confirmpassword" />
							</div>
						</div>
						<p><?php echo L_USER_NEW_PASSWORD_NOTE ?></p>
					<?php if($user->primary_user) {?>
						<div class="hidden">
					<?php } ?>
						<fieldset>
							<legend><?php echo L_USER_DISABLE_ACCOUNT ?></legend>
							<p><?php echo L_USER_DISABLE_ACCOUNT_NOTE ?></p>
							<div class="clearfix">
								<label for="disabled" class="permBox">
									<input type="checkbox" name="disabled" id="disabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : ''; ?> value="1" />
									<span><?php echo L_USER_DISABLE_LABEL ?></span>
								</label>	
							</div>
						</fieldset>
					<?php if($user->primary_user) {?>
						</div>
					<?php } ?>
					</div>
					<div class="span8">
						<?php if($user->primary_user) {?>
							<div class="hidden">
						<?php } ?>
						<fieldset id="categoryBoxes">
							<legend><?php echo L_USER_PRIVILEGES ?></legend>
							<div class="clearfix">
								<input type="hidden" name="permissions" value="" />
								<label for="adminCheck" class="permBox">
									<input type="checkbox" name="admin" <?php echo $user->admin == 1 ? 'checked="checked"' : ''; ?> id="adminCheck" value="1" />
									<span><?php echo L_USER_ADMIN ?></span>
								</label>
							</div>
							<p id="unCheck">&nbsp;</p>
							<h3><?php echo L_USER_MODERATOR ?></h3>
							<div class="clearfix">
								<label id="section_admin_label" for="section_admin_admin">
									<input name="section_admin" type="radio" id="section_admin_admin" value="1" <?php echo $user->section_admin == 1 ? 'checked="checked"' : '' ?> />
									<span><?php echo L_USER_MODERATOR_ADMIN ?></span>
								</label>
							</div>
							<div class="clearfix">
								<label id="section_user_label" for="section_admin_user">
									<input <?php echo $user->section_admin == 0 ? 'checked="checked"' : '' ?> type="radio" name="section_admin" id="section_admin_user" value="0" />
									<span><?php echo L_USER_MODERATOR_USER ?></span>
								</label>
							</div>
							<?php foreach($cats as $s) { ?>
								<div class="clearfix">
									<label for="perm_<?php echo $s->sectionname ?>" class="permBox">
										<input type="checkbox" name="permissions[]" <?php echo in_array($s->id, $user->permissions) || $user->admin == 1 ? 'checked="checked"' : ''; ?> id="perm_<?php echo $s->sectionname ?>" value="<?php echo $s->id ?>" />
										<span><?php echo $s->sectionname ?></span>
									</label>
								</div>
							<?php } ?>
							<?php if($user->admin == 1) { 
								?><span id="noteText" class="formNote" style="visibility: visible; opacity: 1; "><?php echo L_JS_ADMIN_RIGHTS; ?></span><?php
							 }?>
						</fieldset>
					</div>
				</div>

				<?php if($user->primary_user) {?>
				</div>
				<?php } ?>
				<div class="actions">
					<input name="submit" type="submit" class="btn primary" id="submit" value="edit" />
				</div>
				<?php
			}
		} elseif (empty($item_id)) {
				$users = isset($users) && is_object($users) ? $users : new Expanse('users');
				if(is_posting(L_BUTTON_DELETE)){
					if(isset($_POST['del'])){
						foreach($_POST['del'] as $id){
							$us = new Expanse('users');
							$us->Get($id);
							$result = array();
							if(empty($us->username)){
								continue;
							}
							$the_username =  $us->username;
							if($us->primary_user != 1){
								if(deleteItem($id, 'users')){
									$result[] = sprintf('<li>'.L_USER_ACCOUNT_DELETED.'</li>', $the_username);
								} else {
									$result[] = sprintf('<li>'.L_USER_ACCOUNT_NOT_DELETED.'</li>', $the_username);
								}
							} else {
								$result[] = sprintf('<li>'.L_USER_ACCOUNT_PRIMARY_NOT_DELETED.'</li>', $the_username);
							}
						}
						if(!empty($result)){
							$result = '<ul>'.implode('', $result).'</ul>';
							printOut(SUCCESS, $result);
						}
					}
				}
				$userList = $users->GetList(array(array('id','>', 0)));
				$hasitems = !empty($userList) ? true : false;
				?>
				<?php echo $output ;?>
				<table id="users" class="bordered-table zebra-striped">
					<thead>
						<tr>
							<th>Username</th>
							<th>Action</th>
							<th><?php if($hasitems){ ?><label for="toggleBox"><input type="checkbox" id="toggleBox" value="" class /><span> <?php echo L_DELETE_ITEM ?></span></label><?php } ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach($userList as $ind => $user){
						$user->username = trim_title($user->username,L_USER_NO_USERNAME); ?>
						<tr<?php echo $ind % 2 ? ' class="altRow"' : ''; ?><?php echo $user->primary_user == 1 ? ' id="primaryUser"' : ''; ?>>
						<td><a href="<?php echo $_SERVER['REQUEST_URI'] ;?>&amp;id=<?php echo $user->id; ?>"  title=" <?php echo $user->username; ?>" class="<?php echo $user->primary_user != 1 ? 'user' : 'primary'; ?>"><?php echo $user->username; ?></a></td>
						<td><a href="<?php echo $_SERVER['REQUEST_URI'] ;?>&amp;id=<?php echo $user->id; ?>" title=" Edit <?php echo $user->username; ?>" class="editLink"><?php echo L_USER_EDIT_TEXT ?></a></td>
						<td>
							<div class="clearfix">
								<label for="del<?php echo $user->id; ?>"></label>
								<div class="input">
									<input id="del<?php echo $user->id; ?>" name="del[]" type="checkbox" value="<?php echo $user->id; ?>"<?php if($user->primary_user == 1){ ?>disabled="disabled" title="<?php printf(strip_tags(L_USER_ACCOUNT_PRIMARY_NOT_DELETED), $user->username); ?>"<?php } ?> />
								</div>
							</div>
						</td>
					</tr>
					<?php
					} //End While
					?>
					</tbody>
				</table>
				<div class="actions">
					<a href="index.php?cat=admin&sub=users&action=add" class="btn success"><?php echo L_USER_ADD_TITLE ?></a>
					<?php if(count($userList) > 1){?>
					<div class="pull-right">
						<input name="submit" id="submit" type="submit" class="btn danger" value="<?php echo L_BUTTON_DELETE ?>" />
					</div>
					<?php } ?>	
				</div>
				<?php
		} //end isset item id
	}
}
?>