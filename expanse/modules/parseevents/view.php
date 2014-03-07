<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE') || !$auth->Authorized){die('<div class="alert alert-message alert-danger fade in" data-alert="alert"><p>You have no permissions to edit this file.</p></div>');}
date_default_timezone_set('UTC');

?>
<script type="text/javascript" src="modules/parseevents/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="modules/parseevents/bootstrap-timepicker.js"></script>
<?php
//If you're adding content, use this block
if(ADDING):
	$Database = new DatabaseConnection();
	$name_array = $Database->GetAssoc('SELECT DISTINCT title, id FROM '.PREFIX.'items WHERE online=1');

	$typeahead_string = '';
	foreach($name_array as $name) {
	    $formatted_name    =  '&quot;' . trim($name['title']) . ' [' .$name['id'] . ']' . '&quot;, ';
	    $typeahead_string .= $formatted_name;
	}

	$option_list = rtrim($typeahead_string, ', ');  //Strips the last comma and any whitespace from the end string

	$data['typeahead'] = $option_list;

	$typeahead = $data['typeahead'];
//	echo $data['typeahead'];
?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="pid" value="<?php echo $catid; ?>" />
	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<label for="related">Property</label>
				<div class="controls">
					<input type="text" value="" name="related" id="related" data-provide="typeahead" data-items="4" data-source="[<?php echo $typeahead; ?>]" class="" autocomplete="off" />
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span10">
			<div class="row-fluid">
				<div class="span5">
					<div class="control-group">
						<label for="title" class="control-label"><?php echo L_EVENTS_TITLE ?></label>
						<div class="controls">
							<input name="title" type="text" class="span5 formfields" id="title" />
						</div>
					</div>
				</div>
				<div class="span5">
					<div class="control-group">
						<label for="url"><?php echo L_EVENTS_LINK ?></label>
						<div class="controls">
							<div class="input-prepend">
				                <span class="add-on">http://</span>
								<input name="url" type="text" class="span4 formfields" id="url" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span5">
					<div class="control-group">
						<label for="event_date_start" class="control-label"><?php echo L_EVENTS_DATE_START ?></label>
						<div class="controls">
							<input type="text" name="event_date_start" class="span3" id="event_date_start" value="" />
							<input type="text" name="event_time_start" class="span2 timepicker-default" id="event_time_start" value="" />
						</div>
					</div>
				</div>
				<div class="span5">
					<div class="control-group">
						<label for="event_date_end" class="control-label"><?php echo L_EVENTS_DATE_END ?></label>
						<div class="controls">
							<input type="text" name="event_date_end" class="span3" id="event_date_end" value="" />
							<input type="text" name="event_time_end" class="span2 timepicker-default" id="event_time_end" value="" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="span2">
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
	<div class="descr-well">
		<div class="row-fluid">
			<div class="control-group">
				<label for="descr" class="control-label"><?php echo L_EVENTS_BODY ?></label>
				<div class="controls">
					<textarea name="descr" id="descr" class="span12 descr"><?php echo "<p>&nbsp;</p>"; ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
	</div>
<?php
//If you're editing content, use this block
elseif(EDITING):
	if(EDIT_SINGLE):
		$format = new format;
	?>
		<input type="hidden" name="aid" value="<?php echo $items->aid;?>" />
		<input type="hidden" name="cid" value="<?php echo $items->cid;?>" />
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="related">Property</label>
					<div class="controls">
						<input type="text" name="related" id="related" class="disabled" disabled="disabled" value="<?php echo $items->related;?>" />
						<p><small><a href="/expanse/index.php?type=edit&cat_id=2&id=<?php echo $items->related;?>">View property</a></small></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="title" class="control-label"><?php echo L_EVENTS_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span6 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<div class="control-group">
							<label for="event_date_start" class="control-label"><?php echo L_EVENTS_DATE_START ?></label>
							<div class="controls">
								<input type="text" name="event_date_start" class="span2" id="event_date_start" value="<?php echo view(date('d F, Y', $items->event_date_start)); ?>" />
								<input type="text" name="event_time_start" class="span1 timepicker-default" id="event_time_start" value="<?php echo view(date('h:i A', $items->event_date_start)); ?>" />
							</div>
						</div>
					</div>
					<div class="span3">
						<div class="control-group">
							<label for="event_date_end" class="control-label"><?php echo L_EVENTS_DATE_END ?></label>
							<div class="controls">
								<input type="text" name="event_date_end" class="span2" id="event_date_end" value="<?php echo view(date('d F, Y', $items->event_date_end)); ?>" />
								<input type="text" name="event_time_end" class="span1 timepicker-default" id="event_time_end" value="<?php echo view(date('h:i A', $items->event_date_end)); ?>" />
							</div>
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
							<input name="url" type="text" class="span2 formfields" id="url" value="<?php echo view($items->url); ?>" />
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
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="descr" class="control-label"><?php echo L_EVENTS_BODY ?></label>
					<div class="controls">
						<textarea name="descr" id="descr" class="span12 descr"><?php echo ($items->descr !== '') ? view($format->HTML($items->descr)) : "<p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
		</div>
	<?php elseif(EDIT_LIST): ?>
		<?php $the_module->doSort(); ?>
		<div class="row-fluid">
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
					if($has_subcat){
						$sections->Get($item->cid);
						$category = $sections->sectionname;
						if(empty($category)){
							$has_subcat = false;
						}
					}

					?>
					<div id="item_<?php echo $item->id ?>" title="<?php echo strip_tags($item->descr) ?>">
						<span class="pull-right <?php echo ($item->online == 0) ? 'label' : 'label label-success'; ?>"><?php echo ($item->online == 0) ? L_ITEM_OFFLINE : L_ITEM_ONLINE; ?></span>
						<h1><?php echo $item->title ?></h1>
						<p><?php echo date('d F, Y h:i A', $item->event_date_start); ?></p>
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