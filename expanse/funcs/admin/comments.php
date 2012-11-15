<?php
/****************************************************************

                    `-+oyhdNMMMMMMMNdhyo/-`
                .+ymNNmys+:::....-::/oshmNNdy/.
             :smMmy/-``.-:-:-:----:-::--..-+hNNdo.
          .smMdo-`.:::.`               `.-::-`:smMd/`
        .yMNy- -::`                         `-::`:hMmo`
      `yMNo``:/`                               `-/--yMN+
     /mMy.`:-                                  ```./--dMd.
    sMN/ //`                                    `..`-/`sMN/
   yMm-`s.                                       `.-.`+-/NN+
  yMm--y. ```.-/ooyoooo/:.                        `---`/::NN/
 +MN:.h--/sdNNNNMMMNNNmmmhdoo+:.                  `.-::`/:+MN.
`NMs`hyhNNMMMMMMMMMMMNNNmhyso+syy/:-.`          `.-/+o++:. hMh
+MN.`:ssdmmmmmmmmmmmmhyyyo++:.``   `.-:::://:::::.```````  -MN-
mMy    ````````....`````````                         ````  `dMo
MM+            ````                                  ````   yMy
MM:                                                  ````   yMd
MM+                                                  ````   yMy
dMy                                                  ````  `dM+
+Mm.       ``-://++oo+///-``    ``-::/ooooyhhddddddmmm+yo. -MN-
`NM+ -/+s.`ommmmmmmmmmmmmmddhyhyo+++oosyhhdddmmmNNNNMddmh+ hMh
 /MN-oNmds``sdmmmmNNNNNmmmdNmmdddhhyyyyyhhdddmmmNNmmy-+:s`+MN.
  sMm-sNmd+`.ydmmNNNNNNmmmNNNmdhysso+oosyssssso/:--:`.-o`:NN/
   yMm-+Nmds..ymmmNNNNNmNNNNNmdhyso++//::--...```..``:+ /NN+
    sNN/-hmdh+-ommNNNNmNNNNNNmdhyso+//::--..````.` .+:`oMN/
     /mMy.+mmddhhmNNNmmNMNNNNmdyso+//::--..````` `++`-dMd.
      `yMN+./hNmmmmmmmmmNNNNmmhyso+//:--..``..`-//`-yMN/
        .yMNy--odNNNmmmmmNNNmdhyso+/::--..`.://-`:hMmo`
          .smMdo-.+ydNNmmddmmdysso+/::::////.`:smMd/`
             :smMmy+---/oysydhhyyyo/+/:-``-+hNNdo.
                .+yNMNmhs+/::....-::/oshmNNdy/.
                    .-+oyhdNMMMMMMMNdhyo/-`

Expanse - Content Management For Web Designers, By A Web Designer
			  Extended by Ian Tearle, @iantearle
		Started by Nate Cavanaugh and Jason Morrison
			www.alterform.com & www.dubtastic.com

****************************************************************/

if(!defined('EXPANSE')) { die('Sorry, but this file cannot be directly viewed.'); }

