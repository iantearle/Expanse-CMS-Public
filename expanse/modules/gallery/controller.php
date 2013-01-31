<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all module files.
if(!defined('EXPANSE')) {
	die('Sorry, but this file cannot be directly viewed.');
}

/**
 * Gallery class.
 *
 * @extends Module
 */
class Gallery extends Module {
	// This is the meta data for the category.
	var $name = L_GALLERY_NAME;
	var $description = L_GALLERY_DESCRIPTION;

	// Inherit the rest of the category meta-data
	/**/

	/**
	 * add function.
	 *
	 * @access public
	 * @return void
	 */
	function add() {
		// Grab the global message object, and the global category id
		// global $outmess, $catid;
		$catid = $this->cat_id;
		$outmess = $this->output;

		//Process the files
		$uploaddir = UPLOADS;
		$uploads = checkFiles($_FILES, $uploaddir, true, '/^additional_images\d/i');
		$xtra_img_uploads = checkFiles($_FILES, $uploaddir, true, array('img_main', 'img_thumb'));
		// Check for errors
		if(!empty($uploads['errors']) || !empty($xtra_img_uploads['errors'])) {
			foreach($uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}
			foreach($xtra_img_uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}

			//Send a message if there are errors
			$messages = "<ul>".implode('', $messages)."</ul>";
			printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
		} else {
			//Everything looks good
			//Grab module items object
			$items =& $this->items;
			$object_vars = get_object_vars($items);

			//Loop through POST
			foreach($_POST as $ind=>$val) {
				if(isset($object_vars[$ind])) {
					if(is_array($val)) {
						foreach($val as $k => $v) {
							$val[$k] = trim($v);
							if(empty($v)) {
								unset($val[$k]);
							}
						}
						$items->{$ind} = !empty($val) ? serialize($val) : '';
					} else {
						$items->{$ind} = trim($val);
					}
				}
			}

			//Set individual fields
			if(!empty($uploads['files'])) {
				$items->image = $uploads['files']['img_main']['name'];
				$items->thumbnail = !isset($_POST['autothumb']) && isset($uploads['files']['img_thumb']['name']) ? $uploads['files']['img_thumb']['name'] : '';
				$items->width = $uploads['files']['img_main']['width'];
				$items->height = $uploads['files']['img_main']['height'];
			}
			$items->use_default_thumbsize = 1;
			$items->created = dateTimeProcess();
			$items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
			$items->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
			$items->paypal_amount = (isset($_POST['paypal_amount'])) ? (float) $_POST['paypal_amount'] : 0;
			$items->paypal_handling = (isset($_POST['paypal_handling'])) ? (float) $_POST['paypal_handling'] : 0;
			$items->descr = str_replace(array('&nbsp;','<p></p>'), ' ', $items->descr);

			//Add a subcat
			$this->addSubcat();

			//Save the info
			if($items->SaveNew()) {
				$items = applyOzoneAction('item_add', $items);
				$this->manage_custom_fields($items);
				if(!empty($xtra_img_uploads['files'])) {
					$images = new Expanse('images');
					$caption = isset($_POST['caption']) ? $_POST['caption'] : array();
					foreach($xtra_img_uploads['files'] as $xi => $xv){
						$images->image = $xv['name'];
						$images->width = $xv['width'];
						$images->height = $xv['height'];
						$images->caption = isset($caption[$xi]) ? trim(strip_tags($caption[$xi])) : '';
						$images->itemid = $items->id;
						$images->SaveNew();
					}
				}

				//Move or copy
				$new_item =& $this->new_item;
				$new_home =& $this->new_home;
				$this->moveOrCopy($items);
				printOut(SUCCESS,vsprintf(L_ADD_SUCCESS, array($items->title, $catid, $items->id)));

				//Reset POST
				$_POST = array();
			} else {
				printOut(FAILURE,vsprintf(L_ADD_FAILURE, array($items->title, mysql_error())));
			}
		}
	}

