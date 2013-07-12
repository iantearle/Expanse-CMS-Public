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

class outputMessages {
	function outputMessages() {

	}
	function printOut($message, $more="") {
		global $output, $report;
		if(is_array($message)) {
			foreach($report as $level => $reports) {
				$reports[$level] = array_unique($report[$level]);

				foreach($report['error'] as $val) {
					$output .= '<div class="alert alert-block alert-error fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'.$val.'</p></div>';
				}
				foreach($report['success'] as $val) {
					$output .= '<div class="alert alert-block alert-success fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'.$val.'</p></div>';
				}
				foreach($report['alert'] as $val) {
					$output .= '<div class="alert alert-block alert-warning fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'.$val.'</p></div>';
				}
			}

		} else {
			if(empty($more)) {
				$output .= $message;
			} elseif(is_array($more)) {
				$output .= vsprintf($message, $more);
			} else {
				$output .= sprintf($message, $more);
			}
		}
	}

	/**
	 * Writes the header
	 * @param string $pagetitle
	 * @param int $is_vanilla
	 * @param int $is_install
	 * @return void
	 */
	function write_header($pagetitle, $is_vanilla=0, $is_install=0) {
		global $auth, $xajax, $sections, $items, $module_css, $module_js, $page_meta, $headerDate, $cat_id, $cat;
		$finaltitle = CMS_NAME.L_MENU_SEPARATOR.CMS_VERSION.' ';
		add_title($finaltitle, 1);
		$default = L_DEFAULT_TITLE;
		$type = check_get_alphanum('type');
		if(!$is_install) {
			switch (true) {
				case ADDING:
				case EDITING:
					break;
				case CAT == 'admin':
					add_title(L_MENU_ADMIN_SETTINGS, 2);
					break;
				case !ADDING && !EDITING && CAT != 'admin' && empty($type) && LOGGED_IN:
					add_title(sprintf(L_MAIN_TITLE,CMS_NAME), 2);
					break;
				case !LOGGED_IN:
					add_title(sprintf(L_LOGIN_TITLE,CMS_NAME), 2);
					break;
				default:
					add_title($default, 2);
					break;
			}
		}
		if($is_vanilla && $is_install) {
			reset_title();
			add_title($finaltitle, 1);
			add_title('Installing Expanse :: The CMS for Creative People.', 2);
		}
		$finaltitle = make_title();
		$finaltitle = applyOzone('admin_title',$finaltitle);
		$main_css = 'css/expanse.css.php'.($is_install ? '?extend=install': '');
		$main_css = applyOzoneAction('admin_css_url', $main_css);
		$main_js = 'javascript/expanse.js.php'.(isset($_GET['cat_id']) ? '?full=true' : '');
		$main_js = applyOzoneAction('admin_js_url', $main_js);
		?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $finaltitle;?></title>
	<meta name="copyright" content="Little Polar Apps Ltd" />
	<meta name="language" content="EN-US" />
	<meta name="rating" content="General" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="7" />
	<meta name="distribution" content="global" />
	<meta name="author" content="Ian Tearle, Nate Cavanaugh, Jason Morrison" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo $main_css ?>" />
	<?php echo (!empty($module_css)) ? '<link rel="stylesheet" type="text/css" href="'.$module_css.'" />' : ''; ?>
<<<<<<< HEAD:expanse/funcs/class.output.php
	<link rel="stylesheet" href="javascript/redactor/redactor.css" />
	<script type="text/javascript">document.write('<link rel="stylesheet" type="text/css" href="css/expanse.js.css" />');</script>
	<script type="text/javascript" src="javascript/modernizr.min.js"></script>
	<?php
=======
	<script type="text/javascript">document.write('<link rel="stylesheet" type="text/css" href="css/expanse.js.css" />');</script>
	<script type="text/javascript" src="javascript/modernizr.min.js"></script>
	<script type="text/javascript" src="<?php echo $main_js ?>"></script>
	<?php
	echo (!empty($module_js)) ? '<script type="text/javascript" src="'.$module_js.'"></script>' : '';
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c:expanse/funcs/output.class.php
	if(isset($xajax) && is_object($xajax)) { $xajax->printJavascript("./funcs/"); }
	$company_logo = COMPANY_LOGO;
	if(!empty($company_logo) && CUSTOM_INSTALL) { ?>
		<style>
			#header h1 a, #header h1 a:hover{
			background: url(<?php echo $company_logo; ?>) no-repeat;
			}
		</style>
	<?php  }
	applyOzoneAction('admin_header');
	?>
</head>
<body<?php if($is_vanilla){ ?> id="vanilla"<?php } if(isset($_GET['cat'])) { echo ' class="'. dirify($_GET['cat']) .'"'; } else { echo ' class="build"';} ?>>
	<div id="wrap">
		<?php
		if(!$is_install) {
			if(PENDING_UPDATE && CAT != 'upgrade' && LOGGED_IN) {
				echo '<div id="upgradeOverlay"></div>';
				echo '<div class="container mainPage"><div class="alert alert-block block-message fade in upgradeInstructions" data-alert="alert"><a class="close" href="#" data-dismiss="alert">&times;</a><p>'.sprintf(L_UPGRADE_INSTRUCTIONS,CMS_NAME, CMS_VERSION).'</p></div></div>';
			}
		}
	}

	/**
	 * Creates the breadcrumbs HTML
	 * @param string $section
	 * @param $type
	 * @return void
	 */
	function generateBreadCrumbs($section, $type) {
		global $Database, $admin_menu;
		$item_id = ITEM_ID;
		$separator = L_SEPARATOR;
		$sections = get_dao('sections');
		$sections->Get($section);
		$section = $sections->sectionname;
		$category_header = $section;
		add_breadcrumb('<a href="./">'.L_CRUMB_HOME.'</a>', 1);
		if(ADDING) {
			add_breadcrumb('<a href="index.php?type=edit&amp;cat_id='.$sections->id.'">'.sprintf(L_CRUMB_EDIT,ucwords($section)).'</a>', 2);
			add_breadcrumb(sprintf(L_CRUMB_ADD,ucwords($section)), 2);
		} elseif(EDITING) {
			if(!empty($section)) {
				if(EDIT_LIST) {
					add_breadcrumb(sprintf(L_CRUMB_EDIT,ucwords($section)), 2);
				} else {
					global $the_module;
					$items = $the_module->items;
					$title = !empty($items->title) ? $items->title : L_NO_TEXT_IN_TITLE;
					add_breadcrumb('<a href="index.php?type=edit&amp;cat_id='.$sections->id.'">'.sprintf(L_CRUMB_EDIT,ucwords($section)).'</a>', 2);
					add_breadcrumb(sprintf(L_CURRENTLY_EDITING_HTML,$title), 3);
				}
			}
		} elseif(CAT == 'admin') {
			$category_header = L_MENU_ADMIN_SETTINGS;
			$admin_sub = ADMIN_SUB;
			if(empty($admin_sub)) {
				add_breadcrumb(L_CRUMB_EDIT_ADMIN, 2);
			} else {
				add_breadcrumb('<a href="index.php?cat=admin">'.L_CRUMB_EDIT_ADMIN.'</a>', 2);
			}
		} elseif(CAT == 'upgrade') {
			$category_header = L_UPDATE_TITLE;
			add_breadcrumb(sprintf(L_UPDATE_CRUMB,CMS_NAME), 2);
		}
		$category_header = applyOzoneAction('category_header', $category_header);
		$breadcrumbs = make_breadcrumbs(); ?>
		<ul class="breadcrumb">
			<li><?php echo $breadcrumbs ?></li>
		</ul>
		<?php
	}

	/**
	 * Writes a footer
	 * @return void
	 */
	function write_footer() {
		global $auth, $module_js;
		$is_admin = (isset($auth) && is_object($auth) && $auth->Admin == true);
		$upgradable = (defined('UPGRADE_AVAILABLE') && UPGRADE_AVAILABLE == true);
		$show_label = (isset($_SESSION['username']) && $upgradable && $is_admin);
		?>
		<div id="push"></div>
	</div>
	<footer class="footer">
		<div class="container">
			<div class="row-fluid">
				<p><a href="<?php echo COMPANY_URL ?>" target="_blank"><?php echo COMPANY_NAME ?></a>. <?php printf(L_COPYRIGHT_FOOTER, date('Y'));?><?php if(!CUSTOM_INSTALL){ ?><?php echo L_MENU_SEPARATOR ?><a href="misc.php?action=license"><?php echo L_LEGAL_FOOTER ?></a><?php echo L_MENU_SEPARATOR ?><a href="http://expanse.io/forums" target="_blank"><?php echo L_SUPPORT_FOOTER ?></a><?php } ?></p>
			</div>
		</div>
	</footer>
		<?php
		if($show_label) {
			?>
<<<<<<< HEAD:expanse/funcs/class.output.php
			<p id="upgrade"><a href="http://expanse.io/download/" target="_blank"><?php printf(L_UPGRADE_AVAILABLE, UPGRADE_VERSION); ?></a></p>
=======
			<p id="upgrade"><a href="http://expansecms.org/download/" target="_blank"><?php printf(L_UPGRADE_AVAILABLE, UPGRADE_VERSION); ?></a></p>
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c:expanse/funcs/output.class.php
			<?php
		}
		global $LEX_JS;
		echo '<div class="hide">';
		echo is_array($LEX_JS) ? implode("\n",$LEX_JS) : '';
		echo '</div>';
		applyOzoneAction('admin_footer');
		?>
<<<<<<< HEAD:expanse/funcs/class.output.php
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $main_js ?>"></script>
	<script src="javascript/redactor/redactor.js"></script>
	<?php echo (!empty($module_js)) ? '<script type="text/javascript" src="'.$module_js.'"></script>': ''; ?>
=======
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c:expanse/funcs/output.class.php
</body>
</html>
		<?php
	}
}
