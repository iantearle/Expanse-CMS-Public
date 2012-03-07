<?php
/********* Expanse ***********/
require('funcs/admin.php');
if(LOGGED_IN) {
	if((ADDING || EDITING) && !empty($catid)) {
			$cats = $sections->GetList(array(array('id', '=', $catid),array('pid', '=', 0)));
			$more_cats = $sections->GetList(array(array('pid', '=', 0)));
			foreach($more_cats as $k => $v) {
				if(!in_array($v->id, $auth->Permissions) || ($v->id == $catid)){
					unset($more_cats[$k]);
				}
			}
			$cat_type = !empty($cats) ? $cats[0]->cat_type : NULL;
			$category_clean_name = !empty($cats) ? $cats[0]->dirtitle : '';
			define('CLEAN_CAT_NAME', $category_clean_name);
			$cats = getCatList($catid);
			if(file_exists("$modules_dir/$cat_type/controller.php") && file_exists("$modules_dir/$cat_type/view.php") && !is_null($cat_type)) {
				$module_exists = true;
				$module_css = file_exists("$modules_dir/$cat_type/styles.css") ? EXPANSE_URL.'modules/'.$cat_type.'/styles.css' : '';
				$module_js = file_exists("$modules_dir/$cat_type/javascript.js") ? EXPANSE_URL.'modules/'.$cat_type.'/javascript.js' : '';
				include("$modules_dir/$cat_type/controller.php");
				$the_module = (class_exists($cat_type)) ? new $cat_type : new Module;
				$errors =& $the_module->errors;
				$the_module->more();
				if(ADDING) {
					/*   ADDING   //-------------------------------*/
					if(is_posting(L_BUTTON_ADD)){
						$the_module->add();
					}
				} else {
					/*   EDITING   //-------------------------------*/
					if(EDIT_SINGLE){
						if(is_posting(L_BUTTON_EDIT)){
							$the_module->edit();
							}
							$items = $the_module->get_single();
						} elseif(EDIT_LIST) {
							if(is_posting(L_BUTTON_DELETE)){
							$the_module->delete();
							}
							$itemsList = $the_module->get_list();
						}
				}
				$the_module->add_title();
			}
	} // end adding or editing
}
$wordwrap = 75;
$descrlength = 80;
/*   Header   //-------------------------------*/
$outmess->write_header($pagetitle); ?>
	<div class="topbar">
		<div class="topbar-inner">
			<div class="container">
				<a class="brand" href="./"><?php echo CMS_NAME ?></a>
				<ul class="nav">
					<li><a href="./">Home</a></li>
					<?php if(LOGGED_IN) { ?>
					<li data-dropdown="dropdown">
						<a class="dropdown-toggle" href="#">Content</a>
						<?php echo $menu; ?>
					</li>
					<?php } ?>
					<li><a href="<?php echo YOUR_SITE.INDEX_PAGE; ?>" target="_blank"><?php echo L_MENU_VIEW_SITE ?></a></li>
				</ul>
				<?php if(!$is_install) {
					$gravatar = md5( strtolower( trim( $_SESSION['email'] ) ) ); ?>
						<ul class="nav secondary-nav">
							<li data-dropdown="dropdown">
								<a class="dropdown-toggle dropdown-signin" id="signin-link" href="#">
					              <span class="gravatar"><img src="http://www.gravatar.com/avatar/<?php echo $gravatar; ?>?s=25" /></span>
									<?php printf(L_WELCOME, HEADER_ID);?>
					            </a>
					            <?php $showAdminSettings = ($auth->Admin) ? '<li><a href="index.php?cat=admin">'. L_MENU_ADMIN_SETTINGS. '</a></li><li class="divider"></li>' : ''; ?>
					            <?php echo (LOGGED_IN == true) ? '
					            <ul class="dropdown-menu">'.
									$showAdminSettings
									.'<li><a href="index.php?action=logout" id="logoutLink">'.L_LOGOUT.'</a></li>
								</ul>' : ''; ?>
							</li>
						</ul>
					<p class="pull-right">
				<?php } else {
					?><p><?php
					$install_step = check_get_alphanum('step');
					$uninstall_type = check_get_alphanum('uninstall_type');
					if($install_step == 'install'){
						$step ='Installing '.CMS_NAME;
					} elseif($install_step == 'uninstall') {
						$step = 'Uninstalling '.CMS_NAME;
						$step .= ($uninstall_type == 'manual') ? ': Manually' : '';
					} else {
						$step = CMS_NAME.' wants to know what you\'re doing';
					}
					echo $step;
				} ?>
				<?php //echo isset($headerDate) ? $headerDate : ''; ?></p>
			</div>
		</div>
	</div> <?php
