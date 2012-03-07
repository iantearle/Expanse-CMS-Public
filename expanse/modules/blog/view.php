<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}
?>
<?php
//If you're adding content, use this block
if(ADDING):
	?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="cid" value="<?php echo $catid; ?>" />
	<div class="row">
		<div class="span8">
			<div class="clearfix">
				<label for="title"><?php echo L_TITLE ?></label>
				<div class="input">
					<input name="title" type="text" class="span8 formfields" id="title" />
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
				<label for="descr"><?php echo L_BODY ?></label>
					<div class="input border-descr">
					<textarea name="descr" id="descr" class="formfields descr"><?php echo @$_POST['descr']; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span10">
		<?php $the_module->custom_fields(); ?>
		</div>
	</div>

	<div class="stretchContainer">
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
	<div class="actions">
		<input name="submit" type="submit" class="btn primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
	</div>
<?php
//If you're editing content, use this block
elseif(EDITING):
	if(EDIT_SINGLE): ?>
		<input type="hidden" name="aid" value="<?php echo $items->aid;?>" />
		<input type="hidden" name="cid" value="<?php echo $items->cid;?>" />
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
					<label for="title"><?php echo L_TITLE ?></label>
					<div class="input">
						<input name="title" type="text" class="span8 formfields" id="title" value="<?php echo view($items->title);?>" />
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
					<label for="descr"><?php echo L_BODY ?></label>
						<div class="input border-descr">
						<textarea name="descr" cols="60" rows="5" id="descr" class="formfields descr"><?php echo view($items->descr); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span10">
			<?php $the_module->custom_fields(); ?>
			</div>
		</div>

		<div class="stretchContainer">
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
		<div class="actions">
			<input name="submit" type="submit" class="btn primary" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
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
					<p class="<?php echo ($item->online == 0) ? 'offline' : 'online'; ?>"><?php echo ($item->online == 0) ? L_ITEM_OFFLINE : L_ITEM_ONLINE; ?></p>
					<p><?php printf(L_POSTED_BY, $the_displayname, $the_username) ?></p>
					<?php if($has_subcat){ ?>
						<h3><?php echo L_SUB_CATEGORY ?>: <?php echo $category; ?></h3>
					<?php } ?>
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
