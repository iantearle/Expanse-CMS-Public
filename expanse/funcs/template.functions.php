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

****************************************************************/

/*
-------------------------------------------------
Template functions
-------------------------------------------------
*/
$user_vars = array('menu' => array(), 'header' => array(), 'main' => array(), 'footer' => array(), 'loops' => array());

/*
Conditional functions
*/



/**
 * is_home function.
 * Checks to see if we're on the home page
 *
 * @access public
 * @return void
 */
function is_home() {
	$pcat = CAT_ID;
	$ucat = UCAT;
	return(empty($pcat) && empty($ucat));
}

/**
* Checks to see if we're on a single page
* @return boolean
*/
function is_single() {
	$item_id = ITEM_ID;
	return(!empty($item_id));
}

/**
* Checks to see if we're on a search
* @return boolean
*/
function is_search() {
	$search = SEARCH;
	return(!empty($search));
}

/**
* Checks to see if we're in a category
* @return boolean
*/
function is_category() {
	$pcat = CAT_ID;
	return(!empty($pcat));
}

/**
* Checks to see if we're in a subcategory
* @return boolean
*/
function is_subcat() {
	$subcat = SUBCAT;
	return(!empty($subcat));
}

/**
* Checks to see if we're on a user page
* @return boolean
*/
function is_userpage() {
	$ucat = UCAT;
	return(!empty($ucat));
}

/**
* Checks to see if we're on the feed
* @return boolean
*/
function is_feed() {
	return(basename($_SERVER['PHP_SELF']) == XML_PAGE);
}

/**
* Checks to see if a comment is being sumitted
* @return boolean
*/
function is_commenting() {
	return(isset($_POST['comment']) || isset($_POST['comment_x']));
}

/**
* Checks to see if the contact form is being submitted
* @return boolean
*/
function is_contacting() {
	return(isset($_POST['contact']) || isset($_POST['contact_x']));
}

/**
* Checks to see if the theme has a home page
* @return boolean
*/
function has_homepage() {
	global $themetemplates;
	return(file_exists("$themetemplates/home".TPL_EXT));
}

/**
* Checks to see if the theme is being previewed
* @return boolean
*/
function is_preview() {
	global $test_theme;
	return(LOGGED_IN && !empty($test_theme));
}

/**
* Sets the globabl var fof to 404
*/
function trigger_404() {
	global $fof;
	$fof = '404';
}

/*
Compartmentalize page
*/