	/**
	 * edit function.
	 *
	 * @access public
	 * @return void
	 */
	function edit() {
		$outmess = $this->output;
		$catid = $this->cat_id;
		$item_id = $this->item_id;

		//Process the files
		$uploaddir = UPLOADS;
		$uploads = checkFiles($_FILES, $uploaddir, true, '/^additional_images\d/i');
		$xtra_img_uploads = checkFiles($_FILES, $uploaddir, true, array('img_main', 'img_thumb'));

		//Check for errors
		if(!empty($uploads['errors']) || !empty($xtra_img_uploads['errors'])) {
			foreach($uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}
			foreach($xtra_img_uploads['errors'] as $val) {
				foreach($val as $v) {
					$messages[] = "<li>$v</li>";
				}
			}

			// Send a message if there are errors
			$messages = "<ul>".implode('', $messages)."</ul>";
			printOut(FAILURE,sprintf(L_UPLOAD_FAILURE, $messages));
		} else {
			// Everything looks good
			// Instantiate new object
			// global $items;
			$items =& $this->items;
			$images = new Expanse('images');
			$items->Get($item_id);
			$object_vars = get_object_vars($items);
			//Loop through POST
			foreach($_POST as $ind=>$val) {
				if(isset($object_vars[$ind])) {
					if(is_array($val)){
					foreach($val as $k => $v) {
						$val[$k] = trim($v);
						if(empty($v)) {
							unset($val[$k]);
						}
					}
						$items->{$ind} = !empty($val) ? serialize($val) : '';
					} else {
						$items->{$ind} = trim($val);
					}
				}
			}
			//Set individual fields
			if(!empty($uploads['files'])) {
				$mainimage = isset($uploads['files']['img_main']['name']) ? $uploads['files']['img_main']['name'] : NULL;
				$thumbimage = isset($uploads['files']['img_thumb']['name']) ? $uploads['files']['img_thumb']['name'] : NULL;

				if(isset($mainimage) && !empty($items->image)) {
					if($items->image != $mainimage && file_exists($uploaddir.'/'.$mainimage)) {
						if(!empty($items->image) && file_exists($uploaddir.'/'.$items->image)) {
							unlink($uploaddir.'/'.$items->image);
						}
					}
				}

				if(isset($thumbimage) && !empty($items->thumbnail)) {
					if($items->thumbnail != $thumbimage && file_exists($uploaddir.'/'.$thumbimage)) {
						if(!empty($items->thumbnail) && file_exists($uploaddir.'/'.$items->thumbnail)) {
							unlink($uploaddir.'/'.$items->thumbnail);
						}
					}
				}
				$items->image = !is_null($mainimage) ? $mainimage : $items->image;
				$items->thumbnail = (isset($_POST['autothumb']) && $_POST['autothumb'] == 1) ? '' : (!is_null($thumbimage)) ? $thumbimage : $items->thumbnail;
				$items->width = isset($uploads['files']['img_main']['width']) ? $uploads['files']['img_main']['width'] : $items->width;
				$items->height = isset($uploads['files']['img_main']['height']) ? $uploads['files']['img_main']['height'] : $items->height;
			}
			$caption = isset($_POST['caption']) ? $_POST['caption'] : array();
			foreach($_POST['existing_image'] as $xi) {
				$images->Get($xi);
				if(isset($caption[$xi])) {
					$images->caption = isset($caption[$xi]) ? trim($caption[$xi]) : '';
					$images->itemid = $item_id;
					$images->Save();
				}
			}
			if(!empty($xtra_img_uploads['files'])) {
				foreach($xtra_img_uploads['files'] as $xi => $xv) {
					$images->image = $xv['name'];
					$images->width = $xv['width'];
					$images->height = $xv['height'];
					$images->caption = isset($caption[$xi]) ? trim($caption[$xi]) : '';
					$images->itemid = $item_id;
					$images->SaveNew();
				}
			}
			$delete_images = isset($_POST['delete_additional']) ? $_POST['delete_additional'] : array();
			foreach($delete_images as $di) {
				$images->Get($di);
				if(!empty($images->image) && file_exists($uploaddir.'/'.$items->image)) {
					unlink($uploaddir.'/'.$images->image);
				}
				$images->Delete();
			}
			$items->created = dateTimeProcess($items->created);
			$items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $catid;
			$items->dirtitle = set_dirtitle($items);
			$items->paypal_amount = (isset($_POST['paypal_amount'])) ? (float) $_POST['paypal_amount'] : 0;
			$items->paypal_handling = (isset($_POST['paypal_handling'])) ? (float) $_POST['paypal_handling'] : 0;
			$items->descr = str_replace(array('&nbsp;','<p></p>'), ' ', $items->descr);

			//Clean extroptions of empty values
			foreach($_POST['extraoptions'] as $ek => $ev) {
				if(empty($ev)) {
					unset($_POST['extraoptions'][$ek]);
				}
			}
			$items->extraoptions = (!empty($_POST['extraoptions'])) ? serialize($_POST['extraoptions']) : '';
			$items->use_default_thumbsize = (!empty($_POST['use_default_thumbsize'])) ? 1 : 0;
			$title = empty($items->title) ? L_NO_TEXT_IN_TITLE : $items->title;

			//Add a subcat
			$items->cid = $this->addSubcat();

			//Save the info
			if($items->Save()) {
				$items = applyOzoneAction('item_edit', $items);
				$this->manage_custom_fields($items);

				//Move or copy
				$new_item =& $this->new_item;
				$new_home =& $this->new_home;
				if(!$this->moveOrCopy($items)) {
					printOut(SUCCESS,vsprintf(L_EDIT_SUCCESS, array($title, $catid, $items->id)));
				} else {
					printOut(SUCCESS,vsprintf(L_EDIT_MOVE_SUCCESS, array($title, $new_home, $new_item->id)));
				}

				//Reset POST
				$_POST = array();
			} else {
				printOut(FAILURE,vsprintf(L_EDIT_FAILURE, array($title, mysql_error())));
			}
		}
	}

