<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE')){ die('Sorry, but this file cannot be directly viewed.'); }
$size_note = sprintf('<div class="alert alert-block alert-info fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>%s</p></div>', sprintf(L_SIZE_NOTE,MAX_UPLOAD));

//If you're adding content, use this block
if(ADDING) {
?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="cid" value="<?php echo $catid; ?>" />
	<?php
	//you can add your own custom sections by looking for $_GET variables (or any other conditions...) to check against.
	if(isset($_GET['upload']) && $_GET['upload'] == 'mass') {
		printf(NOTE,sprintf(L_GALLERY_UPLOAD_NOTE,CMS_NAME));
		echo $size_note;
	?>
		<div class="row">
			<div class="span12">
				<div class="pull-right">
					<a href="<?php echo preg_replace('/&upload\=mass+/', '', $_SERVER['REQUEST_URI']) ?>" class="btn btn-primary"><?php echo L_GALLERY_SINGLE_UPLOAD ?></a>
				</div>
			</div>
		</div>
		<div class="alert alert-message alert-info" data-alert="alert"><a class="close" href="#">&times;</a><p><?php echo L_GALLERY_MASS_UPLOAD_FTP_HELP; ?></p></div>
		<div class="row">
			<div class="span5">
				<div class="control-group">
					<label for="massupload" class="control-label"><?php echo L_GALLERY_FILE ?></label>
					<div class="controls">
						<input type="file" name="massupload" class="formfields input-file span5" id="massupload" />
					</div>
				</div>
			</div>
			<div class="span2">
				<p class=""><?php echo L_CONCAT_AND.'/'.L_CONCAT_OR ?></p>
			</div>
			<div class="span5">
				<div class="control-group">
					<label for="mass_upload_ftp" class="control-label"><?php echo L_GALLERY_MASS_UPLOAD_FTP ?></label>
					<div class="controls">
						<input type="text" name="mass_upload_ftp" class="formfields span5" id="mass_upload_ftp" />
					</div>
				</div>
			</div>
		</div>
		<fieldset>
			<legend><?php echo L_GALLERY_OPTIONAL ?></legend>
				<div class="row">
					<div class="span8">
						<div class="control-group">
							<label for="none" class="control-label"><?php echo L_TITLE ?></label>
							<div class="controls">
								<input type="text" class="span8 formfields disabled" id="none" disabled="disabled" />
							</div>
						</div>
						<div class="control-group">
							<label for="materials"><?php echo L_MATERIALS ?></label>
							<div class="controls">
								<input name="materials" type="text" class="span8 formfields" id="materials" />
							</div>
						</div>
					</div>
					<div class="span4">
						<div class="control-group">
							<label for="optionsCheckboxes" class="control-label">Post ptions</label>
							<div class="controls">
								<label for="online" class="checkbox">
									<input name="online" type="checkbox" id="online" value="1" checked="checked" />
									<?php echo L_ONLINE ?>
								</label>
								<label class="checkbox">
									<input name="smilies" type="checkbox" id="smilies" value="1" checked="checked" />
									<span><?php echo L_USE_SMILIES ?></span>
								</label>
								<label class="checkbox">
									<input name="comments" type="checkbox" id="comments" value="1" checked="checked" />
									<span><?php echo L_ALLOW_COMMENTS ?></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="span8">
						<div class="control-group">
							<label for="autothumb" class="checkbox">
								<input name="autothumb" class="cBox" type="checkbox" id="autothumb" value="1" checked="checked" />
								<?php echo L_GALLERY_AUTO_THUMB ?>
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="span12">
						<div class="control-group">
							<label for="descr" class="control-label"><?php echo L_BODY ?></label>
							<div class="controls border-descr">
								<textarea name="descr" id="descr" class="formfields descr"></textarea>
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
		<div class="accordion" id="stretchContainer">
		    <?php
			$the_module->more_options(
				array(
					L_CATEGORY_OPTIONS => 'doCategories', 'simple',
					L_GALLERY_PAYPAL_SETTINGS => 'doPaypal'
				)
			);
			applyOzoneAction('more_options');
			$the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>
		<?php printf(ALERT, L_GALLERY_EDIT_MORE_LATER); ?>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_MASS_UPLOAD ?>" />
		</div>
	<?php
	} else {
	?>
		<div class="row">
			<div class="span12">
				<div class="pull-right">
					<a href="<?php echo $_SERVER['REQUEST_URI'] ?>&upload=mass" class="btn primary"><?php echo L_GALLERY_MASS_UPLOAD ?></a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="control-group">
					<label for="title" class="control-label"><?php echo L_GALLERY_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span8 formfields" id="title" />
					</div>
				</div>
				<div class="control-group">
					<label for="materials" class="control-label"><?php echo L_MATERIALS ?></label>
					<div class="controls">
						<input name="materials" type="text" class="span8 formfields" id="materials" />
					</div>
				</div>
			</div>
			<div class="span2">
				<div class="control-group">
					<label for="optionsCheckboxes" class="control-label">Post options</label>
					<div class="controls">
						<label for="online" class="checkbox">
							<input name="online" class="cBox" type="checkbox" id="online" value="1" checked="checked" />
							<?php echo L_ONLINE ?>
						</label>
						<label class="checkbox">
							<input name="smilies" class="cBox" type="checkbox" id="smilies" value="1" checked="checked" />
							<span><?php echo L_USE_SMILIES ?></span>
						</label>
						<label class="checkbox">
							<input name="comments" class="cBox" type="checkbox" id="comments" value="1" checked="checked" />
							<span><?php echo L_ALLOW_COMMENTS ?></span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<?php echo $size_note; ?>
		<div class="row">
			<div class="span8">
				<div class="control-group">
					<label for="img_main" class="control-label"><?php echo L_IMAGE ?></label>
					<div class="controls">
						<input name="img_main" type="file" class="span8 formfields" id="img_main" />
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="control-group">
					<label for="autothumb" class="control-label"><?php echo L_GALLERY_AUTO_THUMB ?></label>
					<div class="controls">
						<input type="checkbox" name="autothumb" class="cBox" id="autothumb" value="1" checked="checked" />
					</div>
				</div>
			</div>
			<div class="span8">
				<div id="thumbField">
					<div class="control-group">
						<label for="img_thumb" class="control-label" id="iTLabel"><?php echo L_THUMBNAIL ?></label>
						<div class="controls">
							<input name="img_thumb" type="file" class="span8 formfields" id="img_thumb" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<fieldset id="additional_images">
			<legend><?php echo L_GALLERY_ADDITIONAL_IMAGES;tooltip(L_GALLERY_ADDITIONAL_IMAGES, L_GALLERY_ADDITIONAL_IMAGES_HELP) ?></legend>
				<div class="row" id="additional_images1Group">
					<div class="span8" id="third">
						<div class="control-group" id="second">
							<label for="additional_images1" class="control-label"><?php echo L_IMAGE ?> 1</label>
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
				<div class="control-group">
					<label for="descr"><?php echo L_GALLERY_BODY ?></label>
					<div class="controls border-descr">
						<textarea name="descr" cols="30" rows="5" id="descr" class="span12 descr formfields"></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="span10">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>
		<div class="accordion" id="stretchContainer">
		    <?php
			$the_module->more_options(
				array(
					L_CATEGORY_OPTIONS => 'doCategories',
					L_GALLERY_PAYPAL_SETTINGS => 'doPaypal'
				)
			);
			applyOzoneAction('more_options');
			$the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
		</div><?php
	}
}