/**
* Assembles the menu object and assigns it to the template
* @return string
*/
function make_menu() {
	global $menu, $mainmenu, $themetemplates, $user_vars, $items, $option, $theme_suffix, $Database, $sections, $menu;
	$tplext = TPL_EXT;
	$sortdirection = ($option->sortdirection == 'ASC') ? true : false;
	$sortby = (!empty($option->sortcats)) ? $option->sortcats : 'created';
	$mainmenu = inject_variables($menu, $user_vars['menu']);
	$user_pages = $items->GetList(array(
		array('pid', '=', 0),
		array('online', '=', 1),
		array('type', '=', 'static'),
		array('created', '<=', time())
	), $sortby, $sortdirection);
	//Grab the categories
	$menu = $sections->GetList(array(array('pid', '=', 0), array('cat_type', '!=', 'pages')));
	$mainmenu->sections = prepare_menu_content($menu);
	$user_pages = prepare_menu_content($user_pages);
	$custom = $items->GetList(
		"SELECT
		id, title, pid, dirtitle, menu_order
		FROM *table*
		WHERE online=1
		AND menu_order != 0
		AND created <=".time()."
		AND type='static'
		ORDER BY menu_order ASC");
	$custom_sections = $sections->GetList(
		"SELECT
		id, pid, dirtitle, descr, sectionname as title, public, order_rank as menu_order
		FROM *table*
		WHERE public=1
		AND cat_type != 'pages'
		AND order_rank != 0
		ORDER BY menu_order ASC");
	$custom = array_merge($custom, $custom_sections);
	csort($custom, 'menu_order');
	$custom_menu = prepare_menu_content($custom);
	$mainmenu->custom_menu = $custom_menu;
	$mainmenu->user_pages = $user_pages;
	$mainmenu->pages_list = '<ul class="page_list">' . "\n" . get_full_page_list() . "\n" . '</ul>';
	$mainmenu = inject_variables($mainmenu, $user_vars['menu']);
	$mainmenu = inject_variables($mainmenu, $user_vars['loops']);
	$mainmenu = applyOzone('menu', $mainmenu);
	return sprintt($mainmenu, safe_tpl("$themetemplates/@menu{$tplext}"));
}

/**
* Builds the menu with all the appropriate link vars
* @param array $menu
* @return array $menu
*/
function prepare_menu_content($menu) {
	global $option, $theme_suffix;
	$sections = get_dao('sections');
	$items = get_dao('items');
	foreach ($menu as $k => $val) {
		$categories = isset($val->public) ? true : false;
		$obj = $categories ? 'sections' : 'items';
		$cat_type = $categories ? 'pcat' : 'ucat';
		$conditions = array();
		$conditions[] = array('pid', '=', $val->id);
		if(!$categories) {
			$conditions[] = array('type', '=', 'static');
		}
		$menu[$k]->subcats = $$obj->GetList($conditions);
		$menu[$k]->yoursite = YOUR_SITE;
		$menu[$k]->current = CAT_ID == $val->id ? $val->id : '';
		$menu[$k]->title = isset($val->sectionname) ? $val->sectionname : $val->title;
		$menu[$k]->title = !empty($menu[$k]->title) ? $menu[$k]->title : L_NO_TEXT_IN_TITLE;
		$menu[$k]->descr = $menu[$k]->descr = isset($val->descr) ? applyOzone('category_description',$val->descr) : 'with ozone';
		$menu[$k]->descr = $menu[$k]->descr = isset($val->descr) ? $val->descr : 'with out';
		$menu[$k]->sitename = $option->sitename;
		$menu[$k]->yourname = $option->yourname;
		$menu[$k]->category_link = YOUR_SITE.INDEX_PAGE."?$cat_type=".($val->pid == 0 ? $val->id : $val->pid) . $theme_suffix;
		$menu[$k]->category_link = (CLEAN_URLS) ? YOUR_SITE."$val->dirtitle" . $theme_suffix : $menu[$k]->category_link;
		if($val->pid != 0) {
			$menu[$k]->category_link = "{$menu[$k]->category_link}&amp;subcat=$val->id" . $theme_suffix;
			$menu[$k]->category_link = (CLEAN_URLS) ? "{$menu[$k]->category_link}/$val->dirtitle" . $theme_suffix : $menu[$k]->category_link;
		}
		$menu[$k]->link =&  $menu[$k]->category_link;
		foreach($menu[$k]->subcats as $i => $v) {
			$menu[$k]->subcats[$i]->yoursite = YOUR_SITE;
			$menu[$k]->subcats[$i]->title = isset($v->sectionname) ? $v->sectionname : $v->title;
			$menu[$k]->subcats[$i]->sitename = $option->sitename;
			$menu[$k]->subcats[$i]->yourname = $option->yourname;
			$menu[$k]->subcats[$i]->subid = $menu[$k]->subcats[$i]->id;
			$v->descr = applyOzone('category_description',$v->descr);
			$menu[$k]->subcats[$i]->descr = $menu[$k]->subcats[$i]->descr  = isset($v->descr) ? $v->descr : '';
			$menu[$k]->subcats[$i]->subcategory_link = $categories ? "{$menu[$k]->category_link}&amp;subcat=$v->id" . $theme_suffix :  YOUR_SITE.INDEX_PAGE."?$cat_type=$v->id" . $theme_suffix;
			$menu[$k]->subcats[$i]->subcategory_link = (CLEAN_URLS) ? ($categories ? "{$menu[$k]->category_link}/$v->dirtitle" : YOUR_SITE.$v->dirtitle) . $theme_suffix : $menu[$k]->subcats[$i]->subcategory_link;
			$menu[$k]->subcats[$i]->link =& $menu[$k]->subcats[$i]->subcategory_link;
		}
	}
	return $menu;
}

/**
* Assembles the header object and assigns it to the template
* @return string
*/
function make_header() {
	global $header, $themetemplates, $user_vars;
	$tplext = TPL_EXT;
	grab_title('header');
	$header = inject_variables($header, $user_vars['header']);
	$header = inject_variables($header, $user_vars['loops']);
	$header = applyOzone('header', $header);
	return sprintt($header, safe_tpl("$themetemplates/@header{$tplext}"));
}

/**
* Assembles the footer object and assigns it to the template
* @return string
*/
function make_footer() {
	global $footer, $themetemplates, $user_vars, $item_id;
	$tplext = TPL_EXT;
	grab_title('footer');
	$footer = inject_variables($footer, $user_vars['footer']);
	$footer = inject_variables($footer, $user_vars['loops']);
	$footer = applyOzone('footer', $footer);
	return sprintt($footer, safe_tpl("$themetemplates/@footer{$tplext}"));
}

/**
* Adds an array of objects loop to the global var $user_vars
* @param string $arg
* @param string $name
*/
function add_loop($arg, $name) {
	global $user_vars;
	if(is_string($arg)) {
		$var = prepare_content($arg); // Removed for custom loops.'|ignore_paging:true');
		$loop_vars = !isset($user_vars['main'][$name]) ? array() : $user_vars['main'][$name];
		$loop = $var['page']->content;
		if(!empty($loop_vars) && !empty($loop)) {
			foreach($loop as $key => $obj) {
				foreach($loop_vars as $var => $value) {
					$loop[$key]->{$var} = $value;
				}
			}
		}
	} elseif(is_array($arg)) {
		$loop = $arg;
	}
	$user_vars['loops'][$name] = (isset($user_vars['loops'][$name])) ? array_merge($user_vars['loops'][$name],$loop) : $loop;
}

/*   Not sure why I added this...   //-------------------------------*/
function get_items($arg = '') {
	global $user_vars;
	return prepare_content($arg, $user_vars);
}


/**
* Adds a variable to the global var $user_vars
* @param string $arg
* @param string $area
* @param string $loop
*/
function add_variable($arg, $area = 'main', $loop = '') {
	global $user_vars;
	$var = parse_strr($arg, false);
	if(!empty($loop)) {
		$user_vars[$area][$loop] = isset($user_vars[$area][$loop]) ? array_merge($user_vars[$area][$loop],$var) : $var;
	} else {
		$user_vars[$area] = isset($user_vars[$area]) ?  array_merge($user_vars[$area],$var) : $var;
	}
}

/**
* Parses a pipe separated list of colon separated name value pairs (ex: name:value|name2:value2)
* @param string $arg
* @param boolean $tolower
*/
function parse_strr($arg, $tolower = true) {
	$arg = explode('|', $arg);
	$arr = array();
	foreach($arg as $val) {
		$narg = explode(':', $val);
		$narg[0] = strtolower($narg[0]);
		if(!in_array($narg[0], array('template', 'category', 'subcategory')) && $tolower) {
			$narg[1] = strtolower($narg[1]);
		}
		$arr[$narg[0]] = unsafe_tpl($narg[1]);
	}
	unset($narg);
	return $arr;
}

/**
* Adds a pregenerated page title, as well as
* individual information (item title, category, subcategory) to the global $user_vars
* @param string $user_section
*/
function grab_title($user_section) {
	global $items, $item_id, $user_vars, $ucat, $pcat, $subcat, $sections, $Database;
	$category = '';
	$subcategory = '';
	$title = '';
	$page_title = '';
	if(!empty($pcat)) {
		$sections->Get($pcat);
		if($sections->pid == 0) {
			$category = $sections->sectionname;
			$page_title = $category;
		}
	}
	if(!empty($subcat)) {
		$sections->Get($subcat);
		$subcategory = $sections->sectionname;
		$page_title .= " &raquo; $subcategory";
	}
	if(is_single() || is_userpage()) {
		$id = is_single() ? $item_id : $ucat;
		if(empty($items->title)) {
			$items->Get($id);
		}
		$title = $items->title;
		$page_title .= " &raquo; $title";
	}
	$user_vars[$user_section]['page_title'] = $page_title;
	$user_vars[$user_section]['title'] = $title;
	$user_vars[$user_section]['category'] = $category;
	$user_vars[$user_section]['subcategory'] = $subcategory;
}

/**
* Assembles the objects for the RSS feeds, and either returns
* the template, or prints it depending on whether $return is true or false
* @param string $arg
* @param string $extraVars
* @param boolean $return
* @return string
*/
function expanseXML($arg = '', $extraVars = '', $return = false) {
	global $items, $sections, $comments, $users, $Database, $output;
	$option = getAllOptions();
	foreach($option as $ok => $ov) {
		$page = new stdClass();
		$page->{$ok} = $ov;
	}
	$paginate = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$arr = parse_strr($arg);
	$conditions = array(array('online', '=', 1), array('created', '<=', time()));
	if(is_search()) {
		$sortby = 'pid';
		preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $arr['search'], $matches);
		$terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);
		$orderby = false;
		$limitvalue = 10;
		$templatesdir = isset($arr['templatedir']) ? $arr['templatedir'] : EXPANSEPATH.'/themes/'.$option->theme.'/templates/';
		$template 	= $templatesdir . '@'.(isset($arr['template']) ? unsafe_tpl($arr['template']) : $category_type . getOption('tplext'));
		$ignore_paging = true;
		$content = $items->Search($conditions, $terms, $sortby, $orderby, $limitvalue, '*');
	} else {
		// -- not a search
		if(isset($arr['category'])) {
			$pcat = $sections->GetList(array(array('sectionname', '=', $arr['category']), array('pid', '=', 0)));
			$pcat = $pcat[0]->id;
		} else {
			$sections->Get($option->startcategory);
			$defcat = $sections->id;
			$defaultcat = $sections->sectionname;
		}
		$pcat = !empty($pcat) ? $pcat : $defcat;
		$parent_category = isset($arr['category']) ? $arr['category'] : $defaultcat;
		$conditions[] = array('pid', '=', $pcat);
		$subcategories = (!isset($arr['id'])) ? $sections->GetList(array(array('pid', '=', $pcat))) : array();
		$subcat = '';
		if(isset($arr['subcategory'])) {
			$subcat = $sections->GetList(array(array('sectionname', '=', $arr['subcategory']), array('pid', '=', $pcat)));
			$subcat = !empty($subcat) ? $subcat[0]->id : '';
			$conditions[] = array('cid', '=', $subcat);
		}
	}
	$feedtype = isset($arr['feedtype']) ? $arr['feedtype'] : 'rss';
	$sortby = isset($arr['sortby']) ? $arr['sortby'] : ((!empty($option->sortcats)) ? $option->sortcats : 'created');
	$orderby = (isset($arr['orderby'])) ? $arr['orderby'] : false;
	$orderby = ($orderby == 'newestontop' || $orderby == 'newontop' || $orderby == 'newestfirst') ? false : true;
	$orderby = (empty($option->sortdirection)) ? $orderby : (($option->sortdirection == 'ASC') ? true : false);
	$howmany = (isset($arr['howmany'])) ? $arr['howmany'] : $option->howmany;
	$item_id = (isset($arr['id'])) ? (int)$arr['id'] : null;
	$limitvalue = $paginate * $howmany - ($howmany);
	$templatesdir = isset($arr['templatedir']) ? $arr['templatedir'] : EXPANSEPATH.'/themes/'.$option->theme.'/templates/';
	$template = $templatesdir.'@'.(isset($arr['template']) ? unsafe_tpl($arr['template']) : $arr['category'] . getOption('tplext'));
	if(is_null($item_id) && !is_search()) {
		$content = $items->GetList($conditions, $sortby, $orderby, "$limitvalue, $howmany", '*');
	} else {
		$content[] = $items->Get($item_id);
	}

	if(is_search()) {
		$content = assignContent($content, array('options' => $option));
	} elseif(!empty($content)) {
		$content = assignContent($content, array('pcat' => $pcat, 'options' => $option));
	}
	foreach($content as $ind => $val) {
		if($feedtype == 'rss') {
			$content[$ind]->created = rssTimestamp($content[$ind]->created);
			$content[$ind]->title = strip_tags($content[$ind]->title);
			$content[$ind]->descr = strip_tags($content[$ind]->descr);
			$content[$ind]->title = xmlEntities($content[$ind]->title);
			$content[$ind]->descr = xmlEntities($content[$ind]->descr);
		} elseif($feedtype == 'atom') {
			$content[$ind]->created = get_iso_8601_date($content[$ind]->created);
		}
	}
	$lastmod = end($content);
	if($lastmod && !empty($content)) {
		$page->lastmodified = $lastmod->created;
	}
	$page->category = $parent_category;
	$page->subcategory = isset($arr['subcategory']) ? $arr['subcategory'] : '';
	$page->content = $content;
	$page->subcats = $subcategories;
	$page->pcat = $pcat;
	$page->subcat = $subcat;
	$page->category_link = (!is_home()) ? YOUR_SITE.INDEX_PAGE."?pcat=$pcat" : YOUR_SITE;
	$page->category_link = (CLEAN_URLS) ? YOUR_SITE."$page->category" : $page->category_link;
	$page->subcategory_link = (!is_subcat()) ? '' : $page->category_link . "&amp;subcat={$subcat}";
	$page->subcategory_link = (!is_subcat()) ? '' : (CLEAN_URLS) ? $page->category_link . "/$subcat" : $page->subcategory_link;
	$page->template_path = dirname($template);
	$page = inject_variables($page, $extraVars);
	$page = applyOzone('xml_page_content', $page);
	$page = ozone_walk($page);
	if(!$return ) {
		printt($page, $template);
	} else {
		return sprintt($page, $template);
	}
}