	/**
	 * more function.
	 *
	 * @access public
	 * @return void
	 */
	function more() {
		if(is_posting(L_BUTTON_MASS_UPLOAD)) {
			$this->galleryUpload();
		}
		if(is_posting('Post this')) {
			echo 'here';
		}
	}

	/**
	 * galleryUpload function.
	 * You can define custom functions in this file as well, however, it may be better to use a class so that there is less chance of redefining an existing function.
	 *
	 * @access public
	 * @return void
	 */
	function galleryUpload() {
		$path = UPLOADS;
		$items = &$this->items;
		$uploaded = checkFiles($_FILES, $path);
		$filename = isset($uploaded['files']['massupload']['name']) ? $uploaded['files']['massupload']['name'] : '';
		$pathfile = $path.'/'.$filename;
		$ftp_path = isset($_POST['mass_upload_ftp']) ? trim($_POST['mass_upload_ftp']) : '';
		if(empty($filename) && empty($ftp_path)) {
			printOut(FAILURE, L_GALLERY_INVALID_MASS_UPLOAD);
			return false;
		}
		if(!empty($ftp_path)) {
			$ftp_path = str_replace(array('..'), '', $ftp_path);
			$ftp_path = $base_folder = trim($ftp_path, '/');
			$ftp_path = UPLOADS.'/'.$ftp_path;
			$errors = array();
			if(!empty($ftp_path) && file_exists($ftp_path) && is_dir($ftp_path)) {
				$resource_files = getFiles($ftp_path);
				$object_vars = get_object_vars($items);
				foreach($_POST as $ind=>$val) {
					//Loop through POST
					if(isset($object_vars[$ind])) {
						if(is_array($val)) {
							foreach($val as $k => $v) {
								$val[$k] = trim($v);
								if(empty($v)) {
									unset($val[$k]);
								}
							}
							$items->{$ind} = !empty($val) ? serialize($val) : '';
						} else {
							$items->{$ind} = trim($val);
						}
					}
				}
				foreach($resource_files['files'] as $i => $val) {
					$base_file = $base_folder.'/'.$val;
					$file = UPLOADS.'/'.$base_folder.'/'.$val;
					$file_dims = @getimagesize($file);
					if(!$file_dims) {
						$errors[] = $file; continue;
					}
					$items->width = $file_dims[0];
					$items->height = $file_dims[1];
					$items->image = $base_file;
					$items->pid = $this->cat_id;
					$items->title = remExtension($val);
					$items->online = isset($_POST['online']) ? 1 : 0;
					$items->aid = $this->auth->Id;
					$items->use_default_thumbsize = 1;
					$items->created = dateTimeProcess();
					$items->dirtitle = unique_dirtitle(dirify($i));
					$items->paypal_amount = (isset($_POST['paypal_amount'])) ? (float) $_POST['paypal_amount'] : 0;
					$items->paypal_handling = (isset($_POST['paypal_handling'])) ? (float) $_POST['paypal_handling'] : 0;
					if(!$items->SaveNew()) {
						$errors[] = $i;
					}
				}
				if(!empty($resource_files) && empty($errors)) {
					$_POST = array();
					printOut(SUCCESS, L_GALLERY_FTP_UPLOAD_SUCCESS);
					return;
				} else {
					printOut(FAILURE, L_GALLERY_FTP_UPLOAD_FAILURE);
					return;
				}
			} else {
				printOut(FAILURE, L_GALLERY_FTP_UPLOAD_MISSING);
			}
		} // uploading via FTP
		if(!empty($filename)) {
			$archive = new PclZip($pathfile);
			if(($exfiles = $archive->extract(PCLZIP_OPT_PATH, $path, PCLZIP_OPT_REMOVE_ALL_PATH)) == 0) {
				$ziperror = L_GALLERY_INVALID_ZIP.$archive->errorName();
				printOut(FAILURE, $ziperror);
				if(file_exists($pathfile) && is_file($pathfile)) {
					unlink($pathfile);
				}
			} else {
				$images = array();
				$nonimages = array();
				$results = array();
				foreach($exfiles as $ind => $val) {
					if(file_exists($val['filename'])) {
						$val['filename'] = renameExtracted($val['filename']);
						if(!getimagesize($val['filename'])) {
							$nonimages[] = $val['stored_filename'];
							unlink($val['filename']);
							unset($exfiles[$ind]);
						} else {
							$images[] = $val['filename'];
							list($width, $height, $type, $attr) = getimagesize($val['filename']);
							$exfiles[$ind]['file_title'] = remExtension($val['stored_filename']);
							$itemvars = get_object_vars($items);
							foreach($_POST as $index => $value) {
								if(isset($itemvars[$index])) {
									if(is_array($value)) {
										foreach($value as $k => $v) {
											$value[$k] = trim($v);
											if(empty($v)) {
												unset($value[$k]);
											}
										}
										$items->{$index} = !empty($value) ? serialize($value) : '';
									} else {
										$items->{$index} = trim($value);
									}
								}
							}
							$items->title = $exfiles[$ind]['file_title'];
							$items->dirtitle = unique_dirtitle(dirify($exfiles[$ind]['file_title']));
							$items->width =  $width;
							$items->height =  $height;
							$items->image =  basename($val['filename']);
							$items->use_default_thumbsize = 1;
							$items->created = dateTimeProcess();
							$items->paypal_amount = (isset($_POST['paypal_amount'])) ? (float) $_POST['paypal_amount'] : 0;
							$items->paypal_handling = (isset($_POST['paypal_handling'])) ? (float) $_POST['paypal_handling'] : 0;
							if($items->SaveNew()) {
								$this->manage_custom_fields($items);
								$results['success'][] = $items->title;
							} else {
								$results['error'][$items->title] = mysql_error();
							}
						}
					} else {
						unset($exfiles[$ind]);
					}
				}
				if(file_exists($pathfile) && is_file($pathfile)) {
					unlink($pathfile);
				}
				if(isset($results['success']) && count($results['success']) > 0) {
					foreach($results['success'] as $v) {
						$messages[] = "<li><strong>$v</strong></li>";
					}
					$messages = "<ul>".implode('', $messages)."</ul>";
					printOut(SUCCESS, sprintf(L_GALLERY_ZIP_EXTRACTED,$messages));
				}
				if(isset($results['error']) && count($results['error']) > 0) {
					$messages = array();
					foreach($results['error'] as $k => $v) {
						$messages[] = "<li><strong>$k</strong>.<br /> Why? $v</li>";
					}
					$messages = "<ul>".implode('', $messages)."</ul>";
					printOut(FAILURE, sprintf(L_GALLERY_ZIP_PARTIALLY_EXTRACTED,$messages));
				}
				if(count($images) == 0) {
					printOut(FAILURE, L_GALLERY_ZIP_NO_IMAGES);
				}
				if(count($nonimages) > 0) {
					$nicount = count($nonimages);
					$errmess = $nicount !=1 ? L_GALLERY_ZIP_NOT_IMAGES_PLURAL : L_GALLERY_ZIP_NOT_IMAGES_SINGULAR;
					$nonimages = implode('</strong>, '.L_CONCAT_AND.' <strong>',$nonimages);
					printOut(FAILURE, '<strong>'.$nonimages.'</strong> '.$errmess.'.');
				}
			}
		}
	}

