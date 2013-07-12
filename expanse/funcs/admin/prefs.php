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

if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}

/*   Preferences   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=prefs">'.L_ADMIN_PREFS.'</a>',array(),'preferences');
if($admin_sub !== 'prefs') {
	return;
}
add_breadcrumb(L_PREFS_TITLE);
add_title(L_PREFS_TITLE);
ozone_action('admin_page', 'prefs_content');

function prefs_content() {
	global $output, $currencysymbols , $themesdir;
<<<<<<< HEAD
	$Database = new DatabaseConnection();
=======
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
	if (is_posting(L_BUTTON_UPDATE)) {
		manageOptions("submit,rettimeformat,retdateformat,utctime,resettime");
		$use_clean_urls = getOption('use_clean_urls');
		if($use_clean_urls == 1 && CAN_REWRITE) {
			$the_ht = HOMEPATH.'/.htaccess';
			$yoursite = checkTrailingSlash(httpify(getOption('yoursite')));
			$index_file = getOption('index_file');
			if((!file_exists($the_ht) && is_writable(HOMEPATH)) || is_writable($the_ht)) {
				$site_root = parse_url($yoursite);
				$site_root = checkTrailingSlash($site_root['path']);
				$index_file = isset($index_file) && !empty($index_file) ? $site_root.$index_file : $site_root.'index.php';
				$rewrite_rules = array(
				'<IfModule mod_rewrite.c>',
				'RewriteEngine On',
				'RewriteBase '.$site_root,
				'RewriteCond %{REQUEST_FILENAME} !-f',
				'RewriteCond %{REQUEST_FILENAME} !-d',
				'RewriteRule . '.$index_file.' [L]',
				'</IfModule>');
				insert_between(HOMEPATH.'/.htaccess', 'expanse rewrite ninja madness', $rewrite_rules);
			}
		}
		applyOzoneAction('manage_options');
	}
	$option = getAllOptions();
	echo $output;
	?>
	<div class="accordion" id="prefs">
		<!--
		/*
		============================================================
		General Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#general" title="<?php echo L_PREFS_HDR_GENERAL ?>"><?php echo L_PREFS_HDR_GENERAL ?></a>
			</div>
			<div id="general" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Your Name   //===============================*/ -->
								<div class="control-group">
									<label for="yourname" class="control-label"><?php echo L_PREFS_YOUR_NAME ?></label>
									<div class="controls">
										<input name="yourname" type="text" class="span12" id="yourname" value="<?php echo $option->yourname; ?>" <?php popOver('bottom', L_PREFS_YOUR_NAME, L_PREFS_YOUR_NAME_HELP); ?>  />
									</div>
								</div>

								<!-- /*   Admin Email   //===============================*/ -->
								<div class="control-group">
									<label for="adminemail" class="control-label"><?php echo L_PREFS_ADMIN_EMAIL ?></label>
									<div class="controls">
										<input name="adminemail" type="text" class="span12" id="adminemail" value="<?php echo $option->adminemail; ?>" <?php popOver('bottom', L_PREFS_ADMIN_EMAIL, L_PREFS_ADMIN_EMAIL_HELP); ?> />
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Site Name   //===============================*/ -->
								<div class="control-group">
									<label for="sitename" class="control-label"><?php echo L_PREFS_SITE_NAME ?></label>
									<div class="controls">
										<input name="sitename" type="text" class="span12" id="sitename" value="<?php echo $option->sitename; ?>" <?php popOver('bottom', L_PREFS_SITE_NAME, L_PREFS_SITE_NAME_HELP); ?> />
									</div>
								</div>

								<!-- /*   Site Description   //===============================*/ -->
								<div class="control-group">
									<label for="sitedescr" class="control-label"><?php echo L_PREFS_SITE_DESCRIPTION ?></label>
									<div class="controls">
										<textarea name="sitedescr" cols="40" class="span12" id="sitedescr" <?php popOver('bottom', L_PREFS_SITE_DESCRIPTION, L_PREFS_SITE_DESCRIPTION_HELP); ?>><?php echo $option->sitedescr; ?></textarea>
									</div>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Your Site URL   //===============================*/ -->
								<div class="control-group">
									<label for="yoursite"><?php echo L_PREFS_SITE_URL ?> </label>
									<div class="controls">
										<input name="yoursite" type="text" class="span12 formfields" id="yoursite" value="<?php echo checkTrailingSlash($option->yoursite); ?>" <?php popOver('bottom', L_PREFS_SITE_URL, array(L_PREFS_SITE_URL_HELP, checkTrailingSlash($_SERVER['HTTP_HOST']))); ?> />
									</div>
								</div>
							</div>
							<?php applyOzoneAction('preferences_general_menu'); ?>
							<!-- // -->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--
		/*
		============================================================
		Time Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#timeSettings" title="<?php echo L_PREFS_HDR_TIME ?>"><?php echo L_PREFS_HDR_TIME ?></a>
			</div>
			<div id="timeSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Current Server Time   //===============================*/ -->
								<div class="control-group">
									<label for="utctime" class="control-label"><?php echo L_PREFS_TIME_SERVER ?> </label>