/**
* Inserts the variables from $var into the template object $tpl_obj
* @param object $tpl_obj
* @param array $vars
* @return object $tpl_obj
*/
function inject_variables($tpl_obj, $vars) {
	if(!empty($vars) && is_array($vars)) {
		foreach ($vars as $ind => $val) {
			if(is_array($val)) {
				if(!isset($tpl_obj->{$ind})) {
					global $user_vars;
					$tpl_obj->{$ind} = (!isset($user_vars['loops'][$ind])) ? array() : $user_vars['loops'][$ind];
				}
				foreach($tpl_obj->{$ind} as $ev_i => $ev_v) {
					foreach ($val as $ni => $nv) {
						if(is_int($ni)){continue;}
						$tpl_obj->{$ind}[$ev_i]->{$ni} = $nv;
					}
				}
				continue;
			}
			$tpl_obj->{$ind} = $val;
		}
	}
	return $tpl_obj;
}

/**
* Assembles the objects for the pages, and either returns
* the template, or prints it depending on whether $return is true or false
* @param string $arg
* @param array $extraVars
* @param boolean $return
* @return string $return
*/
function expanse($arg = '', $extraVars = array(), $return = false) {
	$content = prepare_content($arg, $extraVars, $return );
	$content['page'] = applyOzone('page_content', $content['page']);
	if(!$return ) {
		printt($content['page'], $content['template']);
	} else {
		return sprintt($content['page'], $content['template']);
	}
}