	/**
	 * doThumbnails function.
	 *
	 * @access public
	 * @return void
	 */
	function doThumbnails() {
		$items =& $this->items;
		?>
		<div class="row-fluid">
			<div class="span5">
				<div class="control-group">
					<label for="crop_x" class="control-label"><?php echo L_GALLERY_THUMB_X ?></label>
					<div class="controls">
						<input type="text" name="crop_x" value="<?php echo $items->crop_x;?>" class="infields" id="crop_x"  />
					</div>
				</div>
				<div class="control-group">
					<label for="crop_y" class="control-label"><?php echo L_GALLERY_THUMB_Y ?></label>
					<div class="controls">
						<input type="text" name="crop_y" value="<?php echo $items->crop_y;?>" class="infields" id="crop_y"  />
					</div>
				</div>
			</div>
			<div class="span5">
				<div class="control-group">
					<label for="thumb_w" class="control-label"><?php echo L_GALLERY_THUMB_W ?></label>
					<div class="controls">
						<input type="text" name="thumb_w" value="<?php echo $items->thumb_w;?>" class="infields" id="thumb_w"  />
					</div>
				</div>
				<div class="control-group">
					<label for="thumb_h" class="control-label"><?php echo L_GALLERY_THUMB_H ?></label>
					<div class="controls">
						<input type="text" name="thumb_h" value="<?php echo $items->thumb_h;?>" class="infields" id="thumb_h"  />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span5">
				<div class="control-group">
					<label for="thumb_max" class="control-label"><?php echo L_GALLERY_THUMB_MAX ?></label>
					<div class="controls">
						<input type="text" name="thumb_max" value="<?php echo $items->thumb_max;?>" class="infields" id="thumb_max"  />
					</div>
				</div>
			</div>
			<div class="span5">
				<label for="use_default_thumbsize" class="checkbox">
					<input type="checkbox" name="use_default_thumbsize" value="1" id="use_default_thumbsize"  <?php echo ($items->use_default_thumbsize == 1) ? 'checked="checked"' : '';?>  />
					<?php echo L_GALLERY_THUMB_KEEP_DEFAULT ?>
				</label>
			</div>
		</div>
		<?php
	}