<<<<<<< HEAD
									<input name="utctime" type="text" class="span12" id="utctime" value="<?php echo isset($option->timeoffset) ? date('F d, Y g:i:s a', time() + (3600 * $option->timeoffset)) : date('F d, Y g:i:s a');?>" <?php popOver('bottom', L_PREFS_TIME_SERVER, array(L_PREFS_TIME_SERVER_HELP, date("T"))); ?> />
=======
									<input name="utctime" type="text" class="span12" id="utctime" value="<?php echo date('F d, Y g:i:s a');?>" <?php popOver('bottom', L_PREFS_TIME_SERVER, array(L_PREFS_TIME_SERVER_HELP, date("T"))); ?> />
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
								</div>

								<!-- /*   Local Timezone Offset   //===============================*/ -->
								<div class="control-group">
									<label for="timeoffset"><?php echo L_PREFS_TIME_TZ_OFFSET ?></label>
									<div class="controls" class="control-label">
										<input name="timeoffset" type="text" class="span12" id="timeoffset" value="<?php echo isset($option->timeoffset) ? $option->timeoffset : '';?>" <?php popOver('bottom', L_PREFS_TIME_TZ_OFFSET, L_PREFS_TIME_TZ_OFFSET_HELP); ?> />
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Date Format   //===============================*/ -->
								<div class="control-group">
									<label for="dateformat"><?php echo L_PREFS_TIME_FORMAT_DATE ?> </label>
									<div class="controls" class="control-label">
										<input name="dateformat" type="text" class="span12" id="dateformat" value="<?php echo $option->dateformat; ?>" />
									</div>
								</div>

								<!-- /*   Current Date Format   //===============================*/ -->
								<div class="control-group">
									<label for="retdateformat" class="formatSettingsL"><?php echo L_PREFS_TIME_CURR_FORMAT_DATE ?></label>
									<div class="controls" class="control-label">
										<input name="retdateformat" type="text" class="span12" id="retdateformat" value="" <?php popOver('bottom', L_PREFS_TIME_CURR_FORMAT_DATE, array(L_PREFS_TIME_CURR_FORMAT_DATE_HELP, date($option->dateformat))); ?> />
									</div>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Time Format   //===============================*/ -->
								<div class="control-group">
									<label for="timeformat"><?php echo L_PREFS_TIME_FORMAT_TIME ?></label>
									<div class="controls" class="control-label">
										<input name="timeformat" type="text" class="span12 formfields" id="timeformat" value="<?php echo $option->timeformat; ?>" />
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Current Time Format   //===============================*/ -->
								<div class="control-group">
									<label for="rettimeformat" class="formatSettingsL"><?php echo L_PREFS_TIME_CURR_FORMAT_TIME ?></label>
									<div class="controls" class="control-label">
										<input name="rettimeformat" type="text" class="span12 formatSettings" id="rettimeformat" value="" <?php popOver('bottom', L_PREFS_TIME_CURR_FORMAT_TIME, array(L_PREFS_TIME_CURR_FORMAT_DATE_HELP, date($option->timeformat))); ?> />
									</div>
								</div>
							</div>
							<?php applyOzoneAction('preferences_time_menu'); ?>
							<!-- // -->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--
		/*
		============================================================
		Theme/Appearance Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#appearanceSettings" title="<?php echo L_PREFS_HDR_APPEARANCE ?>"><?php echo L_PREFS_HDR_APPEARANCE ?></a>
			</div>
			<div id="appearanceSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Start Category   //===============================*/ -->
								<div class="control-group">
									<label for="startcategory"><?php echo L_PREFS_APP_START_CATEGORY ?></label>
									<div class="controls" class="control-label">
										<select name="startcategory" id="startcategory" class="span12" <?php popOver('right', L_PREFS_APP_START_CATEGORY, L_PREFS_APP_START_CATEGORY_HELP); ?>> <?php
											$startcategory = $option->startcategory;
											$selected = ($startcategory == 'ALL') ? ' selected="selected"' : '';
											echo '<option'.$selected.' value="ALL">All categories</option>';