/**
* Assembles the objects for the pages, and returns an array
* containing the template object and the file path to the template
* @param string $arg
* @param array $extraVars
* @param boolean $return
* @return array
*/
function prepare_content($arg = '', $extraVars = array(), $return = false) {
	global $items, $sections, $comments, $users, $Database, $output, $user_vars, $theme_suffix;
	$page = new stdClass();
	$option = getAllOptions();
	foreach($option as $ok => $ov) {
		$page->{$ok} = $ov;
	}
	$paginate = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$arr = parse_strr($arg);
	$conditions = array(array('online', '=', 1), array('created', '<=', time()));
	$is_static = (isset($arr['type']) && $arr['type'] == 'static') ? true : false;
	$is_search = isset($arr['search']);
	$category_name = '';
	$category_type = '';
	$category_descr = '';
	$pcat = false;
	$defcat = false;
	$defaultcat = '';
	if(is_search()) {
		$sortby = 'pid';
		$terms = check_get_alphanum('search');
		$orderby = false;
		$limitvalue = 10;
		$templatesdir = isset($arr['templatedir']) ? $arr['templatedir'] : EXPANSEPATH.'/themes/'.$option->theme.'/templates/';
		$template 	= $templatesdir . '@'.(isset($arr['template']) ? unsafe_tpl($arr['template']) : $category_type . getOption('tplext'));
		$ignore_paging = true;
		$content = $items->Search($conditions, $terms, $sortby, $orderby, $limitvalue, '*');
	} else {
		// -- not a search
		if(isset($arr['category'])) {
			$pcat = $sections->GetList(array(array('sectionname', '=', $arr['category']), array('pid', '=', 0)));
			if(!empty($pcat)) {
				$category_type = $pcat[0]->cat_type;
				$category_name = $pcat[0]->dirtitle;
				$category_descr = $pcat[0]->descr;
				$pcat = $pcat[0]->id;
			} else {
				$pcat = false;
			}
		} elseif($is_static) {
			$defcat = 0;
		} else {
			$sections->Get($option->startcategory);
			$defcat = $sections->id;
			$defaultcat = $sections->sectionname;
			$category_type = $sections->cat_type;
			$category_descr = $sections->descr;
		}
		$pcat = ($pcat !== false ? $pcat : ($defcat !== false ? $defcat : 0));
		$parent_category = isset($arr['category']) ? $arr['category'] : $defaultcat;
		$conditions[] = array('pid', '=', $pcat);
		$subcategories = (!isset($arr['id'])) ? $sections->GetList(array(array('pid', '=', $pcat))) : array();
		$subcat = '';
		$subcategory_name = '';
		if(isset($arr['subcategory'])) {
			$subcategory_name = $arr['subcategory'];
			$subcat = $sections->GetList(array(array('sectionname', '=', $subcategory_name), array('pid', '=', $pcat)));
			$category_descr = !empty($subcat) ? $subcat[0]->descr : $category_descr;
			$subcat = !empty($subcat) ? $subcat[0]->id : '';
			$conditions[] = array('cid', '=', $subcat);
		}
		$sortby = isset($arr['sortby']) ? $arr['sortby'] : ((!empty($option->sortcats)) ? $option->sortcats : 'created');
		$orderby = (isset($arr['orderby'])) ? $arr['orderby'] : ($sortby == 'order_rank' ? true : false);
		$orderby = ($orderby == 'newestontop' || $orderby == 'newontop' || $orderby == 'newestfirst') ? false : true;
		$orderby = (empty($option->sortdirection)) ? $orderby : (($option->sortdirection == 'ASC' || $sortby == 'order_rank') ? true : false);
		$howmany = (isset($arr['howmany'])) ? $arr['howmany'] : $option->howmany;
		$item_id = (isset($arr['id'])) ? (int)$arr['id'] : null;
		$ignore_paging = isset($arr['ignore_paging']) ? true : ((empty($howmany) || $howmany == 0) ? true : false);
		$limitvalue = $ignore_paging == false ? ($paginate * $howmany - ($howmany)).', '.$howmany : $howmany;
		$templatesdir = isset($arr['templatedir']) ? $arr['templatedir'] : EXPANSEPATH.'/themes/'.$option->theme.'/templates/';
		$template 	= $templatesdir . '@'.(isset($arr['template']) ? unsafe_tpl($arr['template']) : $category_type . getOption('tplext'));
		if($pcat === false && $defcat === false) {
			trigger_404();
			return array('page' => array(), 'template' => $templatesdir.'@misc.tpl.html');
		}
		if(is_null($item_id)  && !is_search()) {
			$content = $items->GetList($conditions, $sortby, $orderby, $limitvalue, '*');
		} else {
			$items->Get($item_id);
			$content = array();
			if(!empty($items->id)) {
				if(($items->created <= time() && $items->online == 1) || PREVIEW) {
					$content = array($items);
				}
			}
		}
	}

	//Pagination
	if(!$is_static && !$ignore_paging) {
		$andMore = !empty($subcat) ? " AND cid=$subcat" : '';
		$Database->Query("SELECT COUNT(*) as item_count FROM ".PREFIX."items WHERE pid=$pcat{$andMore} AND created <=".time()." AND online=1");
		$itemcount = $Database->Result(0, 'item_count');
		$pagecount = ceil($itemcount / $howmany);
		for($i = 1; $i <= $pagecount; $i++) {
			$page->pages[$i] = new stdClass();
			$page->pages[$i]->pagenumber = $i;
			$page->pages[$i]->currentpage = $paginate;
			$page->pages[$i]->previouspage = $paginate - 1;
			$page->pages[$i]->nextpage = $paginate + 1;
			$page->pages[$i]->pagecount = $pagecount;
			$page->pages[$i]->category = $pcat;
			$page->pages[$i]->subcategory = $subcat;

			//Previous link
			$previous_link = '';
			$previous_link_url = '';
			if($page->pages[$i]->previouspage > 0 && $i == 1) {
				$previous_link_url = INDEX_PAGE.'?pcat=' . $pcat;
				$previous_link_url .= empty($subcat) ? '' : "&amp;subcat=$subcat";
				$previous_link_url .= '&amp;page=' . $page->pages[$i]->previouspage;
				if(CLEAN_URLS) {
					$previous_link_url = YOUR_SITE;
					$previous_link_url .= $category_name;
					$previous_link_url .= empty($subcat) ? '' : "/$subcategory_name";
					$previous_link_url .= '/page/'.$page->pages[$i]->previouspage;
				}
				$previous_link = '<a href="' . $previous_link_url . $theme_suffix . '" id="prev">'.L_PREVIOUS_TEXT.'</a>';
			}
			$page->pages[$i]->previous_link_url = $previous_link_url;
			$page->pages[$i]->previous_link = $previous_link;

			//Next link
			$next_link = '';
			$next_link_url = '';
			if($page->pages[$i]->nextpage <= $pagecount && $i == $pagecount) {
				$next_link_url = INDEX_PAGE.'?pcat=' . $pcat;
				$next_link_url .= empty($subcat) ? '' : "&amp;subcat=$subcat";
				$next_link_url .= '&amp;page=' . $page->pages[$i]->nextpage;
				if(CLEAN_URLS) {
					$next_link_url = YOUR_SITE;
					$next_link_url .= $category_name;
					$next_link_url .= empty($subcat) ? '' : "/$subcategory_name";
					$next_link_url .= '/page/'.$page->pages[$i]->nextpage;
				}
				$next_link = '<a href="' . $next_link_url . $theme_suffix . '" id="next">'.L_NEXT_TEXT.'</a>';
			}
			$page->pages[$i]->next_link_url = $next_link_url;
			$page->pages[$i]->next_link = $next_link;

			//Pages links
			$page_link_url = '';
			$page_link_url = INDEX_PAGE.'?pcat=' . $pcat;
			$page_link_url .= empty($subcat) ? '' : "&amp;subcat=$subcat";
			$page_link_url .= '&amp;page=' . $i;
			$page_link = $i;
			if(CLEAN_URLS) {
				$page_link_url = YOUR_SITE;
				$page_link_url .= $category_name;
				$page_link_url .= empty($subcat) ? '' : "/$subcategory_name";
				$page_link_url .= '/page/'.$i;
			}
			if($paginate != $i) {
				$page_link = '<a href="' . $page_link_url . $theme_suffix . '">' . $i . '</a>';
			}
			$page->pages[$i]->page_link_url = $page_link_url;
			$page->pages[$i]->page_link = $page_link;
		}
	}
	// END Pagination

	$page_ul = '';
	$parent_link = '';
	if(!empty($content)) {
		$pcat = is_null($item_id) ? $pcat : $content[0]->pid;
		if($is_static) {
			$pagecat = $sections->GetList(array(array('cat_type', '=', 'pages')));
			$pcat = $pagecat[0]->id;
			$page_id = is_null($item_id) ? 0 : $item_id;
			$page_ul = '<ul class="page_list">' . get_full_page_list(0, $page_id) . '</ul>';
			if($items->pid != 0) {
				$parent_page = clone($items);
				$parent_page->Get($items->pid);
				$yoursite = YOUR_SITE;
				$url = CLEAN_URLS ? $yoursite.$parent_page->dirtitle : $yoursite.INDEX_PAGE.'?ucat=' . $parent_page->id;
				$parent_link = '<a href=" '.$url. '">' . $parent_page->title . '</a>';
			}
		}
		$content = assignContent($content, array('pcat' => $pcat, 'options' => $option, 'page_list' => $page_ul, 'parent_link' => $parent_link));
		/* if(!$is_search) {
			foreach ($subcategories as $k => $itemObject) {
				$subcontent = $items->GetList(array(array('cid', '=', $itemObject->id)));
				$subcategories[$k]->category_link = YOUR_SITE.INDEX_PAGE."?pcat=$pcat" . '&amp;subcat='.$itemObject->id;
				$subcategories[$k]->category_link = (CLEAN_URLS) ? YOUR_SITE.$category_name . '/'.$itemObject->dirtitle : $subcategories[$k]->category_link;
				$subcategories[$k]->content = assignContent($subcontent, array('pcat' => $pcat, 'options' => $option));
			}
		}*/
	} else {
		printOut(FAILURE, L_NO_ENTRIES_USER);
	}
	$page->page_list = $page_ul;
	$lastmod = end($content);
	if($lastmod && !empty($content)) {
		$page->lastmodified = $lastmod->created;
	}
	$page->category = $parent_category;
	$page->category_dirtitle = $category_name;
	$page->subcategory = isset($arr['subcategory']) ? $arr['subcategory'] : '';
	$page->content = $content;
	$page->subcats = $subcategories;
	$page->output = $output;
	$page->item_count = (!$is_static && !$ignore_paging) ? $itemcount : null;
	$page->pcat = $pcat;
	$page->category_link = (!is_home()) ? YOUR_SITE.INDEX_PAGE."?pcat=$pcat" : YOUR_SITE;
	$page->category_link = (CLEAN_URLS) ? YOUR_SITE."$page->category" : $page->category_link;
	$page->subcategory_link = (!is_subcat()) ? '' : $page->category_link . "&amp;subcat={$subcat}";
	$page->subcategory_link = (!is_subcat()) ? '' : (CLEAN_URLS) ? $page->category_link . "/$subcat" : $page->subcategory_link;
	$page->subcat = $subcat;
	$category_description = applyOzone('category_description',$category_descr);
	$page->category_description = $category_description;
	$page->descr = $category_descr;
	$page->logged_in = LOGGED_IN;
	$page->template_path = dirname($template);
	$page = inject_variables($page, $extraVars);
	if(!empty($user_vars['loops'])) {
		foreach($user_vars['loops'] as $name => $loop) {
			$page->{$name} = (!isset($page->{$name})) ? $loop : $page->{$name};
		}
	}
	$page = ozone_walk($page);
	return array('page' => $page, 'template' => $template);
}