	/**
	 * doPaypal function.
	 *
	 * @access public
	 * @return void
	 */
	function doPaypal() {
		$items =& $this->items;
		global $currencysymbols;
		?>
		<label for="for_sale" class="checkbox">
			<input type="hidden" name="for_sale" value="0" />
			<input type="checkbox" name="for_sale" value="1" id="for_sale"  <?php echo ($items->for_sale == 1) ? 'checked="checked"' : '';?>  />
			<?php echo L_GALLERY_PP_FOR_SALE ?>
		</label>
		<div class="control-group">
			<label for="paypal_amount"> <?php echo L_GALLERY_PP_PRICE; ?></label>
			<div class="controls">
				<div class="input-prepend">
					<span class="add-on"><?php $paypal_currency_code = getOption('paypal_currency_code'); echo (isset($paypal_currency_code)) ? $currencysymbols[$paypal_currency_code] : $currencysymbols['USD']; ?></span>
					<input type="text" name="paypal_amount" value="<?php echo $items->paypal_amount;?>" class="infields" id="paypal_amount"  />
				</div>
			</div>
		</div>
		<div class="control-group">
			<label for="paypal_handling"><?php echo L_GALLERY_PP_HANDLING_COST ?></label>
			<div class="controls">
				<div class="input-prepend">
					<span class="add-on"><?php echo (isset($paypal_currency_code)) ? $currencysymbols[$paypal_currency_code] : $currencysymbols['USD'];?></span>
					<input type="text" name="paypal_handling" value="<?php echo $items->paypal_handling;?>" class="infields" id="paypal_handling"  />
					<span class="note"><?php echo L_GALLERY_PP_HANDLING_NOTE ?></span>
				</div>
			</div>
		</div>


		<fieldset id="new_cat">
			<legend><?php echo L_GALLERY_PP_MORE_OPTIONS; tooltip(L_GALLERY_PP_MORE_OPTIONS, L_GALLERY_PP_MORE_OPTIONS_HELP); ?></legend>
			<?php
			if(!empty($items->extraoptions)) {
				$extraoptions = unserialize($items->extraoptions);
				foreach($extraoptions as $k => $val) {
					$ok = $k+1;
					if($ok === 1) {
						?>
						<div class="control-group">
							<label for="option<?php echo $ok ?>"><?php echo L_JS_OPTION_LABEL ?> <?php echo $ok ?></label>
							<div class="controls">
								<input type="text" name="extraoptions[]" id="option<?php echo $ok ?>" value="<?php echo view($val) ?>" class="formfields"  />
							</div>
						</div>
						<?php
					} else {
					?>
						<div id="option<?php echo $ok ?>Group">
							<div class="control-group">
								<label for="option<?php echo $ok ?>"><?php echo L_JS_OPTION_LABEL ?> <?php echo $ok ?></label>
								<div class="controls">
									<input type="text" name="extraoptions[]" id="option<?php echo $ok ?>" value="<?php echo view($val) ?>" class="formfields"  />
								</div>
							</div>
						</div>
					<?php
					}
				}
			} else {
			?>
				<div class="row" id="new_cat1Group">
					<div class="span6">
						<div class="control-group">
							<label for="option1" class="control-label"><?php echo L_JS_OPTION_LABEL ?> 1</label>
							<div class="controls">
								<input type="text" name="extraoptions[]" id="option1" value="" class="formfields" />
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</fieldset>
		<?php
	}
}
