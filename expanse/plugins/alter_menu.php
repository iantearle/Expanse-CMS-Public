<?php
/*
Plugin Name: AlterMenu
Plugin URL: http://expansecms.org/
Description: Adds the "Mass Upload" and "Send Newsletter" links to your admin menu for easier navigation. This could be incorporated into Expanse's core, but is left as a plugin to serve as a model and in case you wish to alter the menu further.
Version: 2.0
Author: Mr. Nate Cavanaugh and Mr. Ryan Miglavs
Author URL: http://expansecms.org/
*/

/*  Add the action to the menu html   //-------------------------------*/
ozone_action('admin_menu_html', 'alter_menu', 10, 2);

/* Should return the $menu_html as a string.
 * Accepts as params the current menu html
 * and the array of objects that make up the menu.
 * @param string $menu_html
 * @param array $menu_list
 * @return string $menu_html
 */
function alter_menu($menu_html,$menu_list){
	$menu_html = format_altered_menu($menu_list);
	return $menu_html;
}

/* Custom function for iterating over the menu_list array
 * and recrafting the menu with our custom link
 * @param array $menu_list
 * @return string $menu
 */
function format_altered_menu($menu_list) {
	//The L_ constants are from the language file, but they could be any text you like
	$menu_add = L_MENU_ADD;
	$menu_edit = L_MENU_EDIT;
	$menu_mass_upload = L_MENU_MASS_UPLOAD;
	$menu_send_newsletter = L_MENU_SEND_NEWSLETTER;
	$menu_sep = L_MENU_SEPARATOR;
	$menu = '<ul class="dropdown-menu">';
	foreach($menu_list as $val) {
		if($val->cat_type == 'gallery') {
			$menu .= '
			<li class="dropdown-submenu '.$val->cat_type.'">
				<a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">'.$val->sectionname.'</a>
				<ul class="dropdown-menu">
					<li><a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">' . L_MENU_EDIT . '</a></li>
					<li><a href="index.php?type=add&amp;cat_id=' . $val->id . '&amp;upload=mass">'.L_MENU_MASS_UPLOAD.'</a></li>
					<li><a href="index.php?type=add&amp;cat_id=' . $val->id . '">'.L_MENU_ADD.'</a></li>
				</ul>
			</li>';
		} elseif($val->cat_type == 'newsletter') {
			$menu .= '
			<li class="dropdown-submenu '.$val->cat_type.'">
				<a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">'.$val->sectionname.'</a>
				<ul class="dropdown-menu">
					<li><a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">' . L_MENU_EDIT . '</a></li>
					<li><a href="index.php?type=add&amp;cat_id=' . $val->id . '" class="addTo">'.$menu_send_newsletter.'</a></li>
				</ul>
			</li>';
		} else {
			$menu .= '
			<li class="dropdown-submenu '.$val->cat_type.'">
				<a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">'.$val->sectionname.'</a>
				<ul class="dropdown-menu">
					<li><a href="index.php?type=edit&amp;cat_id=' . $val->id . '" title="'.L_MENU_EDIT.'">' . L_MENU_EDIT . '</a></li>
					<li><a href="index.php?type=add&amp;cat_id=' . $val->id . '" class="addTo">'.L_MENU_ADD.'</a></li>
				</ul>
			</li>';
		}
	}
	$menu .= '</ul>';
	return $menu;
}