/**
* Prepares the array of objects by inserting common variables into the object
* @param array $content
* @param array $extraVars
* @param boolean $return
* @return array
*/
function assignContent($content, $extraVars = array()) {
	global $items, $sections, $comments, $users, $Database, $output, $images, $theme_suffix, $pcat, $subcat;
	$option = isset($extraVars['options']) ? $extraVars['options'] : getAllOptions();
	$extraVars['copyrightdate'] = isset($extraVars['copyrightdate']) ? $extraVars['copyrightdate'] : date('Y');
	$extraVars['logged_in'] = isset($extraVars['logged_in']) ? $extraVars['logged_in'] : LOGGED_IN;
	$smiliespath = EXPANSE_URL. '/themes/' . $option->theme . '/images/smilies';
	$currencysymbols = array('USD' => '&#36;', 'AUD' => '&#36;', 'GBP' => '&pound;', 'CAD' => '&#36;', 'EUR' => '&euro;', 'JPY' => '&yen;', );
	$expanseurl = EXPANSE_URL;
	$uploads = pathinfo(UPLOADS);
	$uploads = $uploads['basename'];
	$yoursite = YOUR_SITE;
	$commentsmilies = $option->commentsmilies;
	$dateformat = $option->dateformat;
	$timeformat = $option->timeformat;
	$timeoffset = ($option->timeoffset * 3600) + date('Z');
	$paypal_logo = $option->paypal_logo;
	$currency_type = $option->paypal_currency_code;
	$currency_symbol = $currencysymbols[$currency_type];
	$paypal_email = $option->paypal_email;
	$paypal_logo = $option->paypal_logo;
	$paypal_shipping = $option->paypal_shipping;
	$paypal_shipping2 = $option->paypal_shipping2;
	$paypal_tax = $option->paypal_tax;
	$paypal_handling_cart = $option->paypal_handling_cart;
	$customFields = new Expanse('customfields');
	$uploads_url = UPLOADS_DIR;
	$error_img = $expanseurl . 'images/' . ERROR_IMG;
	$current_cat = !empty($pcat) ? $pcat : $option->startcategory;
	$parent_name = $sections->sectionname;
	$parent_dirtitle = $sections->Get($current_cat);
	$parent_dirtitle = $sections->dirtitle;
	$subcat_dirtitle = '';
	if(!empty($subcat)) {
		$sections->Get($subcat);
		$subcat_dirtitle = $sections->dirtitle;
	}
	foreach($content as $k => $itemObj) {
		if(CLEAN_URLS && empty($pcat)) {
			$sections->Get($itemObj->pid);
			$parent_dirtitle = $sections->dirtitle;
		}

		//Category Link
		$content[$k]->category = $parent_name;
		$content[$k]->category_dirtitle = $parent_dirtitle;
		$content[$k]->category_link = $yoursite.((CLEAN_URLS) ? "$parent_dirtitle" : INDEX_PAGE."?pcat={$itemObj->pid}") . (!empty($subcat) ? (CLEAN_URLS ? "/$subcat_dirtitle": "&amp;subcat={$subcat}"): '') . $theme_suffix;

		//Body
		$content[$k]->body = $itemObj->descr;
		$content[$k]->excerpt = trim_excerpt($itemObj->descr, '', true, true);

		//Permalink & Editlink
		$content[$k]->permalink = $yoursite.((CLEAN_URLS) ? "$parent_dirtitle/$itemObj->dirtitle" : INDEX_PAGE."?pcat={$itemObj->pid}&amp;item={$itemObj->id}") . $theme_suffix;
		if($itemObj->type == 'static') {
			$content[$k]->page_link = YOUR_SITE.INDEX_PAGE."?ucat=$itemObj->id" . $theme_suffix;
			$content[$k]->page_link = (CLEAN_URLS) ?  YOUR_SITE."$itemObj->dirtitle" . $theme_suffix : $content[$k]->page_link;
			$content[$k]->page_title = $itemObj->title;
		}
		$itemObj->pid = $itemObj->type != 'static' ? $itemObj->pid : 7;
		$content[$k]->editlink = $content[$k]->edit_link = "{$expanseurl}index.php?type=edit&amp;cat_id={$itemObj->pid}&amp;id={$itemObj->id}";
		$content[$k]->uploads_url = $uploads_url;

		//Image
		$content[$k]->image = (!empty($itemObj->image)) ? $uploads_url . '/' . $itemObj->image : null;
		$content[$k]->image_name = $itemObj->image;
		$content[$k]->image_tag = '<img src="' . ((!is_null($content[$k]->image) ? $content[$k]->image : $error_img)) . '" title="' . $content[$k]->title . '" alt="' . $content[$k]->title . '" />';

		//Thumbnail
		$content[$k]->thumbnail = ($itemObj->autothumb == 1 && empty($itemObj->thumbnail) ? $expanseurl . 'funcs/tn.lib.php?id=' . $itemObj->id . '&amp;thumb=1' : (!empty($itemObj->thumbnail) ? $uploads_url . '/' . $itemObj->thumbnail : null));
		$content[$k]->thumbnail_name = $itemObj->thumbnail;
		$content[$k]->thumbnail_tag = '<img src="' . $content[$k]->thumbnail . '" title="' . $content[$k]->title . '" alt="' . $content[$k]->title . '" />';

		//Users
		$users->Get($itemObj->aid);
		$content[$k]->username = $users->username;
		$content[$k]->user_email = $users->email;
		$content[$k]->author = $users->displayname;
		$content[$k]->user_url = $users->url;
		$sections->Get($itemObj->cid);
		$content[$k]->sub_cat = $sections->sectionname;
		foreach($option as $ok => $ov) {
			$content[$k]->{$ok} = $ov;
		}
		if(!empty($extraVars)) {
			foreach($extraVars as $ek => $ev) {
				if(is_object($ev) || is_array($ev)) {
					foreach($ev as $si => $sv) {
						$content[$k]->{$si} = $sv;
					}
				} else {
					$content[$k]->{$ek} = $ev;
				}
			}
		}
		$customFieldsArray = $customFields->GetList(array(array('itemid', '=', $itemObj->id)));
		foreach($customFieldsArray as $ex_ind => $ex_val) {
			$custom_var = 'custom_var'.($ex_ind+1);
			$content[$k]->{$custom_var.'_label'} = $ex_val->field;
			$content[$k]->{$custom_var} = $ex_val->value;
			$customFieldsArray[$ex_ind]->label = $ex_val->field;
		}
		$content[$k]->extra_fields = $customFieldsArray;
		unset($customFieldsArray);
		$extraoptions = trim($itemObj->extraoptions);
		$content[$k]->extraoptions = !empty($extraoptions) ? unserialize($extraoptions) : array();
		foreach($content[$k]->extraoptions as $ek => $ev) {
			$content[$k]->options[$ek]->option = $ev;
		}

		//Date
		$content[$k]->timestamp = $itemObj->created;
		$content[$k]->date = gmdate($dateformat, $itemObj->created + $timeoffset);
		$content[$k]->time = gmdate($timeformat, $itemObj->created + $timeoffset);

		//Smilies
		$content[$k]->descr = $itemObj->smilies == 1 ? parseSmilies($itemObj->descr, $smiliespath) : $itemObj->descr;
		if($itemObj->for_sale === 1) {
			$content[$k]->paypal_logo = $paypal_logo;
			$content[$k]->price = $itemObj->paypal_amount;
			$content[$k]->currency_type = $currency_type;
			$content[$k]->currency_symbol = $currency_symbol;
			$content[$k]->paypal_email = $paypal_email;
			$content[$k]->paypal_logo = $paypal_logo;
			$content[$k]->paypal_shipping = $paypal_shipping;
			$content[$k]->paypal_shipping2 = $paypal_shipping2;
			$content[$k]->paypal_tax = $paypal_tax;
			$content[$k]->paypal_handling_cart = $paypal_handling_cart;
			$content[$k]->paypal_form = '<div id="simpleCart">';
			$content[$k]->paypal_form .= '<div class="simpleCart_shelfItem">
				<p>Item Name: '.$content[$k]->title.'</p>
				<select class="item_Size">';
			foreach($content[$k]->extraoptions as $o => $p) {
				$content[$k]->paypal_form .= "<option>".$p."</option>\n";
			}
			$content[$k]->paypal_form .= '</select>';
			$content[$k]->paypal_form .= '<a href="javascript:;" onclick="simpleCart.add(\'name='. $content[$k]->title .'\',\'price='. $content[$k]->price .'\',\'size='.dirify($content[$k]->extraoptions[0]).'\',\'quantity=1\',\'thumb='. $content[$k]->thumbnail .'\');" class="simpleCart_add">add to cart</a>';
			$content[$k]->paypal_form .= '<input type="hidden" name="business" value="'.$paypal_email.'">';
			$content[$k]->paypal_form .= '<input type="hidden" name="currency_code" value="'.$content[$k]->currency_type.'">';
			$content[$k]->paypal_form .= '<input type="hidden" name="shipping" value="1%2e50">';
			$content[$k]->paypal_form .= '</div>';
			$content[$k]->paypal_form .= '<div class="simpleCart_items" ></div>';
			$content[$k]->paypal_form .= '
			SubTotal: <span class="simpleCart_total"></span> <br />
			Tax: <span class="simpleCart_taxCost"></span> <br />
			Shipping: <span class="simpleCart_shippingCost"></span> <br />
			-----------------------------<br />
			Final Total: <span class="simpleCart_finalTotal"></span> <br />
			<a href="javascript:;" class="simpleCart_checkout">checkout</a>';
			$content[$k]->paypal_form .= '</div>';
		}

		/*@=*/
		$itemImages = $images->GetList(array(array('itemid', '=', $itemObj->id)));
		foreach($itemImages as $index => $image_val) {
			foreach($option as $ok => $ov) {
				$itemImages[$index]->{$ok} = $ov;
			}
			if(!empty($extraVars)) {
				foreach($extraVars as $ek => $ev) {
					if(is_object($ev) || is_array($ev)) {
						foreach($ev as $si => $sv) {
							$itemImages[$index]->{$si} = $sv;
						}
					} else {
						$itemImages[$index]->{$ek} = $ev;
					}
				}
			}
			$itemImages[$index]->logged_in = $extraVars['logged_in'];
			$itemImages[$index]->image_count = count($itemImages);

			//Image
			$itemImages[$index]->image = $uploads_url . '/' . $image_val->image;
			$itemImages[$index]->image_tag = '<img src="' . $itemImages[$index]->image . '" title="' . $content[$k]->title . '" alt="' . $content[$k]->title . '" />';
			$itemImages[$index]->image_name = $itemImages[$index]->image;

			//Thumbnail
			$itemImages[$index]->thumbnail = $expanseurl . 'funcs/tn.lib.php?image_id=' . $image_val->id;
			$itemImages[$index]->thumbnail_tag = '<img src="' . $itemImages[$index]->thumbnail . '" title="' . $content[$k]->title . '" alt="' . $content[$k]->title . '" />';
			$itemImages[$index]->thumbnail_name = $itemImages[$index]->thumbnail;
		}
		$content[$k]->image_set = $itemImages;
		$content[$k]->image_count = count($itemImages);

		/*@=*/
		$itemComments = $comments->GetList(array(array('itemid', '=', $itemObj->id),array('online', '=', 1)));
		foreach($itemComments as $index => $comment_val) {
			foreach($option as $ok => $ov) {
				$itemComments[$index]->{$ok} = $ov;
			}
			if(!empty($extraVars)) {
				foreach($extraVars as $ek => $ev) {
					if(is_object($ev) || is_array($ev)) {
						foreach($ev as $si => $sv) {
							$itemComments[$index]->{$si} = $sv;
						}
					} else {
						$itemComments[$index]->{$ek} = $ev;
					}
				}
			}
			$itemComments[$index]->timestamp = $comment_val->created;
			$itemComments[$index]->date = gmdate($dateformat, $comment_val->created + $timeoffset);
			$itemComments[$index]->time = gmdate($timeformat, $comment_val->created + $timeoffset);
			$itemComments[$index]->url = (strtolower($comment_val->url) === '') ? '' : (strpos(strtolower($comment_val->url), 'http://') === false) ? 'http://' . $comment_val->url : $comment_val->url;
			$itemComments[$index]->message = $commentsmilies == 1 ? parseSmilies($comment_val->message, $smiliespath) : $comment_val->message;
			$itemComments[$index]->logged_in = $extraVars['logged_in'];
			$itemComments[$index]->editlink = "{$expanseurl}index.php?type=edit&amp;cat=admin&amp;sub=comments&amp;id={$comment_val->id}";
		}
		$content[$k]->the_comments = $itemComments;
		$content[$k]->comments_count = count($itemComments);

		$content[$k]->images_folder = THEME_URL."images/";
	}
	return $content;
}