if(LOGGED_IN) { ?>
	<div class="container<?php echo ' '.empty($the_type) ? ' mainPage' : ''; ?>">
		<form method="post" action="" enctype="multipart/form-data" id="post" name="post" class="form-stacked">
			<?php if(!(empty($catid) && empty($cat))){ ?>
				<?php echo $outmess->generateBreadCrumbs($catid, 'edit');
			}
			echo $output;
				if(ADDING && !empty($catid)){
					include('add.php');
				} elseif(EDITING && !empty($catid)){
					include('edit.php');
				}  elseif (CAT == 'admin' && $auth->Admin){

					if (empty($admin_sub)){ ?>
						<?php echo $output; ?>
						<div class="well">
							<ul id="catList" class="adminList">
								<?php
								applyOzoneAction('admin_menu_list', $admin_menu['details']);
								foreach($admin_menu['details'] as $menu_item) {
									?>
									<li id="<?php echo $menu_item['id']; ?>">
										<h3><?php echo $menu_item['title']; ?></h3>
										<?php echo $menu_item['links']; ?>
										<p><?php echo $menu_item['description']; ?></p>
									</li>
									<?php
								}
								applyOzoneAction('admin_menu'); ?>
							</ul>
						</div>
						<?php
					} elseif (!empty($admin_sub)) {
						applyOzoneAction('admin_page');
					}
				} elseif(is_home()) {
					?><div class="row"><?php
					include('main.php');
					?></div><?php
				} elseif(CAT == 'upgrade') {
					include('upgrade.php');
				} else { ?>
					<p><?php echo L_NOTHING_HERE ?></p><?php
				} ?>
	</div> <?php
} else { ?>
	<div class="container login">
		<h1><?php printf(L_MAIN_TITLE,CMS_NAME); ?></h1>
		<form method="post" action="" id="post" name="post">
		<?php
		echo $output;
		if(isset($_GET['action']) && $_GET['action'] == 'forgot') {
			if(!isset($_GET['reset_key'])) {
				?>
				<div class="clearfix">
					<label for="username"><?php echo L_LOGIN_USERNAME ?></label>
					<div class="input">
						<input name="username" id="username" type="text" />
					</div>
				</div>
				<div class="clearfix">
					<label for="email"><?php echo L_LOGIN_EMAIL ?></label>
					<div class="input">
						<input name="email" id="email" type="text" />
					</div>
				</div>
				<div class="actions">
					<input name="get_password" id="get_password" type="submit" class="btn error" value="<?php echo L_BUTTON_GET_INFO ?>" />
				</div>
				<?php
			} else {
				?>
				<a href="./"><?php echo L_LOGIN_GO_BACK ?></a>
				<?php
			}
		} else {
			?>
			<h3><?php echo L_GET_STARTED ?></h3>
			<div class="clearfix">
				<label for="username"><?php echo L_LOGIN_USERNAME ?></label>
				<div class="input">
					<input name="username" type="text" id="username" value="<?php echo @$_POST['password'] ?>" />
				</div>
			</div>
			<div class="clearfix">
				<label for="password"><?php echo L_LOGIN_PASSWORD ?></label>
				<div class="input">
					<input name="password" type="password" id="password" />
				</div>
			</div>
			<div class="clearfix">
			<label></label>
				<div class="input">
					<ul class="inputs-list">
		                <li>
							<label for="rememberme">
								<input name="rememberme" type="checkbox" id="rememberme" value="1" />
								<?php echo L_LOGIN_REMEMBER_ME ?>
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="actions">
				<input name="login" type="submit" id="login" value="Submit" class="btn primary" />
				<a href="index.php?action=forgot" title="<?php echo L_LOGIN_FORGOT_PASSWORD ?>" id="forgotLink">
				<?php echo L_LOGIN_FORGOT_PASSWORD ?></a><br />
			</div> <?php
		} //Not resetting ?>
	</div> <?php
} ?>
	</form>
<?php $outmess->write_footer(); ?>
