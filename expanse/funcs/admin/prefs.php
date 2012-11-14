<?php
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}
/*   Preferences   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=prefs">'.L_ADMIN_PREFS.'</a>',array(),'preferences');
if($admin_sub !== 'prefs'){return;}
add_breadcrumb(L_PREFS_TITLE);
add_title(L_PREFS_TITLE);
ozone_action('admin_page', 'prefs_content');
function prefs_content() {
	global $output, $currencysymbols , $themesdir;
	if (is_posting(L_BUTTON_UPDATE)) {
		manageOptions("submit,rettimeformat,retdateformat,utctime,resettime");
		$use_clean_urls = getOption('use_clean_urls');
		if($use_clean_urls == 1 && CAN_REWRITE) {
			$the_ht = HOMEPATH.'/.htaccess';
			$yoursite = checkTrailingSlash(getOption('yoursite'));
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
'</IfModule>'
				);
				insert_between(HOMEPATH.'/.htaccess', 'expanse rewrite ninja madness', $rewrite_rules);
			}
		}
		applyOzoneAction('manage_options');
	}
	$option = getAllOptions();
	echo $output; ?>
	<div class="accordion" id="prefs">
		<!--
		/*
		============================================================
		General Settings
		============================================================
		*/
		-->
		<div class="accordion-group" title="general">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#general"><?php echo L_PREFS_HDR_GENERAL ?></a>
			</div>
			<div id="general" class="accordion-body collapse in">
				<div class="accordion-inner">
					<div class="span5">
						<!-- /*   Your Name   //===============================*/ -->
						<div class="control-group">
							<label for="yourname" class="control-label"><?php echo L_PREFS_YOUR_NAME ?></label>
							<div class="controls">
								<input name="yourname" type="text" class="span5 formfields" id="yourname" value="<?php echo $option->yourname; ?>" />
								<?php helpBlock(L_PREFS_YOUR_NAME_HELP); // tooltip(L_PREFS_YOUR_NAME, L_PREFS_YOUR_NAME_HELP); ?>
							</div>
						</div>

						<!-- /*   Admin Email   //===============================*/ -->
						<div class="control-group">
							<label for="adminemail" class="control-label"><?php echo L_PREFS_ADMIN_EMAIL ?></label>
							<div class="controls">
								<input name="adminemail" type="text" class="span5 formfields" id="adminemail" value="<?php echo $option->adminemail; ?>" />
								<?php helpBlock(L_PREFS_ADMIN_EMAIL_HELP); ?>
							</div>
						</div>
					</div>
					<div class="span5">
						<!-- /*   Site Name   //===============================*/ -->
						<div class="control-group">
							<label for="sitename" class="control-label"><?php echo L_PREFS_SITE_NAME ?></label>
							<div class="controls">
								<input name="sitename" type="text" class="span5 formfields" id="sitename" value="<?php echo $option->sitename; ?>" />
								<?php helpBlock(L_PREFS_SITE_NAME_HELP); ?>
							</div>
						</div>

						<!-- /*   Site Description   //===============================*/ -->
						<div class="control-group">
							<label for="sitedescr" class="control-label"><?php echo L_PREFS_SITE_DESCRIPTION ?></label>
							<div class="controls">
								<textarea name="sitedescr" cols="40" class="span5 formfields" id="sitedescr"><?php echo $option->sitedescr; ?></textarea>
								<?php helpBlock(L_PREFS_SITE_DESCRIPTION_HELP); ?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="span5">
						<!-- /*   Your Site URL   //===============================*/ -->
						<div class="control-group">
							<label for="yoursite"><?php echo L_PREFS_SITE_URL ?> </label>
							<div class="controls">
								<input name="yoursite" type="text" class="span5 formfields" id="yoursite" value="<?php echo checkTrailingSlash($option->yoursite); ?>" />
								<?php helpBlock(array(L_PREFS_SITE_URL_HELP, checkTrailingSlash($_SERVER['HTTP_HOST']))); ?>
							</div>
						</div>
					</div>
					<?php applyOzoneAction('preferences_general_menu'); ?>
					<!-- // -->
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
		<div class="accordion-group" title="timeSettings">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#timeSettings"><?php echo L_PREFS_HDR_TIME ?></a>
			</div>
			<div id="timeSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="span5">
						<!-- /*   Current Server Time   //===============================*/ -->
						<div class="control-group">
							<label for="utctime" class="control-label"><?php echo L_PREFS_TIME_SERVER ?> </label>
							<input name="utctime" type="text" class="span5 formatSettings" id="utctime" value="<?php echo date('F d, Y g:i:s a');?>" />
							<?php helpBlock(array(L_PREFS_TIME_SERVER_HELP, date("T"))); ?>
						</div>

						<!-- /*   Local Timezone Offset   //===============================*/ -->
						<div class="control-group">
							<label for="timeoffset"><?php echo L_PREFS_TIME_TZ_OFFSET ?></label>
							<div class="controls" class="control-label">
								<input name="timeoffset" type="text" class="span5 formfields" id="timeoffset" value="<?php echo isset($option->timeoffset) ? $option->timeoffset : '';?>" size="10" />
								<?php helpBlock(L_PREFS_TIME_TZ_OFFSET_HELP); ?>
							</div>
						</div>
					</div>
					<div class="span5">
						<!-- /*   Date Format   //===============================*/ -->
						<div class="control-group">
							<label for="dateformat"><?php echo L_PREFS_TIME_FORMAT_DATE ?> </label>
							<div class="controls" class="control-label">
								<input name="dateformat" type="text" class="span5 formfields" id="dateformat" value="<?php echo $option->dateformat; ?>" />
							</div>
						</div>

						<!-- /*   Current Date Format   //===============================*/ -->
						<div class="control-group">
							<label for="retdateformat" class="formatSettingsL"><?php echo L_PREFS_TIME_CURR_FORMAT_DATE ?></label>
							<div class="controls" class="control-label">
								<input name="retdateformat" type="text" class="span5 formatSettings" id="retdateformat" value="" />
								<?php helpBlock(array(L_PREFS_TIME_CURR_FORMAT_DATE_HELP, date($option->dateformat))); ?>
							</div>
						</div>

						<!-- /*   Time Format   //===============================*/ -->
						<div class="control-group">
							<label for="timeformat"><?php echo L_PREFS_TIME_FORMAT_TIME ?></label>
							<div class="controls" class="control-label">
								<input name="timeformat" type="text" class="span5 formfields" id="timeformat" value="<?php echo $option->timeformat; ?>" />
							</div>
						</div>

						<!-- /*   Current Time Format   //===============================*/ -->
						<div class="control-group">
							<label for="rettimeformat" class="formatSettingsL"><?php echo L_PREFS_TIME_CURR_FORMAT_TIME ?></label>
							<div class="controls" class="control-label">
								<input name="rettimeformat" type="text" class="formatSettings" id="rettimeformat" value="" />
								<?php helpBlock(array(L_PREFS_TIME_CURR_FORMAT_DATE_HELP, date($option->timeformat))); ?>
							</div>
						</div>
					</div>
					<?php applyOzoneAction('preferences_time_menu'); ?>
					<!-- // -->
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
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#appearanceSettings"><?php echo L_PREFS_HDR_APPEARANCE ?></a>
			</div>
			<div id="appearanceSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<!-- /*   Start Category   //===============================*/ -->
					<div class="control-group">
						<label for="startcategory"><?php echo L_PREFS_APP_START_CATEGORY ?></label>
						<div class="controls" class="control-label">
							<select name="startcategory" id="startcategory" class="span5 formfields"> <?php
							$startcategory = $option->startcategory;
							$selected = ($startcategory == 'ALL') ? ' selected="selected"' : '';
							echo '<option'.$selected.' value="ALL">All categories</option>';
							$d2 = mysql_query("SELECT * FROM ".PREFIX."sections WHERE pid = 0");
							while($d3 = mysql_fetch_array($d2)){
								$d4 = mysql_query("SELECT * FROM ".PREFIX."sections WHERE pid = $d3[id]");
								if($d3['cat_type'] == 'pages') {
									echo '<optgroup label="&mdash;User Created Pages"></optgroup>';
									get_page_dropdown(0,0,0,true, $startcategory);
									continue;
								}
								$selected = ($d3['id'] == $startcategory) ? ' selected="selected"' : '';
								?> <option<?php echo $selected ?> value="<?php echo $d3['id'] ?>"><?php echo ucwords($d3['sectionname']) ?></option> <?php
								while($d5 = mysql_fetch_array($d4)) {
									$selected = ($d5['id'] == $startcategory) ? ' selected="selected"' : '';
									?> <option<?php echo $selected ?> value="<?php echo $d5['id'] ?>">&mdash;<?php echo $d5['sectionname'] ?></option> <?php
								}
							} ?>
							</select>
							<?php helpBlock(L_PREFS_APP_START_CATEGORY_HELP); ?>
						</div>
					</div>

					<!-- /*   How many per page?   //===============================*/ -->
					<div class="control-group">
						<label for="howmany"><?php echo L_PREFS_APP_HOW_MANY ?></label>
						<div class="controls" class="control-label">
							<input type="text" name="howmany" value="<?php echo $option->howmany; ?>" class="span5 formfields" id="howmany" />
							<?php helpBlock(L_PREFS_APP_HOW_MANY_HELP); ?>
						</div>
					</div>

					<!-- /*   How many per edit page?   //===============================*/ -->
					<div class="control-group">
						<label for="howmany_edit"><?php echo L_PREFS_APP_HOW_MANY_EDIT ?></label>
						<div class="controls" class="control-label">
							<input type="text" name="howmany_edit" value="<?php echo $option->howmany_edit; ?>" class="formfields" id="howmany_edit" />
							<?php helpBlock(array(L_PREFS_APP_HOW_MANY_EDIT_HELP, CMS_NAME)); ?>
						</div>
					</div>

					<!-- /*   Sort your categories by   //===============================*/ -->
					<div class="control-group">
						<label for="sortcats" class="control-label"><?php echo L_PREFS_APP_SORT_CATS ?></label>
						<div class="controls">
							<select id="sortcats" class="span5 formfields" name="sortcats"> <?php
							$items = new Expanse('items');
							foreach($items->Fields as $ind => $val){
								switch($val){
									case 'id':
										$sorttag = L_SORT_BY_ID;
										break;
									case 'title':
										$sorttag = L_SORT_BY_TITLE;
										break;
									case 'aid':
										$sorttag = L_SORT_BY_USER;
										break;
									case 'created':
										$sorttag = L_SORT_BY_DATE;
										break;
									case 'order_rank':
										$sorttag = L_SORT_BY_USER_RANK;
										break;
									default:
										$sorttag = '';
										break;
								}
								$selected = ($val == $option->sortcats) ? ' selected="selected"' : '';
								echo !empty($sorttag) ? '<option value="'.$val.'"'.$selected.'>'.$sorttag.'</option>' : '';
							} ?>
							</select>
							<?php helpBlock(array(L_PREFS_APP_SORT_CATS_HELP, CMS_NAME)); ?>
						</div>
					</div>

					<!-- /*   Sort Direction   //===============================*/ -->
					<div class="control-group">
						<label for="sortdirection" class="control-label"><?php echo L_PREFS_APP_SORT_DIR ?></label>
						<div class="controls">
							<select id="sortdirection" class="span5 formfields" name="sortdirection">
								<option value="ASC"<?php echo $option->sortdirection == 'ASC' ? ' selected="selected"' : '' ?>><?php echo L_SORT_BY_ASC ?></option>
								<option value="DESC"<?php echo $option->sortdirection == 'DESC' ? ' selected="selected"' : '' ?>><?php echo L_SORT_BY_DESC ?></option>
							</select>
							<?php helpBlock(array(L_PREFS_APP_SORT_DIR_HELP, array(CMS_NAME,CMS_NAME))); ?>
						</div>
					</div>

					<!-- /*   Default Thumbnail Size   //===============================*/ -->
					<div class="control-group">
						<label for="thumbsize" class="control-label"><?php echo L_PREFS_APP_THUMB_SIZE ?></label>
						<div class="controls">
							<input type="text" name="thumbsize" value="<?php echo $option->thumbsize; ?>" class="span5 formfields" id="thumbsize" />
							<?php helpBlock(L_PREFS_APP_THUMB_SIZE_HELP); ?>
						</div>
					</div>

					<!-- /*   Show smilies in comments?   //===============================*/ -->
					<div class="control-group">
						<div class="controls">
							<label for="commentsmilies" class="control-label">
								<input type="hidden" value="0" name="commentsmilies" />
								<input type="checkbox" name="commentsmilies" value="1"<?php echo $option->commentsmilies == 1 ? ' checked="checked"' : '';?> id="commentsmilies" />
								<span><?php echo L_PREFS_APP_SMILIES_COMMENTS ?></span>
							</label>
							<?php helpBlock(L_PREFS_APP_SMILIES_COMMENTS_HELP); ?>
						</div>
					</div>

					<!-- /*   Select Theme   //===============================*/ -->
					<div class="control-group">
						<label for="theme" class="control-label"><?php echo L_PREFS_APP_THEME ?></label>
						<div class="controls">
							<select name="theme" id="theme" class="span5 formfields"> <?php
								$theme = $option->theme;
								if (is_dir($themesdir)) {
									if ($dh = opendir($themesdir)) {
									while (($file = readdir($dh)) !== false) {
										if(is_dir($themesdir."/".$file) && $file != '.' && $file != '..'){
											echo ($file == $theme) ? '<option selected="selected">'.$file.'</option>' : '<option>'.$file.'</option>';
										}
									}
									closedir($dh);
									}
								} ?>
							</select>
							<?php helpBlock(L_PREFS_APP_THEME_HELP); ?>
						</div>
					</div>

					<?php if(CAN_REWRITE) { ?>
						<!-- /*   Clean URLS   //===============================*/ -->
						<div class="control-group">
							<label for="use_clean_urls" class="checkbox">
								<input type="hidden" name="use_clean_urls" value="0" />
								<input type="checkbox" id="use_clean_urls" name="use_clean_urls" value="1" <?php echo isset($option->use_clean_urls) && $option->use_clean_urls == 1 ? 'checked="checked"' : ''; ?> />
								<span><?php echo L_PREFS_APP_CLEAN_URLS ?></span>
							</label>
							<?php helpBlock(array(L_PREFS_APP_CLEAN_URLS_HELP, array($option->yoursite,INDEX_PAGE,$option->yoursite))); ?>
						</div>
					<?php } ?>

					<!-- /*   Index file renaming   //===============================*/ -->
					<div class="control-group">
						<label for="index_file" class="control-label"><?php echo L_PREFS_APP_INDEX_FILE ?></label>
						<div class="controls">
							<input type="text" id="index_file" name="index_file" value="<?php echo $option->index_file ?>" class="span5" />
							<?php helpBlock(L_PREFS_APP_INDEX_FILE_HELP); ?>
						</div>
					</div>

					<?php applyOzoneAction('preferences_theme_menu'); ?>
					<!-- // -->
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
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#prefs" href="#paypalSettings"><?php echo L_PREFS_HDR_PAYPAL ?></a>
			</div>
			<div id="paypalSettings" class="accordion-body collapse">
				<div class="accordion-inner">
					<!-- /*   Paypal E-mail   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_email" class="control-label"><?php echo L_PREFS_PP_EMAIL ?></label>
						<div class="controls">
							<input name="paypal_email" type="text" class="span5 infields" id="paypal_email" value="<?php echo $option->paypal_email;?>" />
							<?php helpBlock(L_PREFS_PP_EMAIL_HELP); ?>
						</div>
					</div>

					<!-- /*   Currency Type   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_currency_code" class="control-label"><?php echo L_PREFS_PP_CURRENCY_CODE ?></label>
						<div class="controls">
							<select name="paypal_currency_code" id="paypal_currency_code" class="span5 infields"> <?php
							foreach($currencysymbols as $ind => $val) { ?>
								<option value="<?php echo $ind ?>"<?php echo $ind == $option->paypal_currency_code ? 'selected="selected"' : ''; ?>><?php echo $ind ?> (<?php echo $val ?>)</option> <?php
							} ?>
							</select>
							<?php helpBlock(L_PREFS_PP_CURRENCY_CODE_HELP); ?>
						</div>
					</div>

					<!-- /*   Paypal Logo   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_logo" class="control-label"><?php echo L_PREFS_PP_LOGO_URL ?></label>
						<div class="controls">
							<input name="paypal_logo" type="text" class="span5 infields" id="paypal_logo" value="<?php echo $option->paypal_logo;?>" />
							<?php helpBlock(L_PREFS_PP_LOGO_URL_HELP); ?>
						</div>
					</div>

					<!-- /*   Shipping Cost   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_shipping" class="control-label"><?php echo L_PREFS_PP_SHIPPING ?></label>
						<div class="controls">
							<input name="paypal_shipping" type="text" class="span5 infields" id="paypal_shipping" value="<?php echo $option->paypal_shipping;?>" />
							<?php helpBlock(L_PREFS_PP_SHIPPING_HELP); ?>
						</div>
					</div>

					<!-- /*   Shipping cost (multiple items)   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_shipping2" class="control-label"><?php echo L_PREFS_PP_SHIPPING2 ?></label>
						<div class="controls">
							<input name="paypal_shipping2" type="text" class="span5 infields" id="paypal_shipping2" value="<?php echo $option->paypal_shipping2;?>" />
							<?php helpBlock(L_PREFS_PP_SHIPPING2_HELP); ?>
						</div>
					</div>

					<!-- /*   Item Tax   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_tax" class="control-label"><?php echo L_PREFS_PP_TAX ?></label>
						<div class="controls">
							<input name="paypal_tax" type="text" class="span5 infields" id="paypal_tax" value="<?php echo $option->paypal_tax;?>" />
							<?php helpBlock(L_PREFS_PP_TAX_HELP); ?>
						</div>
					</div>

					<!-- /*   Handling Cost   //===============================*/ -->
					<div class="control-group">
						<label for="paypal_handling_cart" class="control-label"><?php echo L_PREFS_PP_HANDLING ?></label>
						<div class="controls">
							<input name="paypal_handling_cart" type="text" class="span5 infields" id="paypal_handling_cart" value="<?php echo $option->paypal_handling_cart;?>" />
							<?php helpBlock(L_PREFS_PP_HANDLING_HELP); ?>
						</div>
					</div>

					<?php applyOzoneAction('preferences_paypal_menu'); ?>
					<!-- // -->
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
					<!-- /*   Choose a language   //===============================*/ -->
					<div class="control-group">
						<label for="language" class="control-label"><?php echo L_PREFS_LANGUAGE ?></label>
						<?php $language_files = getFiles(LEXICON, 'files');
						//debug($language_files); ?>
						<div class="controls">
							<select name="language" id="language" class="span5 infields">
								<?php $language_files = getFiles(LEXICON, 'files');
								foreach($language_files as $ind => $val) {
									$ext = strrchr($val, '.');
									$file_name = remExtension($val);
									if($ext != '.php' || $file_name == 'index'){continue;}
									$lang_info = language_info(LEXICON.$val); ?>
									<option value="<?php echo $file_name ?>"<?php echo $file_name == $option->language ? 'selected="selected"' : ''; ?>><?php echo $lang_info->Language ?> (<?php printf(L_PREFS_LANGUAGE_TRANSLATED_BY, $lang_info->Translator); ?>)</option> <?php
								} ?>
							</select>
							<?php helpBlock(sprintf(L_PREFS_LANGUAGE_HELP, CMS_NAME)); ?>
						</div>
					</div>

					<?php applyOzoneAction('preferences_language_menu'); ?>
					<!-- // -->
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
					<!-- /*   Moderate all comments   //===============================*/ -->
					<div class="control-group">
						<div class="controls">
							<label for="moderate_comments" class="checkbox">
								<input type="hidden" name="moderate_comments" value="0" />
								<input name="moderate_comments"<?php echo isset($option->moderate_comments) && $option->moderate_comments == 1 ? 'checked="checked"' : ''; ?> type="checkbox" class="formfields" id="moderate_comments" value="1" />
								<span><?php echo L_PREFS_USER_MODERATE ?></span>
							</label>
							<?php helpBlock(L_PREFS_USER_MODERATE_HELP); ?>
						</div>
					</div>

					<!-- /*   Flood Control Delay   //==============================*/ -->
					<div class="control-group">
						<label for="floodcontrol" class="control-label"><?php echo L_PREFS_USER_FLOODING ?></label>
						<div class="controls">
							<input name="floodcontrol" type="text" class="span5 formfields" id="floodcontrol" value="<?php echo $option->floodcontrol;?>" />
							<?php helpBlock(L_PREFS_USER_FLOODING_HELP); ?>
						</div>
					</div>

					<!-- /*   Banned Words   //===============================*/ -->
					<div class="control-group">
						<label for="bannedwords" class="control-label"><?php echo L_PREFS_USER_BANNED_WORDS ?></label>
						<div class="controls">
							<textarea rows="" cols="" class="span5 formfields" name="bannedwords" id="bannedwords"><?php echo $option->bannedwords; ?></textarea>
							<?php helpBlock(L_PREFS_USER_BANNED_WORDS_HELP); ?>
						</div>
					</div>

					<!-- /*   Banned IPs   //===============================*/ -->
					<div class="control-group">
						<label for="bannedips" class="control-label"><?php echo L_PREFS_USER_BANNED_IPS ?></label>
						<div class="controls">
							<textarea class="span5 formfields" name="bannedips" id="bannedips"><?php echo $option->bannedips; ?></textarea>
							<?php helpBlock(L_PREFS_USER_BANNED_IPS_HELP); ?>
						</div>
					</div>

					<?php applyOzoneAction('preferences_filter_menu'); ?>
					<!-- // -->
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
?>