/**
* Recursively creates a nested HTML list of user created pages
* @param int $default
* @param int $parent
* @param int $level
* @return string $page_list
*/
function get_full_page_list($default = 0, $parent = 0, $level = 0) {
	global $items, $ucat, $theme_suffix, $option;
	$pages = $items->GetList(array(array('type', '=', 'static'), array('pid', '=', $parent), array('online', '=', '1')));
	$page_list = '';
	$yoursite = YOUR_SITE;
	if(!empty($pages)) {
		foreach($pages as $page) {
			$url = CLEAN_URLS ? $yoursite.$page->dirtitle : $yoursite.INDEX_PAGE.'?ucat='.$page->id;
			if(!empty($item_id)) {
				if($page->id == $ucat) {
					$page_list .= '<li class="page_' . $page->id . ' user_page current_page"><a href="' . $url . $theme_suffix . '" title="' . $page->title . '">' . $page->title . '</a></li>' . "\n";
				}
			}
			$page_list .= '<li class="page_' . $page->id . ' user_page"><a href="' . $url . $theme_suffix . '" title="' . $page->title . '">' . $page->title . '</a></li>' . "\n" . '<ul>' . get_full_page_list($default, $page->id, $level + 1) . '</ul>' . "\n";
		}
	}
	return $page_list;
}

