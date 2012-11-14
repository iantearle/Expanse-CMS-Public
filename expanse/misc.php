<?php
/********* Expanse ***********/

require('funcs/admin.php');
//$pagetitle = $cmsname.L_MENU_SEPARATOR.$version.L_MENU_SEPARATOR;
$cat_action = ACTION;
		switch ($cat_action) {
		case "disabled":
		add_title(L_MISC_DISABLED_TITLE);
		   break;
		case "denied":
		add_title(L_MISC_NO_PERMISSIONS_TITLE);
		   break;
		   case "license":
		add_title('End User License Agreement');
		   break;
		default:
		add_title(L_MISC_NOTHING_HERE_TITLE);
		}
		$outmess->write_header('', 1);
 ?>
      <!-- Begin page content -->
      <div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="./"><?php echo CMS_NAME ?></a>
				<ul class="nav">
					<li><a href="./">Home</a></li>
					<?php if(LOGGED_IN) { ?>
					<li data-dropdown="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Content</a>
						<?php echo $menu; ?>
					</li>
					<?php } ?>
					<li><a href="<?php echo YOUR_SITE.INDEX_PAGE; ?>" target="_blank"><?php echo L_MENU_VIEW_SITE ?></a></li>
				</ul>
				<?php if(!$is_install) {
					$gravatar = md5( strtolower( trim( $_SESSION['email'] ) ) ); ?>
						<ul class="nav secondary-nav">
							<li data-dropdown="dropdown">
								<a class="dropdown-toggle dropdown-signin" data-toggle="dropdown" id="signin-link" href="#">
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
	</div>
	  <div class="errorPage container">
      <?php if ($cat_action == 'disabled'){ ?>
	  <?php printf(FAILURE,L_MISC_ACCOUNT_DISABLED) ?>
      <?php } elseif ($cat_action == 'denied'){ ?>
	  <?php printf(FAILURE, L_MISC_NO_PERMISSIONS);?>
            <?php } elseif($cat_action == 'license'){
			?>
			<h1>The License Agreement</h1>
			<p>Expanse is licensed under the <a href="http://opensource.org/licenses/mit-license.php"><span class="caps">MIT</span> open-source license</a>. That means the code is copyright Ryan Miglavs, but you have permission to do almost anything you like with it. <strong>It’s free, both as in free beer and free speech.</strong></p>
			<p>This includes using all or parts of the code in commercial applications. I request, but don’t require, that you give explicit credit and a link to expanse cms (<a href="http://expansecms.org">http://expansecms.org</a>), without using the expanse name or logo to advertise your product without written permission from the trademark owner (as specified by international trademark laws). You must, however, include the following license and notice with anything you distribute.</p>
			<div id="boilerplate">
				<p>Copyright (c) 2009 Ian Tearle (http://expansecms.org)</p>
				<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>
				<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>
				<p>THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUR OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
			</div>
			<?php
			} else {
				printf(FAILURE, L_MISC_NOTHING_HERE);
			} ?>
	  </div>
       <!-- End page content -->
 <?php $outmess->write_footer();?>