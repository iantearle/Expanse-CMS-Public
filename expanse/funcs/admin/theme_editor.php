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

if(!defined('EXPANSE')) { die('Sorry, but this file cannot be directly viewed.'); }

/*   Themes   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=theme_editor">'.L_ADMIN_EDIT_THEMES.'</a>',array(),'themeEditor');
if($admin_sub !== 'theme_editor') { return; }
$themename = check_get_alphanum('theme');
if(!empty($themename)) {
	$theme = !empty($themename) ? THEMES.'/'.$themename : '';
	$theme = str_replace($special_chars, '', $theme);
	$theme = realpath($theme);
	$themes = getFiles($themesdir, 'dirs');
	if(!empty($theme) && is_dir($theme) && basename($theme) != 'themes' && in_array($themename, $themes)) {
		$theme_css_file = file_exists($theme.'/css/'.$themename.'.css') ? $theme.'/css/'.$themename.'.css' : $theme.'/css/styles.css';
		$theme_info = theme_info($theme_css_file);
		$crumb = has_theme_info($theme_info) ? $theme_info->Title : $themename;
		$crumb = sprintf(L_CURRENTLY_EDITING, $crumb);
		add_breadcrumb('<a href="index.php?cat=admin&sub=theme_editor">'.L_THEME_EDITOR_TITLE.'</a>');
		add_title(L_THEME_EDITOR_TITLE);
		add_title($crumb);
		add_breadcrumb($crumb);
	}
} else {
	add_breadcrumb(L_THEME_EDITOR_TITLE);
	add_title(L_THEME_EDITOR_TITLE);
}
ozone_action('admin_page', 'theme_content');

function theme_content() {
	global $output, $special_chars, $themesdir, $filearr, $auth;
	if(is_posting(L_BUTTON_ACTIVATE) && isset($_POST['activate_this']) && !empty($_POST['activate_this'])) {
		$activate_theme = preg_replace('([^[:alnum:]_[:space:]])', '',  trim($_POST['activate_this']));
		if(setOption('theme',$activate_theme)) {
			printOut(SUCCESS, L_THEME_ACTIVATE_SUCCESS);
		} else {
			printOut(FAILURE, L_THEME_ACTIVATE_FAILURE);
		}
	}
	if(isset($_POST['new_submit']) && !empty($_POST['new_theme_name'])) {
		$new_theme_name = $proper_theme_name = preg_replace('([^[:alnum:]_[:space:]])', '',  $_POST['new_theme_name']);
		$new_theme_name = str_replace(' ', '_',  $new_theme_name);
		$errors = array();
		$tplext = TPL_EXT;
		$expanse_dir = EXPANSEPATH;
		$theme_dir = "$expanse_dir/themes/$new_theme_name";
		$yoursite = YOUR_SITE;
		if(!file_exists($theme_dir)) {
			copyr("$expanse_dir/funcs/misc/theme_prototype/", $theme_dir);
			$style_sheet = file_get_contents("$theme_dir/css/styles.css");
			$users = new Expanse('users');
			$users->Get($auth->Id);
			$style_sheet = str_replace('/*__CSS_META__*/',