<<<<<<< HEAD
											$d2 = $Database->Query("SELECT * FROM ".PREFIX."sections WHERE pid = 0");
											while($d3 = mysqli_fetch_array($d2)) {
												$d4 = $Database->Query("SELECT * FROM ".PREFIX."sections WHERE pid = $d3[id]");
=======
											$d2 = mysql_query("SELECT * FROM ".PREFIX."sections WHERE pid = 0");
											while($d3 = mysql_fetch_array($d2)) {
												$d4 = mysql_query("SELECT * FROM ".PREFIX."sections WHERE pid = $d3[id]");
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
												if($d3['cat_type'] == 'pages') {
													echo '<optgroup label="&mdash;User Created Pages"></optgroup>';
													get_page_dropdown(0,0,0,true, $startcategory);
													continue;
												}
												$selected = ($d3['id'] == $startcategory) ? ' selected="selected"' : '';
												?>
												<option<?php echo $selected ?> value="<?php echo $d3['id'] ?>"><?php echo ucwords($d3['sectionname']) ?></option>
												<?php
<<<<<<< HEAD
												while($d5 = mysqli_fetch_array($d4)) {
=======
												while($d5 = mysql_fetch_array($d4)) {
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
													$selected = ($d5['id'] == $startcategory) ? ' selected="selected"' : '';
													?>
													<option<?php echo $selected ?> value="<?php echo $d5['id'] ?>">&mdash;<?php echo $d5['sectionname'] ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
								</div>

								<!-- /*   How many per page?   //===============================*/ -->
								<div class="control-group">
									<label for="howmany"><?php echo L_PREFS_APP_HOW_MANY ?></label>
									<div class="controls" class="control-label">
										<input type="text" name="howmany" value="<?php echo $option->howmany; ?>" class="span12" id="howmany" <?php popOver('bottom', L_PREFS_APP_HOW_MANY, L_PREFS_APP_HOW_MANY_HELP); ?>>
									</div>
								</div>

								<!-- /*   How many per edit page?   //===============================*/ -->
								<div class="control-group">
									<label for="howmany_edit"><?php echo L_PREFS_APP_HOW_MANY_EDIT ?></label>
									<div class="controls" class="control-label">
										<input type="text" name="howmany_edit" value="<?php echo $option->howmany_edit; ?>" class="span12" id="howmany_edit" <?php popOver('bottom', L_PREFS_APP_HOW_MANY_EDIT, array(L_PREFS_APP_HOW_MANY_EDIT_HELP, CMS_NAME)); ?>>
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Sort your categories by   //===============================*/ -->
								<div class="control-group">
									<label for="sortcats" class="control-label"><?php echo L_PREFS_APP_SORT_CATS ?></label>
									<div class="controls">
										<select id="sortcats" class="span12" name="sortcats" <?php popOver('left', L_PREFS_APP_SORT_CATS, array(L_PREFS_APP_SORT_CATS_HELP, CMS_NAME)); ?>>
											<?php
											$items = new Expanse('items');
											foreach($items->Fields as $ind => $val) {
												switch($val) {
													case 'id': {
														$sorttag = L_SORT_BY_ID;
														break;
													}
													case 'title': {
														$sorttag = L_SORT_BY_TITLE;
														break;
													}
													case 'aid': {
														$sorttag = L_SORT_BY_USER;
														break;
													}
													case 'created': {
														$sorttag = L_SORT_BY_DATE;
														break;
													}
													case 'order_rank': {
														$sorttag = L_SORT_BY_USER_RANK;
														break;
													}
													default: {
														$sorttag = '';
														break;
													}
												}
												$selected = ($val == $option->sortcats) ? ' selected="selected"' : '';
												echo !empty($sorttag) ? '<option value="'.$val.'"'.$selected.'>'.$sorttag.'</option>' : '';
											}
											?>
										</select>
									</div>
								</div>

								<!-- /*   Sort Direction   //===============================*/ -->
								<div class="control-group">
									<label for="sortdirection" class="control-label"><?php echo L_PREFS_APP_SORT_DIR ?></label>
									<div class="controls">
										<select id="sortdirection" class="span12" name="sortdirection" <?php popOver('left', L_PREFS_APP_SORT_DIR, array(L_PREFS_APP_SORT_DIR_HELP, array(CMS_NAME,CMS_NAME))); ?>>
											<option value="ASC"<?php echo $option->sortdirection == 'ASC' ? ' selected="selected"' : '' ?>><?php echo L_SORT_BY_ASC ?></option>
											<option value="DESC"<?php echo $option->sortdirection == 'DESC' ? ' selected="selected"' : '' ?>><?php echo L_SORT_BY_DESC ?></option>
										</select>
									</div>
								</div>

								<!-- /*   Default Thumbnail Size   //===============================*/ -->
								<div class="control-group">
									<label for="thumbsize" class="control-label"><?php echo L_PREFS_APP_THUMB_SIZE ?></label>
									<div class="controls">
										<input type="text" name="thumbsize" value="<?php echo $option->thumbsize; ?>" class="span12" id="thumbsize" <?php popOver('bottom', L_PREFS_APP_THUMB_SIZE, L_PREFS_APP_THUMB_SIZE_HELP); ?>>
									</div>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Select Theme   //===============================*/ -->
								<div class="control-group">
									<label for="theme" class="control-label"><?php echo L_PREFS_APP_THEME ?></label>
									<div class="controls">
										<select name="theme" id="theme" class="span12" <?php popOver('right', L_PREFS_APP_THEME, L_PREFS_APP_THEME_HELP); ?>> <?php
											$theme = $option->theme;
											if(is_dir($themesdir)) {
												if($dh = opendir($themesdir)) {
													while(($file = readdir($dh)) !== false) {
<<<<<<< HEAD
														if(is_dir($themesdir."/".$file) && $file != '.' && $file != '..' && substr($file, 0, 1) != '.') {
=======
														if(is_dir($themesdir."/".$file) && $file != '.' && $file != '..') {
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
															echo ($file == $theme) ? '<option selected="selected">'.$file.'</option>' : '<option>'.$file.'</option>';
														}
													}
													closedir($dh);
												}
											}
											?>
										</select>
									</div>
								</div>

								<!-- /*   Index file renaming   //===============================*/ -->
								<div class="control-group">
									<label for="index_file" class="control-label"><?php echo L_PREFS_APP_INDEX_FILE ?></label>
									<div class="controls">
										<input type="text" id="index_file" name="index_file" value="<?php echo $option->index_file ?>" class="span12" <?php popOver('bottom', L_PREFS_APP_INDEX_FILE, L_PREFS_APP_INDEX_FILE_HELP); ?>>
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Show smilies in comments?   //===============================*/ -->
								<div class="control-group">
									<div class="controls">
										<label for="commentsmilies" class="checkbox" <?php popOver('left', L_PREFS_APP_SMILIES_COMMENTS, L_PREFS_APP_SMILIES_COMMENTS_HELP); ?>>
											<input type="hidden" value="0" name="commentsmilies" />
											<input type="checkbox" name="commentsmilies" value="1"<?php echo $option->commentsmilies == 1 ? ' checked="checked"' : '';?> id="commentsmilies" />
											<?php echo L_PREFS_APP_SMILIES_COMMENTS ?>
										</label>
									</div>
								</div>

								<?php
								if(CAN_REWRITE) {
									?>

									<!-- /*   Clean URLS   //===============================*/ -->
									<div class="control-group">
										<label for="use_clean_urls" class="checkbox" <?php popOver('left', L_PREFS_APP_CLEAN_URLS, array(L_PREFS_APP_CLEAN_URLS_HELP, array($option->yoursite,INDEX_PAGE,$option->yoursite))); ?>>
											<input type="hidden" name="use_clean_urls" value="0" />
											<input type="checkbox" id="use_clean_urls" name="use_clean_urls" value="1" <?php echo isset($option->use_clean_urls) && $option->use_clean_urls == 1 ? 'checked="checked"' : ''; ?> />
											<?php echo L_PREFS_APP_CLEAN_URLS ?>
										</label>
									</div>
									<?php
								}
								?>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">
							<?php applyOzoneAction('preferences_theme_menu'); ?>
							<!-- // -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--
		/*
		============================================================
		Paypal Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#paypalSettings" title="<?php echo L_PREFS_HDR_PAYPAL ?>"><?php echo L_PREFS_HDR_PAYPAL ?></a>
			</div>
			<div id="paypalSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Paypal E-mail   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_email" class="control-label"><?php echo L_PREFS_PP_EMAIL ?></label>
									<div class="controls">
										<input name="paypal_email" type="text" class="span12" id="paypal_email" value="<?php echo $option->paypal_email;?>" <?php popOver('bottom', L_PREFS_PP_EMAIL, L_PREFS_PP_EMAIL_HELP); ?> />
									</div>
								</div>

								<!-- /*   Currency Type   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_currency_code" class="control-label"><?php echo L_PREFS_PP_CURRENCY_CODE ?></label>
									<div class="controls">
										<select name="paypal_currency_code" id="paypal_currency_code" class="span12" <?php popOver('right', L_PREFS_PP_CURRENCY_CODE, L_PREFS_PP_CURRENCY_CODE_HELP); ?>>
											<?php
											foreach($currencysymbols as $ind => $val) {
												?>
												<option value="<?php echo $ind ?>"<?php echo $ind == $option->paypal_currency_code ? 'selected="selected"' : ''; ?>><?php echo $ind ?> (<?php echo $val ?>)</option>
												<?php
											}
											?>
										</select>
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Paypal Logo   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_logo" class="control-label"><?php echo L_PREFS_PP_LOGO_URL ?></label>
									<div class="controls">
										<input name="paypal_logo" type="text" class="span12" id="paypal_logo" value="<?php echo $option->paypal_logo;?>" <?php popOver('bottom', L_PREFS_PP_LOGO_URL, L_PREFS_PP_LOGO_URL_HELP); ?> />
									</div>
								</div>

								<!-- /*   Shipping Cost   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_shipping" class="control-label"><?php echo L_PREFS_PP_SHIPPING ?></label>
									<div class="controls">
										<input name="paypal_shipping" type="text" class="span12" id="paypal_shipping" value="<?php echo $option->paypal_shipping;?>" <?php popOver('bottom', L_PREFS_PP_SHIPPING, L_PREFS_PP_SHIPPING_HELP); ?> />
									</div>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Shipping cost (multiple items)   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_shipping2" class="control-label"><?php echo L_PREFS_PP_SHIPPING2 ?></label>
									<div class="controls">
										<input name="paypal_shipping2" type="text" class="span12" id="paypal_shipping2" value="<?php echo $option->paypal_shipping2;?>" <?php popOver('bottom', L_PREFS_PP_SHIPPING2, L_PREFS_PP_SHIPPING2_HELP); ?> />
									</div>
								</div>

								<!-- /*   Item Tax   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_tax" class="control-label"><?php echo L_PREFS_PP_TAX ?></label>
									<div class="controls">
										<input name="paypal_tax" type="text" class="span12" id="paypal_tax" value="<?php echo $option->paypal_tax;?>" <?php popOver('bottom', L_PREFS_PP_TAX, L_PREFS_PP_TAX_HELP); ?> />
									</div>
								</div>
							</div>

							<div class="span6">

								<!-- /*   Handling Cost   //===============================*/ -->
								<div class="control-group">
									<label for="paypal_handling_cart" class="control-label"><?php echo L_PREFS_PP_HANDLING ?></label>
									<div class="controls">
										<input name="paypal_handling_cart" type="text" class="span12" id="paypal_handling_cart" value="<?php echo $option->paypal_handling_cart;?>" <?php popOver('bottom', L_PREFS_PP_HANDLING, L_PREFS_PP_HANDLING_HELP); ?> />
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6">
							<?php applyOzoneAction('preferences_paypal_menu'); ?>
							<!-- // -->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--
		/*
		============================================================
		Language Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#languageSettings"><?php echo L_PREFS_HDR_LANGUAGE ?></a>
			</div>
			<div id="languageSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Choose a language   //===============================*/ -->
								<div class="control-group">
									<label for="language" class="control-label"><?php echo L_PREFS_LANGUAGE ?></label>
									<?php $language_files = getFiles(LEXICON, 'files');?>
									<div class="controls">
										<select name="language" id="language" class="span12" <?php popOver('right', L_PREFS_LANGUAGE, sprintf(L_PREFS_LANGUAGE_HELP, CMS_NAME)); ?>>
											<?php $language_files = getFiles(LEXICON, 'files');
											foreach($language_files as $ind => $val) {
												$ext = strrchr($val, '.');
												$file_name = remExtension($val);
												if($ext != '.php' || $file_name == 'index') {
													continue;
												}
												$lang_info = language_info(LEXICON.$val);
												?>
												<option value="<?php echo $file_name ?>"<?php echo $file_name == $option->language ? 'selected="selected"' : ''; ?>><?php echo $lang_info->Language ?> (<?php printf(L_PREFS_LANGUAGE_TRANSLATED_BY, $lang_info->Translator); ?>)</option>
												<?php
											}
											?>
										</select>
									</div>
								</div>

								<?php applyOzoneAction('preferences_language_menu'); ?>
								<!-- // -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--
		/*
		============================================================
		User/Comment Filtering Settings
		============================================================
		*/
		-->
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#commentSettings"><?php echo L_PREFS_HDR_COMMENTS ?></a>
			</div>
			<div id="commentSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="container-fluid">
						<div class="row-fluid">
							<div class="span6">

								<!-- /*   Banned Words   //===============================*/ -->
								<div class="control-group">
									<label for="bannedwords" class="control-label"><?php echo L_PREFS_USER_BANNED_WORDS ?></label>
									<div class="controls">
										<textarea rows="" cols="" class="span12" name="bannedwords" id="bannedwords" <?php popOver('bottom', L_PREFS_USER_BANNED_WORDS, L_PREFS_USER_BANNED_WORDS_HELP); ?>><?php echo $option->bannedwords; ?></textarea>
									</div>
								</div>

								<!-- /*   Banned IPs   //===============================*/ -->
								<div class="control-group">
									<label for="bannedips" class="control-label"><?php echo L_PREFS_USER_BANNED_IPS ?></label>
									<div class="controls">
										<textarea class="span12" name="bannedips" id="bannedips" <?php popOver('bottom', L_PREFS_USER_BANNED_IPS, L_PREFS_USER_BANNED_IPS_HELP); ?>><?php echo $option->bannedips; ?></textarea>
									</div>
								</div>
							</div>

							<div class="span6">

<<<<<<< HEAD
								<!-- /*   Moderate all posts   //===============================*/ -->
								<div class="control-group">
									<div class="controls">
										<label for="moderate_posts" class="checkbox" <?php popOver('left', L_PREFS_USER_MODERATE_POSTS, L_PREFS_USER_MODERATE_POSTS_HELP); ?>>
											<input type="hidden" name="moderate_posts" value="0" />
											<input name="moderate_posts"<?php echo isset($option->moderate_posts) && $option->moderate_posts == 1 ? 'checked="checked"' : ''; ?> type="checkbox" class="formfields" id="moderate_posts" value="1" />
											<?php echo L_PREFS_USER_MODERATE_POSTS ?>
										</label>
									</div>
								</div>

=======
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
								<!-- /*   Moderate all comments   //===============================*/ -->
								<div class="control-group">
									<div class="controls">
										<label for="moderate_comments" class="checkbox" <?php popOver('left', L_PREFS_USER_MODERATE, L_PREFS_USER_MODERATE_HELP); ?>>
											<input type="hidden" name="moderate_comments" value="0" />
											<input name="moderate_comments"<?php echo isset($option->moderate_comments) && $option->moderate_comments == 1 ? 'checked="checked"' : ''; ?> type="checkbox" class="formfields" id="moderate_comments" value="1" />
											<?php echo L_PREFS_USER_MODERATE ?>
										</label>
									</div>
								</div>

								<!-- /*   Flood Control Delay   //==============================*/ -->
								<div class="control-group">
									<label for="floodcontrol" class="control-label"><?php echo L_PREFS_USER_FLOODING ?></label>
									<div class="controls">
										<input name="floodcontrol" type="text" class="span12" id="floodcontrol" value="<?php echo $option->floodcontrol;?>" <?php popOver('bottom', L_PREFS_USER_FLOODING, L_PREFS_USER_FLOODING_HELP); ?> />
									</div>
								</div>
<<<<<<< HEAD

								<!-- /*   required field list   //==============================*/ -->
								<div class="row-fluid">
									<div class="span6">
										<div class="control-group">
											<label for="required_comment_list" class="control-label">Required comment fields</label>
											<div class="controls">
												<input type="text" name="required_comment_list" id="required_comment_list" class="span12" value="<?php echo getOption('required_comment_list'); ?>">
											</div>
										</div>
									</div>
									<div class="span6">
										<div class="control-group">
											<label for="required_contact_list" class="control-label">Required contact fields</label>
											<div class="controls">
												<input type="text" name="required_contact_list" id="required_contact_list" class="span12" value="<?php echo getOption('required_contact_list'); ?>">
											</div>
										</div>
									</div>
								</div>
=======
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
							</div>
						</div>

						<div class="row-fluid">
							<div class="span6">

								<?php applyOzoneAction('preferences_filter_menu'); ?>
								<!-- // -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php applyOzoneAction('preferences_menu'); ?>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo L_BUTTON_UPDATE ?>" />
	</div>
	<?php
}