/**
 * html_substr function.
 * Returns a portion of a string while leaving intact any HTML tags in that string
 *
 * @access public
 * @param mixed $posttext
 * @param int $minimum_length (default: 200)
 * @param int $length_offset (default: 10)
 * @return string $posttext
 */
function html_substr($posttext, $minimum_length = 200, $length_offset = 10) {
	$tag_counter = 0;
	$quotes_on = false;
	if(strlen($posttext) > $minimum_length) {
		for($i = 0; $i < strlen($posttext); $i++) {
			$current_char = substr($posttext, $i, 1);
			if($i < strlen($posttext) - 1) {
				$next_char = substr($posttext, $i + 1, 1);
			} else {
				$next_char = "";
			}
			if(!$quotes_on) {
				if($current_char == "<") {
					if($next_char == "/") {
						$tag_counter++;
					} else {
						$tag_counter = $tag_counter + 3;
					}
				}
				if($current_char == "/") {
					$tag_counter = $tag_counter - 2;
				}
				if($current_char == ">") {
					$tag_counter--;
				}
				if($current_char == "\"") {
					$quotes_on = true;
				}
			} else {
				if($current_char == "\"") {
					$quotes_on = false;
				}
			}
			if($i > $minimum_length - $length_offset && $tag_counter == 0) {
				$posttext = substr($posttext, 0, $i + 1) . "...";
				return $posttext;
			}
		}
	}
	return $posttext;
}