'/*
Theme Name: '.$proper_theme_name.'
Theme URL: '.$yoursite.'
Description: Courteously provided by your friends at '.COMPANY_NAME.' We heart your face.
Version: 1.0
Author: '.((!empty($auth->DisplayName)) ? $auth->DisplayName : $auth->Username).'
Author URL: '.((isset($users->url) && !empty($users->url)) ? $users->url : $yoursite).'
*/', $style_sheet);
			$fp = fopen("$theme_dir/css/styles.css", 'w+');
			fwrite($fp, $style_sheet);
			fclose($fp);
			if(empty($errors)) {
				printOut(SUCCESS,L_THEME_CREATED);
			} else {
				printOut(FAILURE,L_THEME_FAILURE);
			}
		} else {
			printOut(FAILURE,L_THEME_FAILURE);
		}
	}
	$themename = isset($_GET['theme']) ? $_GET['theme'] : '';
	$themename = str_replace($special_chars, '', $themename);
	$themename = str_replace('.', '', $themename);
	$themes = getFiles($themesdir, 'dirs');
	$theme = !empty($themename) ? EXPANSEPATH.'/'.$themesdir.'/'.$themename : '';
	$theme = realpath($theme);
	$themefile = isset($_GET['themefile']) ? $_GET['themefile'] : '';
	$themefile = str_replace($special_chars, '', $themefile);
	$writable = is_writable($theme) ? true : false;
	if(!empty($theme) && is_dir($theme) && basename($theme) != 'themes' && in_array($themename, $themes)) {
		if(!$writable) {
			printOut(ALERT,L_THEME_NO_PERMISSIONS);
		}
		if($writable) {
			if(is_posting(L_BUTTON_UPDATE)) {
				$filename =(!empty($themefile) && is_file($theme.'/'.$themefile)) ? $theme.'/'.$themefile : "$theme/css/$themename.css";
				$file_contents = $_POST['file_contents'];
				$file_contents = applyOzoneAction('theme_update_file', $file_contents);
				if(is_file($filename)) {
					$fp = fopen($filename,'wb');
					if(fwrite($fp,$file_contents)) {
						printOut(SUCCESS, L_THEME_FILE_UPDATED);
					}
				}
				fclose($fp);
			}
			if(is_posting(L_BUTTON_CREATE)) {
				$create_in = isset($_POST['create_in']) && $_POST['create_in'] != 'main' ? $theme.'/'.$_POST['create_in']: $theme;
				$create_in_base = isset($_POST['create_in']) && $_POST['create_in'] == 'main' ? '' : $_POST['create_in'].'%2F';//
				$filename = isset($_POST['filename']) ? trim($_POST['filename']) : '';
				$filename = str_replace($special_chars, '', $filename);
				$filename = str_replace('/', '', $filename);
				$filename = $filename != '.' && $filename != '..' ? $filename : '';
				$file_contents = sprintf(L_THEME_FILE_CREATE_NOTE, $filename, date('Y'));
				$create_in = str_replace($special_chars, '', $create_in);
				$filepath = $create_in.'/'.$filename; /*echo  $create_in_base;*/
				if(!empty($filename)) {
					if(!file_exists($filepath) && !is_file($filepath)){
						$fp = fopen($filepath,'w+');
						if(fwrite($fp,$file_contents)) {
							printOut(SUCCESS, sprintf(L_THEME_FILE_CREATED,$themename,$create_in_base.$filename));
						}
						fclose($fp);
					} else {
						printOut(FAILURE, L_THEME_EXISTING_FILE);
					}
				} else {
					printOut(FAILURE, L_THEME_MISSING_FILENAME);
				}
			}
		}
		$themes = getFiles($themesdir, 'all', 1);
		?>
		</form>
		<div id="themeEditor">
			<?php echo $output ;?>
			<div class="accordion" id="stretchContainer">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#stretchContainer" href="#themeEditorStretch"><?php echo L_THEME_CREATE_NEW_FILE ?></a>
					</div>
					<div id="themeEditorStretch" class="accordion-body collapse">
						<div class="accordion-inner">
							<form action="" method="post" class="form-horizontal">
								<div class="row">
									<div class="span5">
										<div class="control-group">
											<label for="create_in" class="control-label"><?php echo L_THEME_CREATE_IN_FOLDER ?></label>
											<div class="controls">
												<select name="create_in" id="create_in" class="infields">
													<option value="main"><?php echo $themename; ?></option> <?php
													foreach($themes['dirs'][$themename]['dirs'] as $k => $v) {
														if(!in_array($k, $blocked_dirs)){ ?>
															<option value="<?php echo $k ?>">&mdash; <?php echo $k ?></option>
															<?php
														}
													} ?>
												</select>
											</div>
										</div>
									</div>
									<div class="span5">
										<div class="control-group">
											<label for="filename" class="control-label"><?php echo L_THEME_CREATE_FILENAME ?></label>
											<div class="controls">
												<input type="text" value="" id="filename" name="filename" class="" />
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<input id="submit" type="submit" name="submit" value="<?php echo L_BUTTON_CREATE ?>" class="btn btn-primary pull-right" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<form method="get" action="" class="form-horizontal">
							<input name="cat" type="hidden" value="admin" />
							<input name="sub" type="hidden" value="theme_editor" />
							<div class="row">
								<div class="span6">
									<div class="well">
										<div class="control-group">
											<label for="theme" class="control-label"><?php echo L_THEME_SELECT_THEME ?></label>
											<div class="controls">
												<select name="theme" id="theme"> <?php
													foreach($themes['dirs'] as $k => $val){
														if($k != $themename){ ?>
															<option value="<?php echo $k ?>"><?php echo str_replace('_', ' ', $k); ?></option> <?php
														} else { ?>
															<option selected="selected" value="<?php echo $k ?>"><?php echo str_replace('_', ' ', $k); ?></option> <?php
														}
													} ?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="span6">
									<div class="well">
										<div class="control-group">
											<label for="themefile" class="control-label"><?php echo L_THEME_SELECT_FILE ?></label>
											<div class="controls">
												<select id="themefile" name="themefile"> <?php
													ksort($themes['dirs'][$themename]['dirs']);
													foreach($themes['dirs'] as $ind => $val) {
														if($ind == $themename){
															foreach($val['dirs'] as $k => $v) {
																if(!in_array($k, $blocked_dirs)) { ?>
																	<optgroup label="<?php echo $k; ?>"> <?php
																	asort($v['files']);
																	foreach($v['files'] as $files) {
																		if($themefile != $k.'/'.$files) { ?>
																			<option value="<?php echo $k.'/'.$files; ?>"><?php echo $files; ?></option> <?php
																		} else { ?>
																			<option selected="selected" value="<?php echo $k.'/'.$files; ?>" class="current">&raquo; <?php echo $files; ?></option> <?php
																		}
																	} ?>
																	</optgroup> <?php
																}
															}
															if(!empty($val['files'])) { ?>
																<optgroup label="<?php echo L_THEME_MAIN_FOLDER ?>"> <?php
																	foreach($val['files'] as $mfiles) {
																		if($themefile != $mfiles){?>
																			<option value="<?php echo $mfiles; ?>"><?php echo $mfiles; ?></option> <?php
																		} else { ?>
																			<option selected="selected" value="<?php echo $mfiles; ?>">&raquo; <?php echo $mfiles; ?></option> <?php
																		}
																	} ?>
																</optgroup> <?php
															}
														}
													} ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<input id="submit" type="submit" value="<?php echo L_BUTTON_EDIT ?>" class="btn btn-primary pull-right" />
						</div>
					</form>
		<form action="" method="post" accept-charset="utf-8" class="form-stacked">
			<div class="row">
				<div class="span12"> <?php
					$filename = urldecode($theme.'/'.$themefile);
					if(!empty($themefile) && is_file($filename)) {
						$filecontents = $filename;
						$properfilename = $themename.'/'.$themefile;
					} else {
						$filecontents = "$theme/css/$themename.css";
						$filecontents = file_exists("$theme/css/$themename.css") ? "$theme/css/$themename.css" : "$theme/css/styles.css";
						$properfilename = file_exists("$themename/css/$themename.css") ? "$themename/css/$themename.css" : "$themename/css/styles.css";
					}
					$filecontents = is_readable($filecontents) ? file_get_contents($filecontents) : L_FILE_NOT_READABLE;
					$filecontents =  view($filecontents); ?>
					<h2>Editing <?php echo $properfilename; ?></h2>
					<?php if(CUSTOM_INSTALL != true && preg_match('/\.tpl\.html$/', $themefile)){printf(NOTE.'<br />',L_THEME_EDITOR_VAR_NOTE); }?>
					<textarea name="file_contents" id="file_contents" class="span12"><?php echo $filecontents;  ?></textarea>
				</div>
			</div>
			<div class="form-actions">
				<input type="submit" name="submit" id="submit" value="update" class="btn btn-primary" />
			</div>
		</div>
	<?php
	} else {
	?>
		<div id="themeEditor" class="row">
			<div class="span12">
				<?php echo $output ;?>
				<p class="contentnote"><?php echo L_THEME_EDIT_CHOOSE_NOTE ?></p>
				<table id="themeList" class="table table-striped table-hover">
					<tbody> <?php
					$expanse_dir = EXPANSEPATH;
					$yoursite = YOUR_SITE;
					$active_theme = getOption('theme');
					foreach($themes as $ind => $theme) {
						$preview_img = "$expanse_dir/themes/$theme/images/preview.png";
						$preview_img_url = EXPANSE_URL."themes/$theme/images/preview.png"; ?>
							<tr<?php echo ($theme == $active_theme) ?  ' class="activeTheme"' : (($ind % 2) ? ' class="altRow"' : ''); ?>>
								<td> <?php
									if(file_exists($preview_img)) {
										echo '<img src="'.$preview_img_url.'" />';
									} ?>
								</td>
								<td class="themeInfo"> <?php
									$theme_css_file = file_exists("$expanse_dir/themes/$theme/css/$theme.css") ? "$expanse_dir/themes/$theme/css/$theme.css" : "$expanse_dir/themes/$theme/css/styles.css";
									$theme_info = theme_info($theme_css_file);
									if(has_theme_info($theme_info)) { ?>
										<h3><?php echo !empty($theme_info->Name) ? $theme_info->Name : str_replace('_', ' ', $theme); echo !empty($theme_info->Author) ? '<small> '.L_THEME_BY.' '.$theme_info->Author.'</small>' : ''; ?></h3> <?php
										echo !empty($theme_info->Version) ? '<h4>'.L_THEME_VERSION.' '.$theme_info->Version.'</h4>' : '';
										echo !empty($theme_info->Description) ? '<p>'.$theme_info->Description.'</p>' : '';
									} else {
										echo '<h1>'.str_replace('_', ' ', $theme).'</h1>';
									}
									?>
								</td>
								<td>
									<a href="<?php echo $yoursite ?>?theme=<?php echo $theme ?>" target="_blank" class="btn"><?php echo L_THEME_PREVIEW ?></a>
								</td>
								<td>
									<a href="index.php?cat=admin&amp;sub=theme_editor&amp;theme=<?php echo $theme ?>" class="editLink btn primary"><?php echo L_THEME_EDIT_TEXT ?></a>
								</td>
								<td> <?php
									if($theme != $active_theme) { ?>
										<label for="activate_this_<?php echo $ind?>" class="radio">
											<input type="radio" id="activate_this_<?php echo $ind?>" name="activate_this" value="<?php echo $theme; ?>" />
											<?php echo L_THEME_ACTIVATE_TEXT ?>
										</label>
									<?php
									} else {
										echo 'Active Theme';
									}
									?>
								</td>
							</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-actions">
			<div class="pull-right">
				<input type="submit" name="submit" id="submit" value="<?php echo L_BUTTON_ACTIVATE ?>" class="btn btn-success" />
			</div>
		</div>
		<?php
		if(is_writable(EXPANSEPATH.'/themes')) {
		?>
		<div class="row">
			<div class="span12">
				<fieldset>
					<legend><?php echo L_THEME_CREATE_TITLE ?></legend>
					<p><?php echo L_THEME_CREATE_DETAIL ?></p>
					<label for="new_theme_name"><?php echo L_THEME_CREATE_THEME_NAME ?></label>
					<input name="new_theme_name" id="new_theme_name" type="text" /><br />
					<div class="actions">
						<input type="submit" value="<?php echo L_BUTTON_MAKE_NEW_THEME ?>" name="new_submit" id="new_submit" class="btn primary" />
					</div>
				</fieldset>
			</div>
		</div>
		<?php
		}
	}
}
