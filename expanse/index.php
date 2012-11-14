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
			www.alterform.com & www.dubtastic.com

****************************************************************/
require('funcs/admin.php');
if(LOGGED_IN) {
	if((ADDING || EDITING) && !empty($catid)) {
		$cats = $sections->GetList(array(array('id', '=', $catid),array('pid', '=', 0)));
		$more_cats = $sections->GetList(array(array('pid', '=', 0)));
		foreach($more_cats as $k => $v) {
			if(!in_array($v->id, $auth->Permissions) || ($v->id == $catid)) {
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
				if(is_posting(L_BUTTON_ADD)) {
					$the_module->add();
				}
			} else {
				/*   EDITING   //-------------------------------*/
				if(EDIT_SINGLE) {
					if(is_posting(L_BUTTON_EDIT)) {
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
$outmess->write_header($pagetitle);
?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="./"><?php echo CMS_NAME ?></a>
				<ul class="nav">
					<?php if(LOGGED_IN) { ?>
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Content
						<b class="caret"></b>
						</a>
						<?php echo $menu; ?>
						</li>
					<?php } ?>
				</ul>
				<?php
				if(!isset($is_install)) {
					$gravatar = (isset($_SESSION['email'])) ? md5( strtolower( trim( $_SESSION['email'] ) ) ) : ''; ?>
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					              <span class="gravatar"><img src="http://www.gravatar.com/avatar/<?php echo $gravatar; ?>?s=25" /></span>
									<span class="hidden-phone"><?php printf(L_WELCOME, HEADER_ID);?></span>
									<b class="caret"></b>
					            </a>
					            <?php $showAdminSettings = ($auth->Admin) ? '<li><a href="index.php?cat=admin">'. L_MENU_ADMIN_SETTINGS. '</a></li><li class="divider"></li>' : ''; ?>
					            <?php echo (LOGGED_IN == true) ? '
					            <ul class="dropdown-menu">'.
									$showAdminSettings
									.'<li><a href="'.YOUR_SITE.INDEX_PAGE.'" target="_blank">'.L_MENU_VIEW_SITE.'</a></li>
									<li><a href="index.php?action=logout" id="logoutLink">'.L_LOGOUT.'</a></li>
								</ul>' : ''; ?>
							</li>
						</ul>
					<p class="pull-right">
				<?php
				} else {
				?>
				<p>
				<?php
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
				}
				?>
				</p>
			</div>
		</div>
	</div>
<?php
if(LOGGED_IN) {
?>
	<div class="container<?php echo ' '.empty($the_type) ? ' mainPage' : ''; ?>">
		<form method="post" action="" enctype="multipart/form-data" id="post" name="post" class="form-stacked">
			<?php
			if(!(empty($catid) && empty($cat))) {
				echo $outmess->generateBreadCrumbs($catid, 'edit');
			}
			echo $output;
				if(ADDING && !empty($catid)) {
					include('add.php');
				} elseif(EDITING && !empty($catid)) {
					include('edit.php');
				}  elseif (CAT == 'admin' && $auth->Admin) {
					if (empty($admin_sub)) {
						echo $output;
					?>
						<div class="well">
							<ul id="catList" class="nav nav-tabs nav-stacked adminList">
								<?php
								applyOzoneAction('admin_menu_list', $admin_menu['details']);
								foreach($admin_menu['details'] as $menu_item) {
									?>
									<li id="<?php echo $menu_item['id']; ?>">
										<?php echo $menu_item['title']; ?>
									</li>
									<?php
								}
								applyOzoneAction('admin_menu'); ?>
							</ul>
						</div>
					<?php
					} elseif(!empty($admin_sub)) {
						applyOzoneAction('admin_page');
					}
				} elseif(is_home()) {
				?>
					<div class="row">
				<?php
					include('main.php');
				?>
					</div>
				<?php
				} elseif(CAT == 'upgrade') {
					include('upgrade.php');
				} else {
					echo L_NOTHING_HERE;
				}
				?>
	</div>
<?php
} else {
?>
	<div class="container login">
		<h1><?php printf(L_MAIN_TITLE,CMS_NAME); ?></h1>
		<form method="post" action="" id="post" name="post">
		<?php
		echo $output;
		if(isset($_GET['action']) && $_GET['action'] == 'forgot') {
			if(!isset($_GET['reset_key'])) {
			?>
				<div class="control-group">
					<label for="username" class="control-label"><?php echo L_LOGIN_USERNAME ?></label>
					<div class="controls">
						<input name="username" id="username" type="text" />
					</div>
				</div>
				<div class="control-group">
					<label for="email" class="control-label"><?php echo L_LOGIN_EMAIL ?></label>
					<div class="controls">
						<input name="email" id="email" type="text" />
					</div>
				</div>
				<div class="form-actions">
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
			<div class="control-group">
				<label for="username" class="control-label"><?php echo L_LOGIN_USERNAME ?></label>
				<div class="controls">
					<input name="username" type="text" id="username" value="<?php echo @$_POST['password'] ?>" />
				</div>
			</div>
			<div class="control-group">
				<label for="password" class="control-label"><?php echo L_LOGIN_PASSWORD ?></label>
				<div class="controls">
					<input name="password" type="password" id="password" />
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label for="rememberme" class="checkbox">
						<input name="rememberme" type="checkbox" id="rememberme" value="1" />
						<?php echo L_LOGIN_REMEMBER_ME ?>
					</label>
				</div>
			</div>
			<div class="form-actions">
				<input name="login" type="submit" id="login" value="Submit" class="btn btn-primary" />
				<a href="index.php?action=forgot" title="<?php echo L_LOGIN_FORGOT_PASSWORD ?>" id="forgotLink"><?php echo L_LOGIN_FORGOT_PASSWORD ?></a>
			</div>
		<?php
		} //Not resetting
?>
	</div>
<?php
}
?>
	</form>
<?php
$outmess->write_footer();
