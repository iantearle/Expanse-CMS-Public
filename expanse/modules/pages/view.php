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
$size_note = sprintf('<div class="alert alert-block alert-info fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>%s</p></div>', sprintf(L_SIZE_NOTE,MAX_UPLOAD));

#If you're adding content, use this block
if(ADDING):
	?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<div class="row">
		<div class="span6">
			<div class="control-group">
				<label for="title" class="control-label"><?php echo L_PAGE_TITLE ?></label>
				<div class="controls">
					<input name="title" type="text" class="span6 formfields" id="title" />
				</div>
			</div>
			<div class="control-group">
				<label for="pid" class="control-label"><?php echo L_PAGE_PARENT; ?></label>
				<div class="controls">
					<select id="pid" name="pid" class="span6">
						<option value="0"><?php echo L_PAGE_PARENT_NONE; ?></option>
						<?php
						get_page_dropdown();
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="control-group">
				<label for="optionsCheckboxes">Post options</label>
				<div class="controls">
					<label for="online" class="checkbox">
						<input name="online" type="checkbox" id="online" value="1" checked="checked" />
						<?php echo L_ONLINE ?>
					</label>
					<label for="smilies" class="checkbox">
						<input name="smilies" type="checkbox" id="smilies" value="1" checked="checked" />
						<?php echo L_USE_SMILIES ?>
					</label>
					<label for="comments" class="checkbox">
						<input name="comments" type="checkbox" id="comments" value="1" checked="checked" />
						<?php echo L_ALLOW_COMMENTS ?>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="descr-well">
		<div class="row-fluid">
			<div class="control-group">
				<label for="descr" class="control-label"><?php echo L_PAGE_BODY ?></label>
				<div class="controls">
					<textarea name="descr" id="descr" class="span12 descr"><?php echo "<p>&nbsp;</p>"; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<fieldset id="additional_images" class="addFiles">
		<?php echo $size_note; ?>
		<legend><?php echo L_PAGE_FILES; tooltip(L_PAGE_ADDITIONAL_FILES, L_PAGE_ADDITIONAL_FILES_HELP); ?></legend>
			<div class="row" id="additional_images1Group">
				<div class="span8" id="third">
					<div class="control-group" id="second">
						<label for="additional_images1" class="control-label"><?php echo L_PAGE_FILE ?> 1</label>
						<div class="controls" id="first">
							<input name="additional_images1" type="file" class="formfields" id="additional_images1" />
						</div>
					</div>
					<div class="control-group">
						<label for="caption1" class="control-label"><?php echo L_CAPTION ?> 1</label><br />
						<div class="controls">
							<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
						</div>
					</div>
				</div>
			</div>
	</fieldset>
	<div class="row">
		<div class="span12">
		<?php $the_module->custom_fields(); ?>
		</div>
	</div>
	<div class="accordion" id="stretchContainer">
		<?php $the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer'); ?>
	</div>
	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
	</div>