/**
* Replaces the colon character with its character entity (For Windows file paths)
* @param string $str
* @return string $str
*/
function safe_tpl($str) {
	$str = str_replace(':', '\x3a', $str);
	$str = str_replace('|', '\x7c', $str);
	return $str;
}

function unsafe_tpl($str) {
	$str = str_replace('\x3a', ':', $str);
	$str = str_replace('\x7c', '|', $str);
	return $str;
}

/**
* Read a compiled template and check if its not stale
* @param string $id
* @return string $content
*/
function ets_cache_read_handler($id) {
	global $themefilepath;
	$cachedir = "$themefilepath/cache";
	$cachefile = $cachedir . '/' . basename($id) . '.cache';
	if(!file_exists($cachedir) || !is_dir($cachedir) || !is_writable($cachedir)) {
		return;
	}
	$content = false;
	if(@filemtime($cachefile) > @filemtime("$id")) {
		if($handle = @fopen($cachefile, 'rb')) {
			$size = @filesize($cachefile);
			$content = @fread($handle, $size);
			fclose($handle);
		}
	}
	return $content;
}

/**
* Write a compiled template
* @param string $id
* @param string $content
*/
function ets_cache_write_handler($id, $content) {
	global $themefilepath;
	$cachedir = "$themefilepath/cache";
	$cachefile = $cachedir . '/' . basename($id) . '.cache';
	if(!file_exists($cachedir) || !is_dir($cachedir) || !is_writable($cachedir)) {
		return;
	}
	if($handle = @fopen($cachefile, 'wb')) {
		@fwrite($handle, $content);
		fclose($handle);
	}
}

/**
* Creates and sends a comment (default handler)
* @return void
*/
function handle_comment() {
	if(!is_commenting()) {
		return;
	}
	global $option, $themetemplates, $tplext, $themecss;
	if(!isFlooding($option->floodcontrol)) {
		$commenthandle = new commentProcess;
		$moderate_comments = isset($option->moderate_comments) && $option->moderate_comments == 1 ? true : false;
		$commenthandle->Subject = $moderate_comments == false ? sprintf(L_COMMENT_MAILER_SUBJECT,$option->sitename) : sprintf(L_COMMENT_PENDING_MAILER_SUBJECT,$option->sitename);
		$commenthandle->FromName = sprintf(L_COMMENT_MAILER_FROM,$option->sitename);
		$commenthandle->RequiredFields = "name,email,url,message,itemid";
		$commenthandle->Template = "$themetemplates/@commentmailer{$tplext}";
		$commenthandle->ExtraVars = array('themecss' => YOUR_SITE. $themecss, 'pageurl' => ltrim($_SERVER['REQUEST_URI'], '/'), 'ip' => $_SERVER['REMOTE_ADDR'], 'hostmask' => gethostbyaddr($_SERVER['REMOTE_ADDR']), 'expanseurl' => EXPANSE_URL, 'cms_name' => CMS_NAME, 'company_url' => COMPANY_URL);
		register_flooding();
		$commenthandle->commentHandle();
	} else {
		printOut(FAILURE, sprintf(L_FLOODING_MESSAGE, $option->floodcontrol));
	}
}

/**
* Sends an email based on the contact form (default handler)
* @return void
*/
function handle_contact() {
	if(!is_contacting()) {
		return;
	}
	global $option, $themetemplates, $tplext, $themecss;
	if(!isFlooding($option->floodcontrol)) {
		$contact = new contactProcess;
		$contact->Subject = L_CONTACT_SUBJECT;
		$contact->FromName = sprintf(L_CONTACT_FROM,$option->sitename);
		$contact->RequiredFields = "name,email,url,message";
		$contact->Template = "$themetemplates/@contactmailer{$tplext}";
		$contact->ExtraVars = array('themecss' => YOUR_SITE. $themecss, 'pageurl' => ltrim($_SERVER['REQUEST_URI'], '/'), 'ip' => $_SERVER['REMOTE_ADDR'], 'hostmask' => gethostbyaddr($_SERVER['REMOTE_ADDR']), 'expanseurl' => EXPANSE_URL, 'cms_name' => CMS_NAME, 'company_url' => COMPANY_URL );
		register_flooding();
		$contact->contactHandle();
	} else {
		printOut(FAILURE, sprintf(L_FLOODING_MESSAGE, $option->floodcontrol));
	}
}

/**
* Manages the flooding option
* @return string $time
*/
function register_flooding() {
	global $option;
	$time = false;
	if(!isset($_SESSION['current_time'])) {
		$time = time();
	} elseif((time() - $_SESSION['current_time']) > $option->floodcontrol) {
		$time = time();
	}
	if($time) {
		$time = applyOzoneAction('flooding_time', $time);
		$_SESSION['current_time'] = $time;
	}
	return $time;
}
