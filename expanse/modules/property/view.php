<?php
/**************************************************
HTML below. No form tag 
(though if you'd like to include another form, 
just close one, and open the new one right after,
like so: </form><form method="post" action="">)
***************************************************/

//Must be included at the top of all mod files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}

$size_note = sprintf('<div class="alert-message info" data-alert="alert"><a class="close" href="#">×</a><p>%s</p></div>', sprintf(L_SIZE_NOTE,MAX_UPLOAD));
//If you're adding content, use this block
if(ADDING):  ?>	
	<input type="hidden" name="aid" value="<?php echo $_SESSION['id']; ?>" />
	<input type="hidden" name="pid" value="<?php echo $catid; ?>" />
	
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="title"><?php echo L_PROPERTY_TITLE ?></label>
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
									<input name="comments" class="cBox" type="checkbox" id="comments" value="1" checked="checked" />
									<span><?php echo L_ALLOW_PROPERTY_REVIEWS ?></span>
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
			<div class="span8">
				<div class="clearfix">
					<label for="address">Address Line 1</label>
					<div class="input">
						<input name="address" type="text" class="span8 formfields" id="address" />	
					</div>
				</div>
				<div class="clearfix">
					<label for="county">County</label>
					<div class="input">
						<input name="county" type="text" class="span8 formfields" id="county" />
					</div>
				</div>
				<div class="clearfix">
					<label for="postcode">Postcode</label>
					<div class="clearfix">
						<input name="postcode" type="text" class="formfields" id="postcode" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="clearfix">
					<label for="latitude">Latitude (57.0000)</label>
					<div class="input">
						<input name="latitude" type="text" class="formfields" id="latitude" />
					</div>
				</div>
				<div class="clearfix">
					<label for="longitude">Longitude (1.0000)</label>
					<div class="input">
						<input name="longitude" type="text" class="formfields" id="longitude" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="well">
					<p>We will try and calculate these for you, based upon the entry you enter in the Postcode field.</p>
					<p>If we fail to geo-locate your address please click <a href="javascript: void(0)" onclick="javascript:
