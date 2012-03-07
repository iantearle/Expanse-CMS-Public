<?php 
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');} 
/*   Categories   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=categories&amp;action=edit">'.L_ADMIN_MANAGE_CATEGORIES.'</a>',array(),'categories');
//'<a href="?cat=admin&amp;sub=categories&amp;action=manage">'.L_CATEGORY_MANAGE_TYPES.'</a>'
if($admin_sub !== 'categories'){return;}
if(ACTION == 'add'){
	add_breadcrumb('<a href="?cat=admin&amp;sub=categories&amp;action=edit">'.L_CATEGORY_EDIT_TITLE.'</a>');
	add_breadcrumb(L_CATEGORY_ADD_TITLE);
	add_title(L_CATEGORY_ADD_TITLE);
} elseif(ACTION == 'edit'){
	if(!empty($item_id)){
		$sections->Get(ITEM_ID);
		$section = !empty($sections->sectionname) ? $sections->sectionname : L_NO_TEXT_IN_TITLE;
		add_breadcrumb('<a href="?cat=admin&sub=categories&action=edit">'.L_CATEGORY_EDIT_TITLE.'</a>');
		add_title(L_CATEGORY_EDIT_TITLE);
		add_breadcrumb(sprintf(L_CURRENTLY_EDITING, $section));
		add_title(sprintf(L_CURRENTLY_EDITING, $section));
	} else {
		add_breadcrumb(L_CATEGORY_EDIT_TITLE);
		add_title(L_CATEGORY_EDIT_TITLE);
	}
} elseif(ACTION == 'manage'){

}
ozone_action('admin_page', 'category_content');

function category_content(){
	global $output, $modules_dir, $auth, $Database;
	$item_id = ITEM_ID;
	?>
	<div class="row" id="categoryMod">
		<?php
		if(ACTION == 'add'){
			if(is_posting(L_BUTTON_ADD)){
				$sections = get_dao('sections');
				$sections->sectionname = isset($_POST['sectionname']) ?  trim(save(strip_tags($_POST['sectionname']))) : '';
				$sections->descr = isset($_POST['cat_descr']) ?  trim(save(strip_tags($_POST['cat_descr']))) : '';
				
				$sections->dirtitle =  unique_dirtitle(dirify($sections->sectionname), 'sections');
				$sections->cat_type = isset($_POST['cat_type']) ? trim($_POST['cat_type']) : '';
				$sections->public = 0;
				if(empty($sections->cat_type) || empty($sections->sectionname)){
					printOut(FAILURE, L_MISSING_CATEGORY_DETAIL);
				} else {
					if($sections->SaveNew()){
						$auth->updateAdmins();
						printOut(SUCCESS, sprintf(L_CATEGORY_ADDED, $sections->sectionname, $sections->id));
					} else {
						printOut(FAILURE, L_CATEGORY_NOT_ADDED);
					}					
				}
			}
			echo $output; ?>
			<div class="span8">
				<h3><?php echo L_CATEGORY_GIVE_NAME ?></h3>
				<div class="clearfix">
					<label for="sectionname"><?php echo L_CATEGORY_NAME ?></label>
					<div class="input">
						<input type="text" id="sectionname" name="sectionname" class="span8 formfields" />
					</div>			
				</div>
				<div class="clearfix">
					<label for="descr"><?php echo L_CATEGORY_DESCRIPTION ?></label>
					<div class="input">
						<textarea name="cat_descr" id="cat_descr" class="span8"></textarea>
					</div>
				</div>
			</div>
			<div class="span8">
				<div class="clearfix">
					<label id="optionsCheckboxes"><?php echo L_CATEGORY_GIVE_TYPE ?></label>
					<div class="input">
						<ul class="inputs-list">
						<?php 	
						$uninstalled_cats = array();
						$modfilesdir = EXPANSEPATH."/$modules_dir";
						$modfiles = getFiles($modfilesdir, 'all',1);
						foreach($modfiles['dirs'] as $k => $v) {
							if(!(in_array("view.php",$v['files']) && in_array("controller.php",$v['files']))){continue;}
							include("$modfilesdir/$k/controller.php");
							$info = (object) get_class_vars($k);
							if($info->Exclude == true){continue;}		
							if(is_callable(array($k,'install'))){
								if(getOption('category', $k)){
								?>
								<li>
									<label for="cat<?php echo $info->name; ?>">
										<input type="radio" id="cat<?php echo $info->name; ?>" name="cat_type" value="<?php echo $k; ?>" />
										<span><?php echo $info->name; ?></span>
										<span class="help-block">
											<strong>Note:</strong> <?php echo $info->description; ?>
										</span>
									</label>
								</li>
								<?php
								} else {
									$uninstalled_cats[] = $info->name;
								}
							} else { ?>
								<li>
									<label for="cat<?php echo $info->name; ?>">
										<input type="radio" id="cat<?php echo $info->name; ?>" name="cat_type" value="<?php echo $k; ?>" />
										<span><?php echo $info->name; ?></span>
										<span class="help-block">
											<strong>Note:</strong> <?php echo $info->description; ?>
										</span>
									</label>
								</li>
							<?php
							}
						}?>
						</ul>
					</div>
				</div><?php
				if(!empty($uninstalled_cats)){
					?>
					<dl>
						<dt><?php echo L_CATEGORY_PENDING_INSTALL_LIST ?></dt>
						<dd>
							<ul>
								<?php 
								foreach($uninstalled_cats as $ind => $uncat){
									?><li><a href="?cat=admin&amp;sub=categories&amp;action=manage#cat<?php echo $uncat; ?>"><?php echo $uncat; ?></a></li><?php
								}
								?>
							</ul>
						</dd>
					</dl>
					<?php 
				}
				?>						
			</div>			
			<input type="hidden" name="pid" value="0">
			</div>
			<div class="row">
			<div class="actions">
				<input type="submit" name="submit" value="<?php echo L_BUTTON_ADD ?>" class="btn primary" />
			</div>
	<?php 
		} elseif(ACTION == 'manage'){
			if(is_posting(L_BUTTON_INSTALL)){
				$install = $Database->Escape($_POST['category']);
				$install = str_replace('..', '', $install);
				if(file_exists("$modules_dir/{$install}/controller.php") && file_exists("$modules_dir/{$install}/view.php")){
					include_once("$modules_dir/{$install}/controller.php");
					$func_arr = array($install,'install');
					if(is_callable($func_arr)){
						if(!getOption('category', $install)){
							call_user_func($func_arr);
							setOption('category', $install);
							printOut( SUCCESS, L_CATEGORY_INSTALLED);
						} else {
							printOut( FAILURE, L_CATEGORY_ALREADY_INSTALLED);
						}
					}
				}
			}
			if(is_posting(L_BUTTON_UNINSTALL)){
				$uninstall = $Database->Escape($_POST['category']);
				$uninstall = str_replace('..', '', $uninstall);
				if(file_exists("$modules_dir/{$uninstall}/controller.php") && file_exists("$modules_dir/{$uninstall}/view.php")){
					include_once("$modules_dir/{$uninstall}/controller.php");
					$func_arr = array($uninstall,'uninstall');
					if(is_callable($func_arr)){
						if(getOption('category', $uninstall)){
							call_user_func($func_arr);
							deleteOption('category', $uninstall);
							printOut(SUCCESS, L_CATEGORY_UNINSTALLED);
						} else {
							printOut( FAILURE, L_CATEGORY_ALREADY_UNINSTALLED);
						}
					}
				}
			}
			echo $output; ?>
			</form>
			<p><?php echo L_CATEGORY_FINISHED_MANAGING ?></p>
			<dl id="addCats">
				<?php 
				$modfilesdir = EXPANSEPATH."/$modules_dir";
				$modfiles = getFiles($modfilesdir, 'all', 1);
				foreach($modfiles['dirs'] as $k => $v){		
					if(in_array("controller.php",$v['files']) && in_array("view.php",$v['files'])){			
						include_once("$modfilesdir/{$k}/controller.php");
						$info = (object) get_class_vars($k);
						if(is_array($info->author)){
							foreach($info->author as $name => $url){
								$authors[] = (!empty($url)) ? '<a href="'.$url.'" target="_blank">'.$name.'</a>' : "$ind";
							}
							$info->author = implode(', ', $authors);
						} else {
							$info->author = '<a href="'.$info->authorURL.'" target="_blank">'.$info->author.'</a>';
						}
						$authors = array();
						?>
						<dt class="collSwitsch" id="cat<?php echo $info->name; ?>">
							<?php echo $info->name; ?>
						</dt>
						<dd id="cat<?php echo $info->name; ?>Contents">
							by <?php echo $info->author;  ?><br />
							v.<?php echo $info->version ?><br />
							<p><?php echo $info->description; ?></p>
							<?php 
							if(is_callable(array($k,'install')) && !getOption('category', $k)){?>
								<p class="contentalert"><?php echo L_CATEGORY_PENDING_INSTALL ?><br />
									<form method="post" action="">
										<input name="category" type="hidden" value="<?php echo $k; ?>" />
										<input type="submit" name="submit" class="buttons" value="<?php echo L_BUTTON_INSTALL ?>" />
									</form>
								</p>
							<?php 
							}
							if(is_callable(array($k,'uninstall')) && getOption('category', $k)){ ?>
								<form method="post" action="">
									<h6><?php echo L_CATEGORY_PENDING_UNINSTALL ?></h6>
									<?php echo L_CATEGORY_PENDING_UNINSTALL_WARNING ?>
									<input name="category" type="hidden" value="<?php echo $k; ?>" />
									<input type="submit" name="submit" class="buttons" value="<?php echo L_BUTTON_UNINSTALL ?>" />
								</form>
								<?php 
							}
						?></dd>
						<?php
					}
				}
				?>
			</dl>
	<?php 
		}	
		if(ACTION == 'edit') {
			if(!empty($item_id)) {
				?><div class="span6"><?php
				$subs =& get_dao('sections');
				$errs = array();
				if(is_posting(L_BUTTON_DELETE)) { 
					if(isset($_POST['del'])) {
						foreach($_POST['del'] as $id){
							$st = get_dao('sections');
							$st->Get($id);
							$title = $st->sectionname;
							$reason = empty($title) ? L_CATEGORY_MISSING : $title;
							if(deleteItem($id, 'sections')){
								$result[] = '<li>'.sprintf(L_SUBCATEGORY_DELETED, $title).'</li>';
							} else {
								$result[] = '<li>'.sprintf(L_SUBCATEGORY_NOT_DELETED, $title, $reason).'</li>';
								$errs[] = 1;
							}
						}
						$result = '<ul>'.implode('', $result).'</ul>';					
						if(empty($errs)){
							printOut(SUCCESS, $result);
						} else {
							printOut(FAILURE, $result);
						}
					}
				}
				if(is_posting(L_BUTTON_EDIT)){
					$subs->Get($item_id);
					$section_name = trim(save(strip_tags($_POST['catTitle'])));
					$subs->descr = isset($_POST['cat_descr']) ? trim(save(strip_tags($_POST['cat_descr']))) : '';
					if($subs->sectionname != $section_name){
						$subs->sectionname = empty($section_name) ? $subs->sectionname : $section_name;
						$subs->dirtitle =  unique_dirtitle(dirify($subs->sectionname), 'sections');
					}
					$new_cat = isset($_POST['new_cat']) && is_array($_POST['new_cat']) ? $_POST['new_cat'] : array();
					$new_cat_descr = isset($_POST['new_cat_descr']) && is_array($_POST['new_cat_descr']) ? $_POST['new_cat_descr'] : array();
					$subadd = '';
					$subcats_list = isset($_POST['sectionname']) && is_array($_POST['sectionname']) ? $_POST['sectionname'] : array();
					$subcats_list_descr = isset($_POST['sectionname_descr']) && is_array($_POST['sectionname_descr']) ? $_POST['sectionname_descr'] : array();
					$subsection =& get_dao('sections');
					foreach($subcats_list as $i => $sl){
						$sl = trim($sl);
						if(empty($sl)){continue;}
						$subsection->Get($i);
						$subsection->sectionname = $sl;
						if(isset($subcats_list_descr[$i])){
							$subsection->descr = trim(save(strip_tags($subcats_list_descr[$i])));
						}
						$subsection->dirtitle = unique_dirtitle(dirify($subsection->sectionname), 'sections');
						$subsection->Save();
					}	
					$sub_result = array('good' => array(), 'bad' => array());
					$subadd = '';
					if(!empty($new_cat)){		
						$newsubs =& get_dao('sections');
						foreach($new_cat as $i => $v){
							$v = trim(save(strip_tags($v)));
							if(empty($v)){continue;}
							if(isset($new_cat_descr[$i]) && !empty($new_cat_descr[$i])){
								$newsubs->descr = trim(save(strip_tags($new_cat_descr[$i])));
							}
							$newsubs->sectionname = $v;
							$newsubs->dirtitle =  unique_dirtitle(dirify($newsubs->sectionname), 'sections');
							$newsubs->pid = $subs->id;
							$newsubs->public = 0;
							if($newsubs->SaveNew()){
								$sub_result['good'][] = $newsubs->sectionname;
							} else {
								$sub_result['bad'][] = $newsubs->sectionname;
							}
						}
					}
					if(!empty($sub_result['good'])){
						$subadd .= '<br />'.sprintf(L_NEW_SUBCATEGORY_ADDED,proper_list($sub_result['good']));
					}
					if(!empty($sub_result['bad'])){
						$subadd .='<br />'.sprintf(L_NEW_SUBCATEGORY_NOT_ADDED,proper_list($sub_result['bad']));
					}
					if($subs->Save()){
						$auth->updateAdmins();
						printOut(SUCCESS, vsprintf(L_EDIT_CATEGORY_SUCCESS,array($subs->sectionname, $subadd)));
					} else {
						printOut(FAILURE, vsprintf(L_EDIT_CATEGORY_FAILURE,array($subs->sectionname,$subadd, mysql_error())));
					}
				}
				$subs->Get($item_id);
				$subc = !empty($subs->id) ? $subs->GetList(array(array('pid', '=', $subs->id))) : array();
				echo $output;	
				if(!empty($subs->id)){	?>
					<div class="well">
						<div class="clearfix">
							<label for="catTitle"><?php echo L_EDIT_CATEGORY_NAME_LABEL ?></label>
							<div class="input">
								<input name="catTitle" id="catTitle" value="<?php echo $subs->sectionname; ?>" type="text" class="formfields" />
							</div>
						</div>
						<div class="clearfix">
							<label for="descr"><?php echo L_CATEGORY_DESCRIPTION ?></label>
							<div class="input">
								<textarea name="cat_descr" id="cat_descr"><?php echo view($subs->descr); ?></textarea>
							</div>
						</div>
					</div>
				</div>
					<?php 
					if($subs->cat_type !== 'pages'){ ?>
					<div class="span10"><?php
						if(!empty($subc)){
							?><fieldset id="editSubs">
								<legend><?php echo L_EDIT_SUBCATEGORY_NAME_LABEL ?></legend><?php					
								foreach($subc as $subCat){ ?>
									<div class="clearfix">
										<input type="text" name="sectionname[<?php echo $subCat->id; ?>]" id="cat<?php echo $subCat->id; ?>" value="<?php echo $subCat->sectionname; ?>" class="formfields" />
									</div>
									<div class="clearfix">
										<div class="input">
											<label for="del<?php echo $subCat->id; ?>">
												<input id="del<?php echo $subCat->id; ?>" name="del[]" class="cBox" type="checkbox" value="<?php echo $subCat->id; ?>" />
												<span><?php echo L_DELETE_ITEM ?></span>
											</label>
										</div>
									</div>
									<div class="clearfix">
										<div class="input">
											<textarea name="sectionname_descr[<?php echo $subCat->id; ?>]"><?php echo view($subCat->descr); ?></textarea>
										</div>
									</div>
									<?php
								} ?>
							</fieldset>
							<?php 
						}
						?>
							<fieldset id="addSubcats">
								<legend><?php echo L_ADD_SUBCATEGORY ?></legend>
								<div id="addSubcats1Group" class="row">
									<div class="span6">
										<div class="clearfix">
											<label for="new_cat"><?php echo L_JS_SUBCAT_LABEL ?></label>
											<div class="input">
												<input name="new_cat[]" id="new_cat1" type="text" class="formfields" />
											</div>
										</div>
										<div class="clearfix">
											<label for="new_cat"><?php echo L_JS_SUBCAT_DESCR ?></label>
											<div class="input">
												<textarea name="new_cat_descr[]" id="new_cat_descr1"></textarea>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
						<div class="span16">
							<div class="actions">
								<input type="submit" name="submit" class="btn primary" value="<?php echo L_BUTTON_EDIT ?>" />
								<div class="pull-right">
									<input type="submit" name="submit" class="btn danger" value="<?php echo L_BUTTON_DELETE ?>" />
								</div>
							</div>
						</div>
						<?php 
					} else { ?>
						<div class="span16">
							<div class="actions">
								<input type="submit" name="submit" class="btn primary" value="<?php echo L_BUTTON_EDIT ?>" />
							</div>
						</div><?php
					}
				} else {
					printf(FAILURE, L_CATEGORY_MISSING);
				}	
			} else{
				?><div class="span16"><?php
				if(is_posting(L_BUTTON_DELETE)) {
					if(isset($_POST['del'])){
						foreach($_POST['del'] as $id){
							$st = get_dao('sections');
							$st->Get($id);
							$title = $st->sectionname;
							$reason = empty($title) ? ' '.L_CATEGORY_DOES_NOT_EXIST : $title;
							if(deleteItem($id, 'sections')){
								$result[] = '<li>'.sprintf(L_CATEGORY_DELETED, $title).'</li>';
								$itm = get_dao('sections');
								$di = $itm->GetList(array(array('pid', '=', $id)));
							} else {
								$result[] = '<li>'.vsprintf(L_CATEGORY_NOT_DELETED, array($title,$reason)).'</li>';
							}
						}
						$result = '<ul>'.implode('', $result).'</ul>';
						$auth->updateAdmins();
						printOut(SUCCESS, $result);
					}
				}
				$cats = get_dao('sections');
				$sec = $cats->GetList(array(array('pid', '=', '0')));
				echo $output;
				$hasitems = count($sec) > 0 ? true :  false;
				$tplext = TPL_EXT;
				?>
				<table id="categoriesList" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo L_LIST_CATEGORY_NAME ?></th>
							<th><?php echo L_LIST_NO_OF_ITEMS ?></th>
							<th><?php echo L_LIST_CATEGORY_TYPE ?></th>
							<th>
								<?php echo L_LIST_TEMPLATE_NAME ?> 
								<img src="images/help.gif" alt="" width="16" height="16" class="hasHelp" id="templateName" />
								<blockquote class="helpContents" id="templateNameHelp">
									<h5><?php echo L_LIST_TEMPLATE_NAME ?></h5> 
									<?php echo L_LIST_TEMPLATE_NAME_HELP ?>
								</blockquote>
							</th>
							<th><?php echo L_LIST_CATEGORY_MANAGE ?></th>
							<th>
								<label for="toggleBox">
									<input id="toggleBox" type="checkbox" value="" />
									<span><?php echo L_DELETE_ITEM ?></span>
								</label>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($sec as $ind => $c){
							if($c->cat_type == 'pages' && $c->id != 1){ continue; }
							$itemCount = $Database->Query("SELECT COUNT(id) as count FROM ".PREFIX."items WHERE pid=$c->id");
							$itemCount = $Database->Result(0, 'count'); 
							?>
							<tr<?php echo ($ind % 2) ? ' class="altRow"' : ''; ?>>
								<td><?php echo $c->sectionname ?></td>
								<td><?php echo $itemCount ?></td>
								<td><?php echo $c->cat_type ?></td>
								<td><?php echo $c->dirtitle.$tplext ?></td>
								<td><a href="<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>&amp;id=<?php echo $c->id; ?>" class="editLink">Edit</a></td>
								<td><?php 
									if($c->cat_type != 'pages'){
										?><input id="del<?php echo $c->id; ?>" name="del[]" type="checkbox" value="<?php echo $c->id; ?>" /><?php 
									} ?>
								</td>
							</tr>
							<?php 
						}
						?>
					</tbody>
				</table>
				<p class="contentalert"><?php echo L_DELETE_CATEGORY_ALERT ?></p>
				<p><?php echo L_CATEGORY_ADD_LINK ?></p>
				<?php if($hasitems){ ?>
					<div class="actions">
						<div class="clearfix">
							<div class="pull-right">
								<input name="submit" type="submit" class="btn danger" value="<?php echo L_BUTTON_DELETE ?>" />
							</div>
						</div>
					</div>
				<?php } ?>
				<?php				
			}
		} ?>
	</div> 
</div><?php 
} ?>