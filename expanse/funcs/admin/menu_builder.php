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

/*   Menu builder   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=menu_builder">'.L_MB_LINK.'</a>',array(),'menuBuilder');

if($admin_sub !== 'menu_builder') { return; }
add_breadcrumb(L_MB_NAME);
add_title(L_MB_NAME);
ozone_action('admin_page', 'menu_builder_content');
ozone_action('admin_js_url', 'mb_include_full_js');

function menu_builder_content() {
	printf(NOTE, L_MB_NOTE);
	$sections = get_dao('sections');
	$items = get_dao('items');
	$public = $items->GetList("SELECT
						  id, title, pid, dirtitle, menu_order
						  FROM *table*
						  WHERE online=1
						  AND menu_order != 0
						  AND created <=".time()."
						  AND type='static'
						  ORDER BY menu_order ASC");
	$public_sections = $sections->GetList("SELECT
								  id, pid, dirtitle, sectionname as title, public, order_rank as menu_order
								  FROM *table*
								  WHERE public=1
								  AND cat_type != 'pages'
								  AND order_rank != 0
								  ORDER BY menu_order ASC");
	$public = array_merge($public, $public_sections);
	csort($public, 'menu_order');
	$private = $items->GetList("SELECT
					  id, title, pid, dirtitle, menu_order
					  FROM *table*
					  WHERE online=1
					  AND menu_order = 0
					  AND created <=".time()."
					  AND type='static'
					  ORDER BY menu_order ASC");
	$private_sections = $sections->GetList("SELECT
								  id, pid, dirtitle, sectionname as title, public, order_rank as menu_order
								  FROM *table*
								  WHERE cat_type != 'pages'
								  AND (order_rank = 0
								  OR public = 0)
								  ORDER BY order_rank ASC");
	$private = array_merge($private, $private_sections);
	$include_subcats = getOption('mb_include_subcats');
	$include_subcats = $include_subcats == false || $include_subcats == 0 ? 'no' : 'yes';
?>
	<div class="row">
		<div class="span12">
			<input type="hidden" id="cb_subcats" value="<?php echo $include_subcats ?>" />
			<div id="beforeMenuContainer"></div>
		</div>
	</div>
	<div class="row">
		<div class="span6">
			<div id="keepMenuContainer">
				<div id="keepMenu">
					<h2>Final custom menu<br /><small>Your live menu</small></h2>
					<?php
					foreach($public as $menu_item){
					$title = trim_title($menu_item->title);
					$class = 'kept';
					$class .= ($menu_item->pid !=0) ? ' sub_cat' : '';
					$class .= !isset($menu_item->public) ? ' page' : '';
					?>
					<div id="item_<?php echo $menu_item->id ?>" class="<?php echo $class ?>"><h3><span><?php echo $title ?></span></h3></div>
					<?php
					}
					?>
				</div>
			</div>
		</div>

		<div class="span6">
			<div id="excludeMenuContainer">
				<div id="excludeMenu">
					<h2>Menu trash<br /><small>Drag the categories you don't want on the menu here</small></h2>
					<?php
					foreach($private as $menu_item){
					$title = trim_title($menu_item->title);
					$class = 'trashed';
					$class .= ($menu_item->pid !=0) ? ' sub_cat' : '';
					$class .= !isset($menu_item->public) ? ' page' : '';
					?>
					<div id="item_<?php echo $menu_item->id ?>" class="<?php echo $class ?>"><h3><span><?php echo $title ?></span></h3></div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
<?php
}
function mb_include_full_js($url) {
	return (!strpos($url, 'full=true') ? $url.'?full=true' : $url);
}