usePointFromPostcode(document.getElementById('postcode').value)">here</a>.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="email">Email</label>
					<div class="input">
						<input name="email" type="text" class="span8 formfields" id="email" />
					</div>
				</div>
				<div class="clearfix">
					<label for="telephone">Telephone</label>
					<div class="input">
						<input name="telephone" type="text" class="span8 formfields" id="telephone" />
					</div>
				</div>
				<div class="clearfix">
					
				</div>
			</div>
			<div class="span8">
				<div class="clearfix">
					<label for="url">Website</label>
					<div class="input">
						<input name="url" type="text" class="span8 formfields" id="url"  />
					</div>
				</div>
				<div class="row">
					<div class="span4">
						<div class="clearfix">
							<label for="twitter">Twitter</label>
							<div class="input">
								<div class="input-prepend">
									<span class="add-on">@</span>
									<input name="twitter" type="text" class="medium formfields" id="twitter" />
								</div>
								<span class="help-block">Enter only your <span class="label notice">username</span></span>
							</div>
						</div>
					</div>
					<div class="span4">
						<div class="clearfix">
							<label for="facebook">Facebook</label>
							<div class="input">
								<input name="facebook" type="text" class="formfields" id="facebook" />
								<span class="help-block">Enter the full <span class="label notice">URL</span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php echo $size_note; ?>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="img_main"><?php echo L_IMAGE ?></label>
					<div class="input">
						<input name="img_main" type="file" class="span8 formfields" id="img_main" />
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="autothumb"><?php echo L_GALLERY_AUTO_THUMB ?></label>
					<div class="input">
						<input type="checkbox" name="autothumb" class="cBox" id="autothumb" value="1" checked="checked" />
					</div>
				</div>
			</div>
			<div class="span8">
				<div id="thumbField">
					<div class="clearfix">
						<label for="img_thumb" id="iTLabel"><?php echo L_THUMBNAIL ?></label>
						<div class="input">
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
						<div class="clearfix" id="second">
							<label for="additional_images1"><?php echo L_IMAGE ?> 1</label>
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
			<div class="span16">
				<div class="clearfix">
					<label for="descr"><?php echo L_GALLERY_BODY ?></label>
					<div class="input border-descr">
						<textarea name="descr" cols="30" rows="5" id="descr" class="span16 descr formfields"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="admission">Admission</label>
					<div class="input border-descr">
						<textarea name="admission" cols="30" rows="5" id="admission" class="span16 descr formfields"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="events">Events</label>
					<div class="input border-descr">
						<textarea name="events" cols="30" rows="5" id="events" class="span16 descr formfields"></textarea>
						<?php helpBlock(L_PROPERTY_EVENTS_HELP); // 'If you have planned events throughout the year you can list them here. You can edit this section at anytime, but changes will only be applied to the database at our periodical updates throughout the year.' ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="dates">Dates</label>
					<div class="input border-descr">
						<textarea name="dates" cols="30" rows="5" id="dates" class="span16 descr formfields"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span10">
				<div class="clearfix">
					<label for="other">Other</label>
					<div class="input border-descr">
						<textarea name="other" cols="30" rows="5" id="other" class="span16 descr formfields"></textarea>
						<?php helpBlock(L_PROPERTY_OTHER_HELP); ?>
					</div>
				</div>
			</div>

			<div class="span6">
				<div class="clearfix">
					<label id="optionsCheckboxes">Facilities</label>
					<div class="input">
						<ul class="inputs-list">
							<li>
								<label for="shop">
									<input name="shop" class="cBox" type="checkbox" id="shop" value="0" />
									<span>Shop</span>
								</label>
							</li>
							<li>
								<label for="plantsales">
									<input name="plantsales" class="cBox" type="checkbox" id="plantsales" value="0" />
									<span>Plantsales</span>
								</label>
							</li>
							<li>
								<label for="cafe">
									<input type="checkbox" name="cafe" id="cafe" value="0" class="cBox" />
									<span>Cafe</span>
								</label>
							</li>
							<li>
								<label for="restaurant">
									<input type="checkbox" name="restaurant" id="restaurant" value="0" class="cBox" />
									<span>Restaurant</span>
								</label>
							</li>
							<li>
								<label for="civilweddinglicense">
									<input type="checkbox" name="civilweddinglicense" id="civilweddinglicense" value="0" class="cBox" />
									<span>Civil Wedding License</span>
								</label>
							</li>
							<li>
								<label for="audiotours">
									<input type="checkbox" name="audiotours" id="audiotours" value="0" class="cBox" />
									<span>Audio Tours</span>
								</label>
							</li>
							<li>
								<label for="nodogs">
									<input type="checkbox" name="nodogs" id="nodogs" value="0" class="cBox" />
									<span>Dogs Disallowed</span>
								</label>
							</li>
							<li>
								<label for="openallyear">
									<input type="checkbox" name="openallyear" id="openallyear" value="0" class="cBox" />
									<span>Open All Year</span>
								</label>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>			
	
		<div class="stretchContainer">
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
		<div class="actions">
			<input name="submit" type="submit" class="btn primary" id="submit" value="<?php echo L_BUTTON_ADD ?>" />
		</div>
<?php endif; ?>

<?php 
//If you're editing content, use this block
if(EDITING): 
	//If you're editing one specific item, use this block
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
					<label for="title"><?php echo L_PROPERTY_TITLE ?></label>
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
									<input type="hidden" name="comments" value="0" id="" />
									<input type="checkbox" name="comments" id="comments" <?php echo ($items->comments == 1) ? 'checked="checked"' : ''; ?> value="1" class="cBox" />
									<span><?php echo L_ALLOW_PROPERTY_REVIEWS ?></span>
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
			<div class="span8">
				<div class="clearfix">
					<label for="address">Address Line 1</label>
					<div class="input">
						<input name="address" type="text" class="span8 formfields" id="address" value="<?php echo view($items->address);?>" />	
					</div>
				</div>
				<div class="clearfix">
					<label for="county">County</label>
					<div class="input">
						<input name="county" type="text" class="span8 formfields" id="county" value="<?php echo view($items->county);?>" />
					</div>
				</div>
				<div class="clearfix">
					<label for="postcode">Postcode</label>
					<div class="clearfix">
						<input name="postcode" type="text" class="formfields" id="postcode" value="<?php echo view($items->postcode);?>" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="clearfix">
					<label for="latitude">Latitude (57.0000)</label>
					<div class="input">
						<input name="latitude" type="text" class="formfields" id="latitude" value="<?php echo view($items->latitude);?>" />
					</div>
				</div>
				<div class="clearfix">
					<label for="longitude">Longitude (1.0000)</label>
					<div class="input">
						<input name="longitude" type="text" class="formfields" id="longitude" value="<?php echo view($items->longitude);?>" />
					</div>
				</div>
			</div>
			<div class="span4">
				<div class="well">
					<p>We will try and calculate these for you, based upon the entry you enter in the Postcode field.</p>
					<p>If we fail to geo-locate your address please click <a href="javascript: void(0)" onclick="javascript:
