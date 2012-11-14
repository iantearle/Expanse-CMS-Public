<?php if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}
/*   Plugins   //-------*/
add_admin_menu('<a href="?cat=admin&amp;sub=plugins">'.L_MENU_MANAGE_PLUGINS.'</a>','','plugins');
if($admin_sub !== 'plugins'){return;}
add_breadcrumb(L_PLUGIN_TITLE);
add_title(L_PLUGIN_TITLE);
ozone_action('admin_page', 'plugin_content');
function plugin_content(){
	global $output;
	if(!CUSTOM_INSTALL){
	?>
		<div class="alert alert-block alert-info fade in" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p><?php echo L_PLUGIN_NOTE ?></p></div>
	<?php
	}
	$current_plugins = getOption('active_plugins');
	$plug_install = check_get_alphanum('install');
	$plug_uninstall = check_get_alphanum('uninstall');
	$get_plugin = check_get_alphanum('plugin', '/');
	if(!empty($get_plugin)){
		if ($plug_install == 'yes') {
			if (!in_array($get_plugin, $current_plugins)) {
				$current_plugins[] = $get_plugin;
				sort($current_plugins);
				if(setOption('active_plugins', $current_plugins)) {
					include(PLUGINS.'/'.$get_plugin);
					applyOzoneAction('install_'.remExtension(basename($get_plugin)));
					printOut(SUCCESS, L_PLUGIN_INSTALLED);
				} else {
					printOut(FAILURE, L_PLUGIN_NOT_INSTALLED);
				}
			}
		} elseif ($plug_uninstall == 'yes') {
			array_splice($current_plugins, array_search($get_plugin, $current_plugins),1);
			if(setOption('active_plugins', $current_plugins)) {
				applyOzoneAction('uninstall_'.remExtension(basename($get_plugin)));
				printOut(SUCCESS, L_PLUGIN_UNINSTALLED);
			} else {
				printOut(FAILURE, L_PLUGIN_NOT_UNINSTALLED);
			}

		}
	}

	//Cleanup & purge
	if ($current_plugins == false || !is_array($current_plugins)) {
		$current_plugins = array();
	}
	foreach ($current_plugins as $key => $current_plugin) {
		if (!file_exists(PLUGINS . "/$current_plugin")) {
				if (!isset($current_plugins[$key])){continue;}
					unset($current_plugins[$key]);
		}
	}
	setOption('active_plugins', $current_plugins);
	$plugins = get_plugins();
	echo $output; ?>
	<table id="pluginList" class="table table-hover table-condensed">
		<?php
		foreach($plugins as $k => $plugin) {
			$css_class = '';
			$installed = false;
			if(in_array($k, $current_plugins)) {
				$css_class = ' class="success"';
				$installed = true;
			}
			?>
			<tr<?php echo $css_class ?>>
				<td>
					<h4><?php echo $plugin->Name; ?> <small><?php echo L_THEME_VERSION.' '.$plugin->Version; ?></small></h4>
					<p><?php echo L_THEME_BY.' '.$plugin->Author; ?></p>
					<p><?php echo  $plugin->Description; ?></p>
				</td>
				<td><a href="index.php?cat=admin&amp;sub=plugins&amp;<?php echo $installed ? 'un' : ''; ?>install=yes&amp;plugin=<?php echo $k; ?>" class="btn <?php echo $installed ? 'btn-danger' : 'btn-success'; ?>"><?php echo $installed ? L_BUTTON_UNINSTALL : L_BUTTON_INSTALL; ?></a></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>