if(EDITING) {
	//If you're editing one specific item, use this block
	if(EDIT_SINGLE) {
	?>
		<input type="hidden" name="aid" value="<?php echo $items->aid;?>" />
		<input type="hidden" name="cid" value="<?php echo $items->cid;?>" />
		<div class="row">
			<div class="span8">
				<div class="control-group">
					<label for="title" class="control-label"><?php echo L_GALLERY_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span8 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
				<div class="control-group">
					<label for="materials" class="control-checkbox"><?php echo L_MATERIALS ?></label>
					<div class="controls">
						<input name="materials" type="text" class="span8 formfields" id="materials" value="<?php echo view($items->materials);?>" />
					</div>
				</div>
			</div>
			<div class="span2">
				<div class="control-group">
					<label for="optionsCheckboxes" class="control-label">Post options</label>
					<div class="controls">
						<label for="online" class="checkbox">
							<input type="hidden" name="online" id="" value="0" />
							<input type="checkbox" name="online" id="online" <?php echo ($items->online == 1) ? 'checked="checked"' : '';?> value="1" class="cBox" />
							<?php echo L_ONLINE ?>
						</label>
						<label for="smilies" class="checkbox">
							<input type="hidden" name="smilies" value="0" />
							<input type="checkbox" name="smilies" id="smilies" <?php echo ($items->smilies == 1) ? 'checked="checked"' : '';?>  value="1" class="cBox" />
							<?php echo L_USE_SMILIES ?>
						</label>
						<label for="comments" class="checkbox">
							<input type="hidden" name="comments" value="0" id="" />
							<input type="checkbox" name="comments" id="comments" <?php echo ($items->comments == 1) ? 'checked="checked"' : ''; ?> value="1" class="cBox" />
							<?php echo L_ALLOW_COMMENTS ?>
						</label>
					</div>
				</div>
			</div>
			<div class="span2">
				<?php echo preview_link(); ?>
			</div>
		</div>
		<?php echo $size_note; ?>
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="img_main" class="control-label"><?php echo L_IMAGE ?></label>
					<div class="controls">
						<input type="file" name="img_main" class="formfields" id="img_main" size="50" />
					</div>
				</div>
				<img id="canvasImg" style="display:none;" width="<?php echo $items->width; ?>" height="<?php echo $items->height; ?>" src="funcs/tn.lib.php?id=<?php echo $item_id ?>&amp;dim=11024&amp;<?php echo time(); ?>" />
				<div class="control-group">
					<label for="autothumb" class="checkbox">
						<input type="hidden" name="autothumb" value="0" />
						<input class="cBox" type="checkbox" id="autothumb" value="1" name="autothumb"  <?php echo ($items->autothumb == 1) ? 'checked="checked"' : '';?> />
						<?php echo L_GALLERY_AUTO_THUMB ?>
					</label>
				</div>
				<img style="display:none;" src="funcs/tn.lib.php?thumb=1&dim=<?php echo $items->width ?>&id=<?php echo $item_id ?>&amp;<?php echo time(); ?>&amp;ignore_scale=1" alt="<?php echo $items->thumbnail ?>" id="scaleImg" name="scaleImg" />
			</div>
			<div class="span6">
				<p id="thumbNails">
					<img src="funcs/tn.lib.php?dim=200&amp;id=<?php echo $items->id ?>&amp;<?php echo time(); ?>" alt="<?php echo $items->image ?>" id="thumbNailMain" class="thumbnails" />
				 	<img class="thumbnails" src="funcs/tn.lib.php?dim=75<?php echo (!$items->autothumb) ? '&manual=1' : '' ?>&thumb=1&id=<?php echo $item_id ?>&amp;<?php echo time(); ?>" alt="<?php echo $items->thumbnail ?>" id="thumbNailThumb" />
				</p>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div id="thumbField">
					<div class="control-group">
						<label for="img_thumb" id="iTLabel"><?php echo L_THUMBNAIL ?></label>
						<div class="controls">
							<input name="img_thumb" type="file" class="formfields" id="img_thumb" size="50" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<fieldset id="additional_images">
					<legend><?php echo L_GALLERY_ADDITIONAL_IMAGES;tooltip(L_GALLERY_ADDITIONAL_IMAGES, L_GALLERY_ADDITIONAL_IMAGES_HELP) ?></legend>
						<div id="extraImages">
							<?php
							$add_images = $images->GetList(array(array('itemid', '=', $item_id)));
							$count = count($add_images);
							$count++;
							foreach($add_images as $ximg) {
								?>
								<div class="imgBox">
									<img src="funcs/tn.lib.php?image_id=<?php echo $ximg->id ?>&amp;dim=100" title="<?php echo view($ximg->caption); ?>" alt="<?php echo view($ximg->caption); ?>" id="addImg<?php echo $ximg->id ?>" class="thumbnails hasHelp" />
									<blockquote class="helpContents" id="addImg<?php echo $ximg->id ?>Help"><h5>Image caption</h5><?php echo view($ximg->caption); ?></blockquote>
									<div class="control-group">
										<label for="delete_additional<?php echo $ximg->id ?>" class="checkbox">
											<input type="checkbox" id="delete_additional<?php echo $ximg->id ?>" name="delete_additional[]" value="<?php echo $ximg->id ?>" class="xtraImgDelete" />
											<?php echo L_GALLERY_ADDITIONAL_IMAGES_DELETE ?>
										</label>
									</div>
								</div>
								<?php
							}
							?>
						</div>
						<div class="row" id="additional_images1Group">
							<div class="span8" id="third">
								<div class="control-group" id="second">
									<label for="additional_images1" class="control-label"><?php echo L_IMAGE ?></label>
									<div class="controls" id="first">
										<input type="file" name="additional_images1" class="formfields" id="additional_images1" />
									</div>
								</div>
								<div class="control-group">
									<label for="caption1" class="control-label"><?php echo L_CAPTION ?></label>
									<div class="controls">
										<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
									</div>
								</div>
							</div>
						</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<div class="control-group">
					<label for="descr" class="contol-label"><?php echo L_GALLERY_BODY ?></label>
					<div class="controls border-descr">
						<textarea name="descr" cols="30" rows="5" id="descr" class="descr formfields"><?php echo view($items->descr); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span6">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<div class="accordion" id="stretchContainer">
					<?php
					$the_module->more_options(
						array(
						L_CATEGORY_OPTIONS => 'doCategories',
						L_CLEAN_URL_TITLES => 'doCleanURLTitles',
						L_GALLERY_PAYPAL_SETTINGS => 'doPaypal'
						)
					);
					$the_module->more_options(L_GALLERY_THUMBNAIL_SETTINGS,'doThumbnails','thumbInfoContents');
					$the_module->more_options(L_SHARE_ITEM, 'doSharing', 'sharing');
					$the_module->more_options(L_POST_TIME_EDIT, 'doDateTimeForms', 'editDateTimeContainer');
					?>
				</div>
			</div>
		</div>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
		</div>
	<?php
	} elseif(EDIT_LIST) {
	?>
		<div class="row">
				<?php $the_module->doSort(); ?>
			<div class="span12">
				<?php
					$itemsList = paginate($itemsList, '', EDIT_LIMIT);
					$numitems = count($itemsList);
					$hasitems = $numitems > 0 ? true : false;
				?>

				<div id="itemList">
				<?php
				foreach($itemsList as $ind => $item):
					$item->title = trim_title($item->title);
					$item->descr = trim_excerpt($item->descr);
					$users->Get($item->aid);
					$the_displayname = $users->displayname;
					$the_username = $users->username;
					$has_subcat = ($item->cid != $item->pid && $item->cid != 0) ? true : false;
					if($has_subcat) {
						$sections->Get($item->cid);
						$category = $sections->sectionname;
						if(empty($category)) {
							$has_subcat = false;
						}
					}

					?>
					<div id="item_<?php echo $item->id ?>" title="<?php echo strip_tags($item->descr) ?>">
						<span class="pull-right <?php echo ($item->online == 0) ? 'label' : 'label label-success'; ?>"><?php echo ($item->online == 0) ? L_ITEM_OFFLINE : L_ITEM_ONLINE; ?></span>
						<img src="funcs/tn.lib.php?dim=70&thumb=1&id=<?php echo $item->id ?>" alt="<?php echo $item->thumbnail ?>" />
						<h1><?php echo $item->title ?></h1>
						<p><?php printf(L_POSTED_BY, $the_displayname, $the_username) ?></p>
						<?php if($has_subcat){ ?>
							<h3><?php echo L_SUB_CATEGORY ?>: <?php echo $category; ?></h3>
						<?php } ?>
						<a href="<?php echo edit_link($item->id); ?>" title="<?php echo L_EDIT_ITEM ?>" class="btn btn-success"><?php echo L_EDIT_ITEM ?></a>
						<a href="<?php echo edit_link($item->id); ?>#sharing" title="<?php echo L_SHARE_ITEM ?>" class="btn shareLink"><?php echo L_SHARE_ITEM ?></a>
						<fieldset>
							<input type="checkbox" name="del[]" value="<?php echo $item->id; ?>" id="item_delete_<?php echo $item->id; ?>" /><label for="item_delete_<?php echo $item->id; ?>"><?php echo L_DELETE_ITEM ?></label>
						</fieldset>
					</div>
				<?php
				endforeach;
				?>

					<input type="hidden" value="<?php echo getOption('sortcats'); ?>" id="order_by" />
				</div>
				<?php
				if($hasitems):
				?>
				<div class="form-actions">
					<div class="pull-right">
						<input name="submit" type="submit" class="btn btn-danger" id="submit" value="<?php echo L_BUTTON_DELETE ?>" />
					</div>
				</div>
				<?php
				endif;
				?>
			</div>
		</div>
<?php
	}
}