usePointFromPostcode(document.getElementById('postcode').value)">here</a>.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="email">Email</label>
					<div class="input">
						<input name="email" type="text" class="span8 formfields" id="email" value="<?php echo view($items->email);?>" />
					</div>
				</div>
				<div class="clearfix">
					<label for="telephone">Telephone</label>
					<div class="input">
						<input name="telephone" type="text" class="span8 formfields" id="telephone" value="<?php echo view($items->telephone);?>" />
					</div>
				</div>
			</div>
			<div class="span8">
				<div class="clearfix">
					<label for="url">Website</label>
					<div class="input">
						<input name="url" type="text" class="span8 formfields" id="url" value="<?php echo view($items->url);?>" />
					</div>
				</div>
				<div class="row">
					<div class="span4">
						<div class="clearfix">
							<label for="twitter">Twitter</label>
							<div class="input">
								<div class="input-prepend">
									<span class="add-on">@</span>
									<input name="twitter" type="text" class="medium formfields" id="twitter" value="<?php echo view($items->twitter);?>" />
								</div>
								<span class="help-block">Enter only your <span class="label notice">username</span></span>
							</div>
						</div>
					</div>
					<div class="span4">
						<div class="clearfix">
							<label for="facebook">Facebook</label>
							<div class="input">
								<input name="facebook" type="text" class="formfields" id="facebook" value="<?php echo view($items->facebook);?>" />
								<span class="help-block">Enter the full <span class="label notice">URL</span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php echo $size_note; ?>
		<div class="row">
			<div class="span8">
				<div class="clearfix">
					<label for="img_main"><?php echo L_IMAGE ?></label>
					<div class="input">
						<input name="img_main" type="file" class="formfields" id="img_main" size="50" />
					</div>
				</div>
				<img id="canvasImg" style="display:none;" width="<?php echo $items->width; ?>" height="<?php echo $items->height; ?>" src="funcs/tn.lib.php?id=<?php echo $item_id ?>&amp;dim=11024&amp;<?php echo time(); ?>" />
				<div class="clearfix">
					<label for="autothumb"><?php echo L_GALLERY_AUTO_THUMB ?></label>
					<input type="hidden" name="autothumb" value="0" />
					<div class="input">
						<input class="cBox" type="checkbox" id="autothumb" value="1" name="autothumb"  <?php echo ($items->autothumb == 1) ? 'checked="checked"' : '';?> />
					</div>
				</div>
				<img style="display:none;" src="funcs/tn.lib.php?thumb=1&dim=<?php echo $items->width ?>&id=<?php echo $item_id ?>&amp;<?php echo time(); ?>&amp;ignore_scale=1" alt="<?php echo $items->thumbnail ?>" id="scaleImg" name="scaleImg" />
			</div>
			<div class="span8">
				<p id="thumbNails">
					<img src="funcs/tn.lib.php?dim=200&amp;id=<?php echo $items->id ?>&amp;<?php echo time(); ?>" alt="<?php echo $items->image ?>" id="thumbNailMain" class="thumbnails" />
				 	<img class="thumbnails" src="funcs/tn.lib.php?dim=75<?php echo (!$items->autothumb) ? '&manual=1' : '' ?>&thumb=1&id=<?php echo $item_id ?>&amp;<?php echo time(); ?>" alt="<?php echo $items->thumbnail ?>" id="thumbNailThumb" />
				</p>
			</div>
		</div>
		<div class="row">
			<div class="span8">

			</div>
			<div class="span8">
				<div id="thumbField">
					<div class="clearfix">
						<label for="img_thumb" id="iTLabel"><?php echo L_THUMBNAIL ?></label>
						<div class="input">
							<input name="img_thumb" type="file" class="formfields" id="img_thumb" size="50" />
						</div>
					</div>
				</div>
			</div>
		</div>
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
							<div class="clearfix">
								<label for="delete_additional<?php echo $ximg->id ?>"><?php echo L_GALLERY_ADDITIONAL_IMAGES_DELETE ?></label>
								<div class="input">
									<input type="checkbox" id="delete_additional<?php echo $ximg->id ?>" name="delete_additional[]" value="<?php echo $ximg->id ?>" class="xtraImgDelete" />
								</div>
							</div>
							<img src="funcs/tn.lib.php?image_id=<?php echo $ximg->id ?>&amp;dim=50" title="<?php echo view($ximg->caption); ?>" alt="<?php echo view($ximg->caption); ?>" id="addImg<?php echo $ximg->id ?>" class="thumbnails hasHelp" />
							<blockquote class="helpContents" id="addImg<?php echo $ximg->id ?>Help"><h5>Image caption</h5><?php echo view($ximg->caption); ?></blockquote>
						</div>
						<?php
					}
					?>
				</div>
				<div class="row" id="additional_images1Group">
					<div class="span8" id="third">
						<div class="clearfix" id="second">
							<label for="additional_images1"><?php echo L_IMAGE ?> 1</label>
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
			<div class="span16">
				<div class="clearfix">
					<label for="descr"><?php echo L_GALLERY_BODY ?></label>
					<div class="input border-descr">
						<textarea name="descr" cols="30" rows="5" id="descr" class="span16 descr formfields"><?php echo view($items->descr); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="admission">Admission</label>
					<div class="input border-descr">
						<textarea name="admission" cols="30" rows="5" id="admission" class="span16 descr formfields"><?php echo view($items->admission); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="events">Events</label>
					<div class="input border-descr">
						<textarea name="events" cols="30" rows="5" id="events" class="span16 descr formfields"><?php echo view($items->events); ?></textarea>
						<?php helpBlock(L_PROPERTY_EVENTS_HELP); // 'If you have planned events throughout the year you can list them here. You can edit this section at anytime, but changes will only be applied to the database at our periodical updates throughout the year.' ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span16">
				<div class="clearfix">
					<label for="dates">Dates</label>
					<div class="input border-descr">
						<textarea name="dates" cols="30" rows="5" id="dates" class="span16 descr formfields"><?php echo view($items->dates); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span10">
				<div class="clearfix">
					<label for="other">Other</label>
					<div class="input border-descr">
						<textarea name="other" cols="30" rows="5" id="other" class="span16 descr formfields"><?php echo view($items->other); ?></textarea>
						<?php helpBlock(L_PROPERTY_OTHER_HELP); ?>
					</div>
				</div>
			</div>

			<div class="span6">
				<div class="clearfix">
					<label id="optionsCheckboxes">Facilities</label>
					<div class="input">
						<ul class="inputs-list">
							<li>
								<label for="shop">
									<input type="hidden" name="shop" value="0" id="" />
									<input name="shop" class="cBox" type="checkbox" id="shop" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Shop</span>
								</label>
							</li>
							<li>
								<label for="plantsales">
									<input type="hidden" name="plantsales" value="0" id="" />
									<input name="plantsales" class="cBox" type="checkbox" id="plantsales" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Plantsales</span>
								</label>
							</li>
							<li>
								<label for="cafe">
									<input type="hidden" name="cafe" value="0" id="" />
									<input name="cafe" class="cBox" type="checkbox" id="cafe" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Cafe</span>
								</label>
							</li>
							<li>
								<label for="restaurant">
									<input type="hidden" name="restaurant" value="0" id="" />
									<input name="restaurant" class="cBox" type="checkbox" id="restaurant" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Restaurant</span>
								</label>
							</li>
							<li>
								<label for="civilweddinglicense">
									<input type="hidden" name="civilweddinglicense" value="0" id="" />
									<input name="civilweddinglicense" class="cBox" type="checkbox" id="civilweddinglicense" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Civil Wedding License</span>
								</label>
							</li>
							<li>
								<label for="audiotours">
									<input type="hidden" name="audiotours" value="0" id="" />
									<input name="audiotours" class="cBox" type="checkbox" id="audiotours" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Audio Tours</span>
								</label>
							</li>
							<li>
								<label for="nodogs">
									<input type="hidden" name="nodogs" value="0" id="" />
									<input name="nodogs" class="cBox" type="checkbox" id="nodogs" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Dogs Disallowed</span>
								</label>
							</li>
							<li>
								<label for="openallyear">
									<input type="hidden" name="openallyear" value="0" id="" />
									<input name="openallyear" class="cBox" type="checkbox" id="openallyear" value="1" <?php echo ($items->shop == 1) ? 'checked="checked"' : '';?> />
									<span>Open All Year</span>
								</label>
							</li>
						</ul>
					</div>
				</div>
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
			$the_module->more_options(L_GALLERY_THUMBNAIL_SETTINGS,'doThumbnails','thumbInfoContents');
			$the_module->more_options(L_SHARE_ITEM, 'doSharing', 'sharing');
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
					if(empty($category)){
						$has_subcat = false;
					}
				}

				?>
				<div id="item_<?php echo $item->id ?>" title="<?php echo strip_tags($item->descr) ?>">
					<img class="thumbnails" src="funcs/tn.lib.php?dim=70&thumb=1&id=<?php echo $item->id ?>" alt="<?php echo $item->thumbnail ?>" />
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