/*   Comments   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=comments">'.L_ADMIN_MANAGE_COMMENTS.'</a>',array(),'comments');
if($admin_sub !== 'comments') { return; }
$items = isset($items) && is_object($items) ? $items : new Expanse('items');
$comments = isset($comments) && is_object($comments) ? $comments : new Expanse('comments');
if(empty($item_id)) {
	add_breadcrumb(L_COMMENT_EDIT_TITLE);
	add_title(L_COMMENT_EDIT_TITLE);
} else {
	add_breadcrumb('<a href="index.php?cat=admin&sub=comments">'.L_COMMENT_EDIT_TITLE.'</a>');
	$comments->Get($item_id);
	$comment_title = !empty($comments->name) ? $comments->name : L_NO_TEXT_IN_TITLE;
	add_breadcrumb(sprintf(L_CURRENTLY_EDITING, $comment_title));
	add_title(sprintf(L_CURRENTLY_EDITING, $comment_title));
}
ozone_action('admin_page', 'comments_content');

function comments_content() {
	global $output, $comments, $items, $item_id;
	if(is_posting(L_BUTTON_DELETE)) {
		if(isset($_POST['del'])) {
			$del = check_array($_POST['del']);
			foreach($del as $id) {
				$ct = $comments;
				$ct->Get($id);
				$name = empty($ct->name) ? L_COMMENT_NAME_MISSING : $ct->name;
				if(deleteItem($id, 'comments')) {
					$result[] = '<li>'.sprintf(L_COMMENT_DELETE_SUCCESS,$name).'</li>';
				} else {
					if(empty($ct->id)) {
						$result[] = '<li>'.L_COMMENT_MISSING.'</li>';
					} else {
						$result[] = '<li>'.sprintf(L_COMMENT_DELETE_FAILURE,$name).'</li>';
					}
				}
			}
			$ips_to_ban = array();
			if(isset($_POST['ban_delete'])) {
				$ip = check_array($_POST['ip']);
				$banned_ips = getOption('bannedips');
				$banned_ips = $banned_ips == false ? '' : $banned_ips;
				foreach($ip as $k => $the_ip) {
					if(!isset($del[$k]) || strpos($banned_ips, $the_ip) !== false || in_array($the_ip,$ips_to_ban)) { continue; }
					$ips_to_ban[] = $the_ip;
				}
				$ips_to_ban_proper = proper_list($ips_to_ban);
				$ips_to_ban = (empty($banned_ips) ? '' : $banned_ips.',').implode(',',$ips_to_ban);
			}

			$result = '<ul>'.implode("\n", $result).'</ul>';
			printOut(SUCCESS, $result);
			if(!empty($ips_to_ban) && !empty($ips_to_ban_proper)) {
				if(setOption('bannedips', $ips_to_ban)) {
					printOut(SUCCESS, sprintf(L_COMMENT_IPS_BANNED, $ips_to_ban_proper));
				} else {
					printOut(FAILURE, sprintf(L_COMMENT_IPS_NOT_BANNED, $ips_to_ban_proper));
				}
			}
		}
	}
	if(!empty($item_id)) {
		$comment =& $comments;
		if(is_posting(L_BUTTON_EDIT)) {
			if(saveItem($item_id, 'comments', $_POST)) {
				$comment->Get($item_id);
				printOut(SUCCESS, vsprintf(L_EDIT_SUCCESS, $comment->name));
			} else {
				$comment->Get($item_id);
				printOut(FAILURE,vsprintf(L_EDIT_FAILURE, $comment->name));
			}
		}
		$comment->Get($item_id);
		$hasitems = (empty($comment->id)) ? 0 : 1;
		echo $output;
		if($hasitems) {
		?>
			<form action="" method="post">
				<div class="row">
					<div class="span12">
						<input type="hidden" name="itemid" value="<?php echo $comment->itemid ?>" />
						<input type="hidden" name="cid" value="<?php echo $comment->cid ?>" />
						<div class="control-group">
							<label for="online" class="checkbox">
								<input name="online" type="hidden" id="" value="0" />
								<input type="checkbox" name="online" value="1" id="online" <?php echo ($comment->online == 1) ? 'checked="checked"' : '';?> />
								<?php echo L_COMMENT_ONLINE ?>
							</label>
						</div>
						<div class="control-group">
							<label for="name" class="control-label"><?php echo L_COMMENT_NAME ?></label>
							<div class="controls">
								<input name="name" id="name" class="formfields" type="text" value="<?php echo $comment->name ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="email" class="control-label"><?php echo L_COMMENT_EMAIL ?></label>
							<div class="controls">
								<input name="email" id="email" type="text" class="formfields" value="<?php echo $comment->email ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="url" class="control-label"><?php echo L_URL ?></label>
							<div class="controls">
								<input name="url" id="url" type="text" class="formfields" value="<?php echo $comment->url ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="message" class="control-label"><?php echo L_COMMENT_COMMENTS ?></label>
							<div class="controls">
								<textarea name="message" rows="6" cols="" id="message" class="formfields xxlarge"><?php echo $comment->message ?></textarea>
							</div>
						</div>

						<input type="hidden" name="del[]" value="<?php echo $comment->id ?>" />
						<?php applyOzoneAction('manage_comment', $comment); ?>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" name="submit" value="<?php echo L_BUTTON_EDIT ?>" class="btn btn-primary" />
					<div class="pull-right">
						<input type="submit" name="submit" value="<?php echo L_BUTTON_DELETE ?>" class="btn btn-danger" />
					</div>
				</div>
			</form>
		<?php
		} elseif(!$hasitems && is_posting(L_BUTTON_DELETE)) {
			printf(FAILURE, L_COMMENT_DOES_NOT_EXIST);
		}
	}
	if(empty($item_id)) {
		$commentsList = $comments->GetList(array(array('itemid','>',0)));
		$hasitems = empty($commentsList) ? 0 : 1;
		unset($commentsList);
		if(!$hasitems) {
			printOut(FAILURE, L_NO_COMMENTS);
		}
		echo $output;
		if($hasitems) {
			$timeformat = getOption('timeformat');
			$dateformat = getOption('dateformat');
			$itemList = $items->GetList(array(array('id','>','0')), 'id', false); ?>
			<div class="accordion" id="stretchContainer">
			<?php
			foreach($itemList as $item) {
				$comment = $comments->GetList(array(array('itemid','=',$item->id)), 'id', false);
				if(!empty($comment)) { ?>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#<?php echo str_replace(' ', '', $item->title) ?>"><?php echo $item->title; ?></a>
					</div>
					<div id="<?php echo str_replace(' ', '', $item->title) ?>" class="accordion-body collapse">
						<div class="accordion-inner">
							<table class="commentBlock table table-striped">
								<thead>
									<tr>
										<th><?php echo L_COMMENT_POSTED_BY; ?></th>
										<th>Message</th>
										<th>Created</th>
										<th>Edit</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php
								foreach($comment as $ind => $comm):
									$comm->name = trim_title($comm->name,L_COMMENT_NAME_MISSING);
									$comm->message = trim_excerpt($comm->message, L_COMMENT_MESSAGE_MISSING);
								?>
									<tr class="tools">
										<td>
											<?php echo '<a href="'.$_SERVER['REQUEST_URI'].'&amp;id='.$comm->id.'">'.$comm->name.'</a>'; ?>
										</td>
										<td>
											<p><?php echo $comm->message;?></p>
										</td>
										<td>
											<p><?php echo userDate($comm->created); ?></p>
										</td>
										<td>
											<a href="<?php echo $_SERVER['REQUEST_URI'] ;?>&amp;id=<?php echo $comm->id; ?>" title="<?php echo L_COMMENT_EDIT ?>" class="editLink"><?php echo L_COMMENT_EDIT ?></a>
										</td>
										<td>
											<input id="ip<?php echo $comm->id; ?>" name="ip[]" type="hidden" value="<?php echo $comm->ip; ?>" />
											<div class="control-group">
												<label for="del<?php echo $comm->id; ?>" class="checkbox">
													<input id="del<?php echo $comm->id; ?>" name="del[]" type="checkbox" value="<?php echo $comm->id; ?>" />
													<?php echo L_BUTTON_DELETE ?>
												</label>
											</div>
										</td>
									</tr>
								<?php
								endforeach;
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
				}
			}
			?>
			</div>
			<div class="actions">
				<div class="row">
					<div class="span12">
						<div class="form-actions">
							<div class="control-group pull-left">
								<label for="ban_delete" class="extendedLabels checkbox">
									<input type="checkbox" id="ban_delete" name="ban_delete" class="buttons" value="1" />
									<?php echo L_BAN_DELETE ?>
								</label>
							</div>
							<input type="submit" name="submit" id="submit" class="btn btn-danger pull-right" value="<?php echo L_BUTTON_DELETE ?>" />
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
