<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE')) { die('Sorry, but this file cannot be directly viewed.'); }
?>
<script type="text/javascript" src="modules/events/bootstrap-datepicker.js"></script>
<?php
//If you're adding content, use this block
if(ADDING):
?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="pid" value="<?php echo $catid; ?>" />
	<div class="row">
		<div class="span6">
			<div class="control-group">
				<label for="title" class="control-label"><?php echo L_EVENTS_TITLE ?></label>
				<div class="controls">
					<input name="title" type="text" class="span6 formfields" id="title" />
				</div>
			</div>
			<div class="row">
				<div class="span2">
					<div class="control-group">
						<label for="event_date" class="control-label"><?php echo L_EVENTS_DATE ?></label>
						<div class="controls">
							<div class="input-prepend">
								<span class="add-on"><i class="icon-th"></i></span>
								<input name="event_date" type="text" class="span2 formfields" id="event_date" />
							</div>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="control-group">
						<label for="url"><?php echo L_EVENTS_LINK ?></label>
						<div class="controls">
							<div class="input-prepend">
				                <span class="add-on">http://</span>
								<input name="url" type="text" class="span3 formfields" id="url" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="control-group">
				<label for="optionsCheckboxes" class="control-label">Post options</label>
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
	<div class="row">
		<div class="span12">
			<div class="control-group">
				<label for="descr" class="control-label"><?php echo L_EVENTS_BODY ?></label>
					<div class="controls border-descr">
					<textarea name="descr" id="descr" class="formfields descr"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
		<?php $the_module->custom_fields(); ?>
		</div>
	</div>

	<div class="accordion" id="stretchContainer">
	    <?php
		$the_module->more_options(
			array(
			L_CATEGORY_OPTIONS => 'doCategories',
			)
		);
		applyOzoneAction('more_options');
		$the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer');
		?>
	</div>
	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
	</div>
<?php
//If you're editing content, use this block
elseif(EDITING):

	if(EDIT_SINGLE):
?>
		<input type="hidden" name="aid" value="<?php echo $items->aid;?>" />
		<input type="hidden" name="cid" value="<?php echo $items->cid;?>" />
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="title"><?php echo L_EVENTS_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span6 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
				<div class="row">
					<div class="span2">
						<div class="control-group">
							<label for="event_date" class="control-label"><?php echo L_EVENTS_DATE ?></label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on"><i class="icon-th"></i></span>
									<input name="event_date" type="text" class="span2 formfields" id="event_date" value="<?php echo view(date('d F, Y', $items->event_date)); ?>" />
								</div>
							</div>
						</div>
					</div>
					<div class="span3">
						<div class="control-group">
							<label for="url" class="control-label"><?php echo L_EVENTS_LINK ?></label>
							<div class="controls">
								<div class="input-prepend">
					                <span class="add-on">http://</span>
									<input name="url" type="text" class="span3 formfields" id="url" value="<?php echo view($items->url); ?>" />
								</div>
							</div>
						</div>
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
		<div class="row">
			<div class="span12">
				<div class="control-group">
					<label for="descr"><?php echo L_EVENTS_BODY ?></label>
						<div class="controls border-descr">
						<textarea name="descr" id="descr" class="formfields descr"><?php echo view($items->descr); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span12">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>

		<div class="accordion" id="stretchContainer">
			  <?php
			$the_module->more_options(
				array(
				L_CATEGORY_OPTIONS => 'doCategories',
				L_CLEAN_URL_TITLES => 'doCleanURLTitles'
				)
			);
			$the_module->more_options(L_SHARE_ITEM, 'doSharing', 'sharing');
			applyOzoneAction('more_options');
			$the_module->more_options(L_POST_TIME_EDIT, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
		</div>
<?php
	elseif(EDIT_LIST):
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
	endif;
endif;