<?php endif;
#If you're editing content, use this block
if(EDITING):
	if(EDIT_SINGLE):
		$format = new format;
	?>
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="title" class="control-label"><?php echo L_PAGE_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span6 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
				<div class="control-group">
					<label for="pid" class="control-label"><?php echo L_PAGE_PARENT; ?></label>
					<div class="controls">
						<select id="pid" name="pid" class="span6">
							<option><?php echo L_PAGE_PARENT_NONE; ?></option>
							<?php
							get_page_dropdown($items->pid);
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="dir_title" class="control-label"><?php echo L_PAGE_TEMPLATE_SAFE_TITLE ?></label>
					<div class="controls">
						<input name="dir_title" type="text" class="formfields" id="dir_title" value="<?php echo $items->dirtitle ;?>" />
						<?php tooltip(L_PAGE_TEMPLATE_SAFE_TITLE, sprintf(L_PAGE_TEMPLATE_SAFE_TITLE_HELP, CMS_NAME)); ?>
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="control-group">
					<label for="optionsCheckboxes" class="control-label">Post options</label>
					<div class="controls">
						<label for="online" class="checkbox">
							<input type="hidden" name="online" id="" value="0" />
							<input type="checkbox" name="online" id="online" <?php echo ($items->online == 1) ? 'checked="checked"' : '';?> value="1" />
							<?php echo L_ONLINE ?>
						</label>
						<label for="smilies" class="checkbox">
							<input type="hidden" name="smilies" value="0" />
							<input type="checkbox" name="smilies" id="smilies" <?php echo ($items->smilies == 1) ? 'checked="checked"' : '';?>  value="1" />
							<span><?php echo L_USE_SMILIES ?></span>
						</label>
						<label for="comments" class="checkbox">
							<input type="hidden" name="comments" value="0" id="" />
							<input type="checkbox" name="comments" id="comments" <?php echo ($items->comments == 1) ? 'checked="checked"' : ''; ?> value="1" />
							<span><?php echo L_ALLOW_COMMENTS ?></span>
						</label>
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="pull-right">
				<?php echo preview_link(); ?>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="descr" class="control-label"><?php echo L_PAGE_BODY ?></label>
					<div class="controls">
						<textarea name="descr" id="descr" class="span12 descr"><?php echo ($items->descr !== '') ? view($format->HTML($items->descr)) : "<p>&nbsp;</p>"; ?></textarea>
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
							<div class="control-group">
								<label for="delete_additional<?php echo $ximg->id ?>" class="control-label"><?php echo L_PAGE_ADDITIONAL_FILES_DELETE ?></label>
								<div class="controls">
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
						<div class="control-group" id="second">
							<label for="additional_images1" class="control-label"><?php echo L_PAGE_FILE." $count"; ?></label>
							<div class="controls" id="first">
								<input name="additional_images1" type="file" class="formfields" id="additional_images1" />
							</div>
						</div>
						<div class="control-group">
							<label for="caption1" class="control-label"><?php echo L_PAGE_FILE_CAPTION." $count"; ?></label><br />
							<div class="controls">
								<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
							</div>
						</div>
					</div>
				</div>
		</fieldset>
		<div class="row">
			<div class="span12">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>
		<div class="accordion" id="stretchContainer">
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
		<div class="row">
				<?php $the_module->doSort(); ?>
			<div class="span12">
				<?php
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
					if($has_subcat) {
						$sections->Get($item->cid);
						$category = $sections->sectionname;
					}
					?>
					<div id="item_<?php echo $item->id ?>" title="<?php echo strip_tags($item->descr) ?>">
						<span class="pull-right <?php echo ($item->online == 0) ? 'label' : 'label label-success'; ?>"><?php echo ($item->online == 0) ? L_ITEM_OFFLINE : L_ITEM_ONLINE; ?></span>
						<h1><?php echo $item->title ?></h1>
						<p><?php printf(L_POSTED_BY, $the_displayname, $the_username) ?></p>
						<ul>
							<?php get_page_list(0, $item->id); ?>
						</ul>
						<a href="<?php echo edit_link($item->id); ?>" title="<?php echo L_EDIT_ITEM ?>" class="btn btn-success"><?php echo L_EDIT_ITEM ?></a>
						<a href="<?php echo edit_link($item->id); ?>#sharing" title="<?php echo L_SHARE_ITEM ?>" class="btn shareLink"><?php echo L_SHARE_ITEM ?></a>
						<fieldset>
							<input type="checkbox" name="del[]" value="<?php echo $item->id; ?>" id="item_delete_<?php echo $item->id; ?>" /><label for="item_delete_<?php echo $item->id; ?>"><?php echo L_DELETE_ITEM ?></label>
						</fieldset>
					</div>
				<?php endforeach; ?>

					<input type="hidden" value="<?php echo getOption('sortcats'); ?>" id="order_by" />
				</div>

				<div class="form-actions">
					<a class="btn btn-primary" href="index.php?type=add&amp;cat_id=<?php echo $item->pid ?>"><?php echo L_MENU_ADD ?></a>
					<?php if($hasitems): ?>
					<div class="pull-right">
						<input name="submit" type="submit" class="btn btn-danger" id="submit" value="<?php echo L_BUTTON_DELETE ?>" />
					</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>