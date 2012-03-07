<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

#Must be included at the top of all mod files.
if(!defined('EXPANSE')){
	die('Sorry, but this file cannot be directly viewed.');
}
$size_note = sprintf('<div class="alert-message info" data-alert="alert"><a class="close" href="#">×</a><p>%s</p></div>', sprintf(L_SIZE_NOTE,MAX_UPLOAD));

#If you're adding content, use this block
if(ADDING):
	?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<div class="row">
		<div class="span8">
			<div class="clearfix">
				<label for="title"><?php echo L_PAGE_TITLE ?></label>
				<div class="input">
					<input name="title" type="text" class="span8 formfields" id="title" value="<?php echo @$_POST['title']; ?>" />
				</div>
			</div>
			<div class="clearfix">
				<label for="pid"><?php echo L_PAGE_PARENT; ?></label>
				<div class="input">
					<select id="pid" name="pid" class="span8">
						<option value="0"><?php echo L_PAGE_PARENT_NONE; ?></option>
						<?php
						get_page_dropdown();
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="clearfix">
				<label for="optionsCheckboxes">Post options</label>
				<div class="input">
					<ul class="inputs-list">
						<li>
							<label>
								<input name="smilies" class="cBox" type="checkbox" id="smilies" value="1" checked="checked" />
								<span><?php echo L_USE_SMILIES ?></span>
							</label>
						</li>
						<li>
							<label>
								<input name="comments" class="cBox" type="checkbox" id="comments" value="1" checked="checked" />
								<span><?php echo L_ALLOW_COMMENTS ?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="clearfix">
				<label for="online"><?php echo L_ONLINE ?></label>
				<div class="input">
					<input name="online" class="cBox" type="checkbox" id="online" value="1" checked="checked" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span16">
			<div class="clearfix">
				<label for="descr"><?php echo L_PAGE_BODY ?></label>
				<div class="input border-descr">
					<textarea name="descr" id="descr" class="descr formfields"><?php echo @$_POST['descr']; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<fieldset id="additional_images" class="addFiles">
		<legend><?php echo L_PAGE_FILES; tooltip(L_PAGE_ADDITIONAL_FILES, L_PAGE_ADDITIONAL_FILES_HELP); ?></legend>
			<div class="row" id="additional_images1Group">
				<div class="span8" id="third">
					<div class="clearfix" id="second">
						<label for="additional_images1"><?php echo L_PAGE_FILE ?> 1</label>
						<div class="input" id="first">
							<input name="additional_images1" type="file" class="formfields" id="additional_images1" />
						</div>
					</div>
					<div class="clearfix">
						<label for="caption1"><?php echo L_CAPTION ?> 1</label><br />
						<div class="input">
							<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
						</div>
					</div>
				</div>
			</div>
	</fieldset>
	<div class="row">
		<div class="span10">
		<?php $the_module->custom_fields(); ?>
		</div>
	</div>
	<div class="stretchContainer">
		<?php $the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer'); ?>
	</div>
	<div class="actions">
		<input name="submit" type="submit" class="btn primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
	</div>
