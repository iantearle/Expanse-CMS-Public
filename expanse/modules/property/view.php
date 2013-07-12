<?php
/**************************************************
HTML below. No form tag
(though if you'd like to include another form,
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}

$size_note = sprintf('<div class="alert alert-message alert-info" data-alert="alert"><a class="close" href="#">&times;</a>%s</div>', sprintf(L_SIZE_NOTE,MAX_UPLOAD));
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"></script>
<?php

//If you're adding content, use this block
if(ADDING):  ?>
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="pid" value="<?php echo $catid; ?>" />

		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="title" class="control-label"><?php echo L_PROPERTY_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span6 formfields" id="title" />
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
						<label for="comments" class="checkbox">
							<input name="comments" type="checkbox" id="comments" value="1" checked="checked" />
							<span><?php echo L_ALLOW_PROPERTY_REVIEWS ?></span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="address" class="control-label">Address Line 1</label>
					<div class="controls">
						<input name="address" type="text" class="span6 formfields" id="address" />
					</div>

					<label for="county" class="control-label">County</label>
					<div class="controls">
						<input name="county" type="text" class="span6 formfields" id="county" />
					</div>

					<label for="postcode" class="control-label">Postcode</label>
					<div class="control-group">
						<input name="postcode" type="text" class="formfields" id="postcode" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="control-group">
					<label for="latitude" class="control-label">Latitude (57.0000)</label>
					<div class="controls">
						<input name="latitude" type="text" class="formfields" id="latitude" />
					</div>

					<label for="longitude" class="control-label">Longitude (1.0000)</label>
					<div class="controls">
						<input name="longitude" type="text" class="formfields" id="longitude" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="well">
					<p>We will try and calculate these for you, based upon the entry you enter in the Postcode field.</p>
					<p>If we fail to geo-locate your address please click <a href="javascript: void(0);" id="getPostcode">here</a>.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="email" class="control-label">Email</label>
					<div class="controls">
						<input name="email" type="text" class="span6 formfields" id="email" />
					</div>

					<label for="telephone" class="control-label">Telephone</label>
					<div class="controls">
						<input name="telephone" type="text" class="span6 formfields" id="telephone" />
					</div>

					<label for="url" class="control-label">Website</label>
					<div class="controls">
						<input name="url" type="text" class="span6 formfields" id="url"  />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label for="twitter" class="control-label">Twitter</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">@</span>
							<input name="twitter" type="text" class="formfields" id="twitter" />
						</div>
						<span class="help-block">Enter only your <span class="label label-info">username</span></span>
					</div>

					<label for="facebook" class="control-label">Facebook</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">&#9823;</span>
							<input name="facebook" type="text" class="formfields" id="facebook" />
						</div>
						<span class="help-block">Enter only your <span class="label label-info">username</span> if you have one</span>
					</div>
				</div>
			</div>
		</div>

		<?php echo $size_note; ?>
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="img_main" class="control-label"><?php echo L_IMAGE ?></label>
					<div class="controls">
						<input name="img_main" type="file" class="span6 formfields" id="img_main" />
						<input type="hidden" name="autothumb" id="autothumb" value="1" />
					</div>
				</div>
			</div>

			<div class="span6">
				<fieldset id="additional_images">
					<legend><?php echo L_GALLERY_ADDITIONAL_IMAGES;tooltip(L_GALLERY_ADDITIONAL_IMAGES, L_GALLERY_ADDITIONAL_IMAGES_HELP) ?></legend>
						<div class="row" id="additional_images1Group">
							<div class="span6" id="third">
								<div class="clearfix" id="second">
									<label for="additional_images1" class="control-label"><?php echo L_IMAGE ?> 1</label>
									<div class="controls" id="first">
										<input name="additional_images1" type="file" class="formfields" id="additional_images1" />
									</div>
								</div>
								<div class="control-group">
									<label for="caption1" class="control-label"><?php echo L_CAPTION ?> 1</label>
									<div class="controls">
										<textarea name="caption[additional_images1]" class="caption" id="caption1"></textarea>
									</div>
								</div>
							</div>
						</div>
				</fieldset>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="descr" class="control-label"><?php echo L_GALLERY_BODY ?></label>
					<div class="controls">
						<textarea name="descr" id="descr" class="span12 descr"><?php echo " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="admission" class="control-label">Admission</label>
					<div class="controls">
						<textarea name="admission" id="admission" class="span12 descr-admission"><?php echo " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="events" class="control-label">Events</label>
					<div class="controls">
						<textarea name="events" id="events" class="span12 descr-events"><?php echo " <p>&nbsp;</p>"; ?></textarea>
						<?php helpBlock(L_PROPERTY_EVENTS_HELP); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="dates" class="control-label">Dates</label>
					<div class="controls">
						<textarea name="dates" id="dates" class="span12 descr-dates"><?php echo " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="descr-well">
					<div class="row-fluid">
						<div class="control-group">
							<label for="other" class="control-label">Other</label>
							<div class="controls">
								<textarea name="other" id="other" class="span12 descr-other"><?php echo " <p>&nbsp;</p>"; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="span4">
				<div class="control-group">
					<label id="optionsCheckboxes" class="control-label">Facilities</label>
					<div class="controls">
						<label for="shop" class="checkbox">
							<input name="shop" type="checkbox" id="shop" value="0" />
							<span>Shop</span>
						</label>
						<label for="plantsales" class="checkbox">
							<input name="plantsales" type="checkbox" id="plantsales" value="0" />
							<span>Plantsales</span>
						</label>
						<label for="cafe" class="checkbox">
							<input type="checkbox" name="cafe" id="cafe" value="0" />
							<span>Cafe</span>
						</label>
						<label for="restaurant" class="checkbox">
							<input type="checkbox" name="restaurant" id="restaurant" value="0" />
							<span>Restaurant</span>
						</label>
						<label for="civilweddinglicense" class="checkbox">
							<input type="checkbox" name="civilweddinglicense" id="civilweddinglicense" value="0" />
							<span>Civil Wedding License</span>
						</label>
						<label for="audiotours" class="checkbox">
							<input type="checkbox" name="audiotours" id="audiotours" value="0" />
							<span>Audio Tours</span>
						</label>
						<label for="nodogs" class="checkbox">
							<input type="checkbox" name="nodogs" id="nodogs" value="0" />
							<span>Dogs Disallowed</span>
						</label>
						<label for="openallyear" class="checkbox">
							<input type="checkbox" name="openallyear" id="openallyear" value="0" />
							<span>Open All Year</span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="accordion" id="stretchContainer">
			<?php
			$the_module->more_options(
				array(
					L_CATEGORY_OPTIONS => 'doCategories'
				)
			);
			applyOzoneAction('more_options');
			$the_module->more_options(L_POST_TIME_ADD, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>
		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
		</div>
<?php endif; ?>

<?php
//If you're editing content, use this block
if(EDITING):
	//If you're editing one specific item, use this block
	if(EDIT_SINGLE):
		$format = new format;
	?>
		<?php
			if($_SESSION['id'] == 2 || $_SESSION['id'] == $items->aid) {

			} else {
				echo '<div class="alert alert-message alert-danger fade in" data-alert="alert"><a class="close" href="#">×</a><p>You have no permssions to edit this file.</p></div>';
				exit;
			}
		?>
		<input type="hidden" name="aid" value="<?php echo $items->aid;?>" />
		<input type="hidden" name="cid" value="<?php echo $items->cid;?>" />

		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="title"><?php echo L_PROPERTY_TITLE ?></label>
					<div class="controls">
						<input name="title" type="text" class="span6 formfields" id="title" value="<?php echo view($items->title);?>" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="control-group">
					<label for="optionsCheckboxes">Post options</label>
					<div class="controls">
						<label for="online" class="checkbox">
							<input type="hidden" name="online" id="" value="0" />
							<input type="checkbox" name="online" id="online" <?php echo ($items->online == 1) ? 'checked="checked"' : '';?> value="1" />
							<?php echo L_ONLINE ?>
						</label>
						<label for="comments" class="checkbox">
							<input type="hidden" name="comments" value="0" id="" />
							<input type="checkbox" name="comments" id="comments" <?php echo ($items->comments == 1) ? 'checked="checked"' : ''; ?> value="1" />
							<?php echo L_ALLOW_PROPERTY_REVIEWS ?>
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

		<hr>

		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="address">Address Line 1</label>
					<div class="controls">
						<input name="address" type="text" class="span6 formfields" id="address" value="<?php echo $items->address;?>" />
					</div>
				</div>
				<div class="control-group">
					<label for="county">County</label>
					<div class="controls">
						<input name="county" type="text" class="span6 formfields" id="county" value="<?php echo view($items->county);?>" />
					</div>
				</div>
				<div class="control-group">
					<label for="postcode">Postcode</label>
					<div class="control-group">
						<input name="postcode" type="text" class="formfields" id="postcode" value="<?php echo view($items->postcode);?>" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="control-group">
					<label for="latitude">Latitude (57.0000)</label>
					<div class="controls">
						<input name="latitude" type="text" class="formfields" id="latitude" value="<?php echo view($items->latitude);?>" />
					</div>
				</div>
				<div class="control-group">
					<label for="longitude">Longitude (1.0000)</label>
					<div class="controls">
						<input name="longitude" type="text" class="formfields" id="longitude" value="<?php echo view($items->longitude);?>" />
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="well">
					<p>We will try and calculate these for you, based upon the entry you enter in the Postcode field.</p>
					<p>If we fail to geo-locate your address please click <a href="javascript: void(0);" id="getPostcode">here</a>.</p>
				</div>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="email">Email</label>
					<div class="controls">
						<input name="email" type="text" class="span6 formfields" id="email" value="<?php echo view($items->email);?>" />
					</div>

					<label for="telephone">Telephone</label>
					<div class="controls">
						<input name="telephone" type="text" class="span6 formfields" id="telephone" value="<?php echo view($items->telephone);?>" />
					</div>

					<label for="url">Website</label>
					<div class="controls">
						<input name="url" type="text" class="span6 formfields" id="url" value="<?php echo view($items->url);?>" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label for="twitter">Twitter</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">@</span>
							<input name="twitter" type="text" class="formfields" id="twitter" value="<?php echo view($items->twitter);?>" />
						</div>
						<span class="help-block">Enter only your <span class="label label-info">username</span></span>
					</div>

					<label for="facebook">Facebook</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">&#9823;</span>
							<input name="facebook" type="text" class="formfields" id="facebook" value="<?php echo view($items->facebook);?>" />
						</div>
						<span class="help-block">Enter only your <span class="label label-info">username</span></span>
					</div>
				</div>
			</div>
		</div>

		<?php echo $size_note; ?>
		<div class="row">
			<div class="span6">
				<div class="control-group">
					<label for="img_main"><?php echo L_IMAGE ?></label>
					<div class="controls">
						<input name="img_main" type="file" class="formfields" id="img_main" size="50" />
						<input type="hidden" name="autothumb" id="autothumb" value="1" />
					</div>
				</div>
				<ul class="thumbnails">
					<li class="span3">
						<img src="funcs/tn.lib.php?dim=200&amp;id=<?php echo $items->id ?>&amp;<?php echo time(); ?>" alt="<?php echo $items->image ?>" id="thumbNailMain" />
					</li>
				 </ul>
			</div>
			<div class="span6">
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
									<div class="control-group">
										<label for="delete_additional<?php echo $ximg->id ?>"><?php echo L_GALLERY_ADDITIONAL_IMAGES_DELETE ?></label>
										<div class="controls">
											<input type="checkbox" id="delete_additional<?php echo $ximg->id ?>" name="delete_additional[]" value="<?php echo $ximg->id ?>" class="xtraImgDelete" />
										</div>
									</div>
									<img src="funcs/tn.lib.php?image_id=<?php echo $ximg->id ?>&amp;dim=50" title="<?php echo view($ximg->caption); ?>" alt="<?php echo view($ximg->caption); ?>" id="addImg<?php echo $ximg->id ?>" class="hasHelp" />
									<blockquote class="helpContents" id="addImg<?php echo $ximg->id ?>Help"><h5>Image caption</h5><?php echo view($ximg->caption); ?></blockquote>
								</div>
								<?php
							}
							?>
						</div>
						<div class="row" id="additional_images1Group">
							<div class="span6" id="third">
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
			</div>
		</div>

		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="descr" class="control-label"><?php echo L_GALLERY_BODY ?></label>
					<div class="controls">
						<textarea name="descr" id="descr" class="span12 descr"><?php echo ($items->descr !== '') ? view($format->HTML($items->descr)) : " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="admission" class="control-label">Admission</label>
					<div class="controls">
						<textarea name="admission" id="admission" class="span12 descr-admission"><?php echo ($items->admission !== '') ? view($format->HTML($items->admission)) : " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="events" class="control-label">Events</label>
					<div class="controls">
						<textarea name="events" id="events" class="span12 descr-events"><?php echo ($items->events !== '') ? view($format->HTML($items->events)) : " <p>&nbsp;</p>"; ?></textarea>
						<?php helpBlock(L_PROPERTY_EVENTS_HELP); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="descr-well">
			<div class="row-fluid">
				<div class="control-group">
					<label for="dates" class="control-label">Dates</label>
					<div class="controls">
						<textarea name="dates" id="dates" class="span12 descr-dates"><?php echo ($items->dates !== '') ? view($format->HTML($items->dates)) : " <p>&nbsp;</p>"; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="descr-well">
					<div class="row-fluid">
						<div class="control-group">
							<label for="other" class="control-label">Other</label>
							<div class="controls">
								<textarea name="other" id="other" class="span12 descr-other"><?php echo ($items->other !== '') ? view($items->other) : " <p>&nbsp;</p>"; ?></textarea>
								<?php helpBlock(L_PROPERTY_OTHER_HELP); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="control-group">
					<label id="optionsCheckboxes" class="control-label">Facilities</label>
					<div class="controls">
						<label for="shop" class="checkbox">
							<input type="hidden" name="shop" value="0" id="" />
							<input name="shop" type="checkbox" id="shop" <?php echo ($items->shop === '1') ? 'checked="checked"' : '';?>  value="1" />
							<span>Shop</span>
						</label>
						<label for="plantsales" class="checkbox">
							<input type="hidden" name="plantsales" value="0" id="" />
							<input name="plantsales" type="checkbox" id="plantsales" value="1" <?php echo ($items->plantsales === '1') ? 'checked="checked"' : '';?> />
							<span>Plantsales</span>
						</label>
						<label for="cafe" class="checkbox">
							<input type="hidden" name="cafe" value="0" id="" />
							<input name="cafe" type="checkbox" id="cafe" value="1" <?php echo ($items->cafe === '1') ? 'checked="checked"' : '';?> />
							<span>Cafe</span>
						</label>
						<label for="restaurant" class="checkbox">
							<input type="hidden" name="restaurant" value="0" id="" />
							<input name="restaurant" type="checkbox" id="restaurant" value="1" <?php echo ($items->restaurant === '1') ? 'checked="checked"' : '';?> />
							<span>Restaurant</span>
						</label>
						<label for="civilweddinglicense" class="checkbox">
							<input type="hidden" name="civilweddinglicense" value="0" id="" />
							<input name="civilweddinglicense" type="checkbox" id="civilweddinglicense" value="1" <?php echo ($items->civilweddinglicense === '1') ? 'checked="checked"' : '';?> />
							<span>Civil Wedding License</span>
						</label>
						<label for="audiotours" class="checkbox">
							<input type="hidden" name="audiotours" value="0" id="" />
							<input name="audiotours" type="checkbox" id="audiotours" value="1" <?php echo ($items->audiotours === '1') ? 'checked="checked"' : '';?> />
							<span>Audio Tours</span>
						</label>
						<label for="nodogs" class="checkbox">
							<input type="hidden" name="nodogs" value="0" id="" />
							<input name="nodogs" type="checkbox" id="nodogs" value="1" <?php echo ($items->nodogs === '1') ? 'checked="checked"' : '';?> />
							<span>Dogs Disallowed</span>
						</label>
						<label for="openallyear" class="checkbox">
							<input type="hidden" name="openallyear" value="0" id="" />
							<input name="openallyear" type="checkbox" id="openallyear" value="1" <?php echo ($items->openallyear === '1') ? 'checked="checked"' : '';?> />
							<span>Open All Year</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="accordion" id="stretchContainer">
			<?php
			$the_module->more_options(L_SHARE_ITEM, 'doSharing', 'sharing');
			$the_module->more_options(
				array(
				L_CATEGORY_OPTIONS => 'doCategories',
				L_CLEAN_URL_TITLES => 'doCleanURLTitles'
				)
			);
			$the_module->more_options(L_GALLERY_THUMBNAIL_SETTINGS,'doThumbnails','thumbInfoContents');
			$the_module->more_options(L_POST_TIME_EDIT, 'doDateTimeForms', 'editDateTimeContainer');
			?>
		</div>

		<div class="form-actions">
			<input name="submit" type="submit" class="btn btn-primary" id="submit" value="<?php echo L_BUTTON_EDIT ?>" />
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
						<img src="funcs/tn.lib.php?dim=70&thumb=1&id=<?php echo $item->id ?>" alt="<?php echo $item->thumbnail ?>" />
						<h1><?php echo $item->title ?></h1>
						<p><?php printf(L_POSTED_BY, $the_displayname, $the_username) ?></p>
						<?php if($has_subcat){ ?>
							<h3><?php echo L_SUB_CATEGORY ?>: <?php echo $category; ?></h3>
						<?php } ?>
						<a href="<?php echo edit_link($item->id); ?>" title="<?php echo L_EDIT_ITEM ?>" class="btn btn-success"><?php echo L_EDIT_ITEM ?></a>
						<a href="<?php echo edit_link($item->id); ?>#sharing" title="<?php echo L_SHARE_ITEM ?>" class="btn shareLink"><?php echo L_SHARE_ITEM ?></a>
						<a href="http://bufferapp.com/add" class="btn btn-info" data-text="Find <?php echo $item->title ?> on Heritage #GoHeritage" data-url="<?php echo $item->hrtgs ?>" data-count="vertical">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
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
						<input name="submit" type="submit" class="btn btn-danger pull-right" id="submit" value="<?php echo L_BUTTON_DELETE ?>" />
					</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>
