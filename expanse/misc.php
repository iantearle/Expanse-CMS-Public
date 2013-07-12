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

require('funcs/admin.php');

//$pagetitle = $cmsname.L_MENU_SEPARATOR.$version.L_MENU_SEPARATOR;
$cat_action = ACTION;
switch($cat_action) {
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
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="./"><?php echo CMS_NAME ?></a>
				<ul class="nav">
					<?php
					if(LOGGED_IN) {
					?>
						<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Content <b class="caret"></b></a>
							<?php echo $menu; ?>
						</li>
					<?php
					}
					?>
				</ul>
				<?php
				if(!isset($is_install)) {
					if(LOGGED_IN) {
						$gravatar = (isset($_SESSION['email'])) ? md5( strtolower( trim( $_SESSION['email'] ) ) ) : ''; ?>
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					              <span class="gravatar"><img src="http://www.gravatar.com/avatar/<?php echo $gravatar; ?>?s=25" /></span>
									<span class="hidden-phone"><?php printf(L_WELCOME, HEADER_ID);?></span>
									<b class="caret"></b>
					            </a>
					            <ul class="dropdown-menu">
					            	<?php
					            	echo ($auth->Admin) ? '<li><a href="index.php?cat=admin">'. L_MENU_ADMIN_SETTINGS. '</a></li><li class="divider"></li>' : '';
					            	?>
									<li><a href="<?php echo YOUR_SITE.INDEX_PAGE; ?>" target="_blank"><?php echo L_MENU_VIEW_SITE; ?></a></li>
									<li><a href="index.php?action=logout" id="logoutLink"><?php echo L_LOGOUT; ?></a></li>
								</ul>
							</li>
						</ul>
					<?php
					}
				} else {
					$install_step = check_get_alphanum('step');
					$uninstall_type = check_get_alphanum('uninstall_type');
					if($install_step == 'install') {
						$step ='<p>Installing '.CMS_NAME.'</p>';
					} elseif($install_step == 'uninstall') {
						$step = '<p>Uninstalling '.CMS_NAME.'</p>';
						$step .= ($uninstall_type == 'manual') ? ': Manually' : '';
					} else {
						$step = '<p>'.CMS_NAME.' wants to know what you\'re doing</p>';
					}
					echo $step;
				} ?>
			</div>
		</div>
	</div>

<?php
if($cat_action == 'disabled') {
	printf(FAILURE,L_MISC_ACCOUNT_DISABLED);
} elseif ($cat_action == 'denied') {
	printf(FAILURE, L_MISC_NO_PERMISSIONS);
} elseif($cat_action == 'license') {
?>
	<div class="errorPage container mainPage">
		<div class="page-header">
			<h1>The License Agreement</h1>
		</div>
		<p>Copyright &copy; 2012 Ian Tearle</p>
		<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>
		<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>
		<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
	</div>
<?php
} else {
	printf(FAILURE, L_MISC_NOTHING_HERE);
}

$outmess->write_footer();