<?php endif;
#If you're editing content, use this block
if(EDITING):
	if(EDIT_SINGLE): ?>
		<div class="row">
			<div class="span16">
				<div class="pull-right">
				<?php echo preview_link(); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="title"><?php echo L_PAGE_TITLE ?></label>
					<div class="input">
						<input name="title" type="text" class="span8 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
				<div class="clearfix">
					<label for="pid"><?php echo L_PAGE_PARENT; ?></label>
					<div class="input">
						<select id="pid" name="pid" class="span8">
							<option><?php echo L_PAGE_PARENT_NONE; ?></option>
							<?php
							get_page_dropdown($items->pid);
							?>
						</select>
					</div>
				</div>
				<div class="clearfix">
					<label for="dir_title"><?php echo L_PAGE_TEMPLATE_SAFE_TITLE ?></label>
					<div class="input">
						<input name="dir_title" type="text" class="formfields" id="dir_title" value="<?php echo $items->dirtitle ;?>" />
						<?php tooltip(L_PAGE_TEMPLATE_SAFE_TITLE, sprintf(L_PAGE_TEMPLATE_SAFE_TITLE_HELP, CMS_NAME)); ?>
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="clearfix">
					<label for="optionsCheckboxes">Post options</label>
					<div class="input">
						<ul class="inputs-list">
							<li>
								<label>
									<input type="hidden" name="smilies" value="0" />
									<input type="checkbox" name="smilies" id="smilies" <?php echo ($items->smilies == 1) ? 'checked="checked"' : '';?>  value="1" class="cBox" />
									<span><?php echo L_USE_SMILIES ?></span>
								</label>
							</li>
							<li>
								<label>
									<input type="hidden" name="comments" value="0" id="" />
									<input type="checkbox" name="comments" id="comments" <?php echo ($items->comments == 1) ? 'checked="checked"' : ''; ?> value="1" class="cBox" />
									<span><?php echo L_ALLOW_COMMENTS ?></span>
								</label>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="clearfix">
					<label for="online"><?php echo L_ONLINE ?></label>
					<div class="input">
						<input type="hidden" name="online" id="" value="0" />
						<input type="checkbox" name="online" id="online" <?php echo ($items->online == 1) ? 'checked="checked"' : '';?> value="1" class="cBox" />
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="descr"><?php echo L_PAGE_BODY ?></label>
					<div class="input border-descr">
						<textarea name="descr" id="descr" class="descr formfields"><?php echo view($items->descr); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<fieldset id="additional_images" class="addFiles">
			<legend><?php echo L_PAGE_FILES; tooltip(L_PAGE_ADDITIONAL_FILES, L_PAGE_ADDITIONAL_FILES_HELP); ?></legend>
				<?php echo $size_note; ?>
				<div id="extraImages">
					<?php
					$add_images = $images->GetList(array(array('itemid', '=', $item_id)));
					$count = count($add_images);
					$count++;
					foreach($add_images as $ximg) {
						?>
						<div class="imgBox">
							<div class="clearfix">
								<label for="delete_additional<?php echo $ximg->id ?>"><?php echo L_PAGE_ADDITIONAL_FILES_DELETE ?></label>
								<div class="input">
									<input type="checkbox" id="delete_additional<?php echo $ximg->id ?>" name="delete_additional[]" value="<?php echo $ximg->id ?>" class="xtraImgDelete" />
								</div>
							</div>
							<img src="funcs/tn.lib.php?image_id=<?php echo $ximg->id ?>&amp;dim=50" title="<?php echo view($ximg->caption); ?>" alt="<?php echo view($ximg->caption); ?>" id="addImg<?php echo $ximg->id ?>" class="thumbnails hasHelp" />
							<blockquote class="helpContents" id="addImg<?php echo $ximg->id ?>Help"><h5><?php echo L_PAGE_FILE_CAPTION ?></h5><?php echo empty($ximg->width) ? "<strong>".L_PAGE_FILE_NAME."</strong> $ximg->image" : ''; ?><?php echo view($ximg->caption) ?></blockquote>
						</div>
						<?php
					}
					?>
				</div>
				<div class="row" id="additional_images1Group">
					<div class="span8" id="third">
						<div class="clearfix" id="second">
							<label for="additional_images1"><?php echo L_PAGE_FILE." $count"; ?></label>
							<div class="input" id="first">
								<input name="additional_images1" type="file" class="formfields" id="additional_images1" />
							</div>
						</div>
						<div class="clearfix">
							<label for="caption1"><?php echo L_PAGE_FILE_CAPTION." $count"; ?></label><br />
							<div class="input">
								<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
							</div>
						</div>
					</div>
				</div>
		</fieldset>
		<div class="row">
			<div class="span10">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>
		<div class="stretchContainer">
			<?php
			$the_module->more_options(
				array(
				L_CLEAN_URL_TITLES => 'doCleanURLTitles'
				)
			);
			$the_module->more_options(L_SHARE_ITEM, 'doSharing', 'sharing');
			$the_module->more_options(L_POST_TIME_EDIT, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>
		<div class="actions">
			<input name="submit" type="submit" class="buttons" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
		</div>
	<?php elseif(EDIT_LIST): ?>
		<div class="span16">
			<?php
				$the_module->doSort();
				$itemsList = paginate($itemsList, '', EDIT_LIMIT);
				$numitems = count($itemsList);
				$hasitems = $numitems > 0 ? true : false;
			?>
			<div id="itemList">
			<?php foreach($itemsList as $ind => $item):
				$item->title = trim_title($item->title);
				$item->descr = trim_excerpt($item->descr);
				$users->Get($item->aid);
				$the_displayname = $users->displayname;
				$the_username = $users->username;
				$has_subcat = ($item->cid != $item->pid && $item->cid != 0) ? true : false;
				if($has_subcat){
					$sections->Get($item->cid);
					$category = $sections->sectionname;
				}
				?>
				<div id="item_<?php echo $item->id ?>" title="<?php echo strip_tags($item->descr) ?>">
					<h1><?php echo $item->title ?></h1>
					<p class="<?php echo ($item->online == 0) ? 'offline' : 'online'; ?>"><?php echo ($item->online == 0) ? L_PAGE_OFFLINE : L_PAGE_ONLINE; ?></p>
					<p><?php printf(L_POSTED_BY, $the_displayname, $the_username) ?></p>
					<ul>
						<?php get_page_list(0, $item->id); ?>
					</ul>
					<a href="<?php echo edit_link($item->id); ?>" title="<?php echo L_EDIT_ITEM ?>" class="btn success"><?php echo L_EDIT_ITEM ?></a>
					<a href="<?php echo edit_link($item->id); ?>#sharing" title="<?php echo L_SHARE_ITEM ?>" class="btn shareLink"><?php echo L_SHARE_ITEM ?></a>
					<fieldset>
						<input type="checkbox" name="del[]" value="<?php echo $item->id; ?>" id="item_delete_<?php echo $item->id; ?>" /><label for="item_delete_<?php echo $item->id; ?>"><?php echo L_DELETE_ITEM ?></label>
					</fieldset>
				</div>
			<?php endforeach; ?>

				<input type="hidden" value="<?php echo getOption('sortcats'); ?>" id="order_by" />
			</div>
			<?php if($hasitems): ?>
			<div class="actions">
				<div class="clearfix">
					<div class="pull-right">
						<input name="submit" type="submit" class="btn danger" id="submit" value="<?php echo L_BUTTON_DELETE ?>" />
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
