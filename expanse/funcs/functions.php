<?php
/********* Expanse ***********/
/* // Do not edit anything below
--------------------------------------------------
*/
fixServerSettings();
regenerate_session(false);
$output = '';
function debug($data){
	echo '<div class="debug_info"><pre>';
	$args = func_get_args();
	foreach($args as $arg){
		if(!(is_array($arg) || is_object($arg))) {
			print_r("$arg \n");
			continue;
		}
		print_r($arg);
	}
	echo "<strong>File information</strong>\n";
	print_r(debug_backtrace());
	echo '</pre></div>';
}
//Cloning objects compatibility
if (version_compare(phpversion(), '5.0') < 0) {
	eval('function clone($object) {
		return $object;
	}');
}
//May add AJAX functionality...
function addItem($table, $data) {
	$expanse = new Expanse($table);
	foreach($data as $k => $v){
		if(array_key_exists($k, get_object_vars($expanse))){
			$expanse->{$k} = $v;
		}
	}
	return $expanse->SaveNew() ? true : false;
}
function saveItem($id, $table, $data) {
	$expanse = new Expanse($table);
	$expanse->Get($id);
	foreach($data as $k => $v){
		if(array_key_exists($k, get_object_vars($expanse))){
			$expanse->{$k} = $v;
		}
	}
	return $expanse->Save() ? true : false;
}
function deleteItem($id, $table) {
	$expanse = new Expanse($table);
	$expanse->Get($id);
	return $expanse->Delete() ? true : false;
}
/*****************************
* Options					 *
*****************************/
function getOption($opt_name, $opt_val='') {
	$opt = isset($GLOBALS['prefs']) && is_object($GLOBALS['prefs']) ? $GLOBALS['prefs']:  new Expanse('prefs');
	$optname = array('opt_name', '=', $opt_name);
	if(!empty($opt_val)){
		$opt = $opt->GetList(array($optname, array('opt_value', '=', "$opt_val")));
	} else {
		$opt = $opt->GetList(array($optname));
	}
	$opt = isset($opt[0]) ? $opt[0] : '';
	return (empty($opt->opt_value)) ? false :  ((@unserialize($opt->opt_value) !== FALSE) ? unserialize($opt->opt_value) : $opt->opt_value);
}
function setOption($opt_name, $opt_val){
	$opt = isset($GLOBALS['prefs']) && is_object($GLOBALS['prefs']) ? $GLOBALS['prefs']:  new Expanse('prefs');
	$opt_val = (is_array($opt_val) || is_object($opt_val)) ? serialize($opt_val) : $opt_val;
	$option = $opt->GetList(array(array('opt_name','=',$opt_name)));
	if(!empty($option)){
		$opt->Get($option[0]->id);
	}
	$opt->opt_name = $opt_name;
	$opt->opt_value = $opt_val;
	if(!empty($option)){
		return ($opt->Save()) ? true : false;
	}
	return ($opt->SaveNew()) ? true : false;
}
function deleteOption($opt_name, $opt_val='') {
	$opt = isset($GLOBALS['prefs']) && is_object($GLOBALS['prefs']) ? $GLOBALS['prefs']:  new Expanse('prefs');
	if(!empty($opt_val)) {
		$option = $opt->GetList(array(array('opt_name','=', $opt_name),array('opt_value','=', $opt_val)));
	} else {
		$option = $opt->GetList(array(array('opt_name','=', $opt_name)));
	}
	foreach($option as $op){
		$opt->Get($op->id);
		$opt->Delete();
	}
}
function getAllOptions() {
	global $Database;
	$buildobject = new stdClass;
	$sql = "SELECT opt_name, opt_value FROM ".PREFIX."prefs";
	$S2 = $Database->Query($sql);
	$buildarray = array();
	while($object = mysql_fetch_object($S2)){
		$buildobject->{$object->opt_name} = $object->opt_value;
	}
	return $buildobject;
}
function manageOptions($exceptions){
	global $Database;
	$report = array();
	$exceptions = str_replace(" ", "", $exceptions);
	$exceptions = explode(",", $exceptions);
	foreach($_POST as $k => $v) {
		if (!in_array($k, $exceptions)) {
			$k = $Database->Escape($k);
			$v = $Database->Escape($v);
			$Database->Query("SELECT id FROM ".PREFIX."prefs WHERE opt_name='$k'");
			$query = ($Database->Rows() > 0) ? "UPDATE ".PREFIX."prefs SET opt_value='$v' WHERE opt_name='$k'" : "INSERT INTO ".PREFIX."prefs (opt_name, opt_value) VALUES('$k', '$v')";
			$queried = $Database->Query($query);
			if($queried){
				$report[] = 1;
			} else {
				$report[$k] = 0;
			}
		}
	}
	if(!in_array(0,$report)){
		printOut(SUCCESS, L_PREFS_UPDATED);
		$result = true;
	} else {
		printOut(FAILURE, L_PREFS_UPDATE_FAILED);
		$result = false;
	}
	return $result;
}
/*****************************
* Misc *
*****************************/
function regenerate_session($delete_old = true) {
	if (version_compare('5.1.0', phpversion(), '>')) {
		session_regenerate_id($delete_old);
	} else {
		if(version_compare('4.3.3', phpversion(), '>')){return;} // 4.3.2 has a bug that will not allow sessions to stay active
			if($delete_old){
				@unlink(ini_get('session.save_path').'/sess_'.session_id());
			}
		session_regenerate_id();
	}
}
function isInstalled() {
	$config_file = realpath(dirname(__FILE__).'/../config.php');
	if(file_exists(realpath($config_file))){
	global $CONFIG;
		if(!empty($CONFIG['host']) && !empty($CONFIG['user']) && !empty($CONFIG['pass']) && !empty($CONFIG['db'])){
			if(!class_exists('Database')){
				global $Database;
				require_once(dirname(__FILE__).'/database.class.php');
			}
			if(!class_exists('Expanse')){
				require_once(dirname(__FILE__).'/expanse.class.php');
			}
			$users = new Expanse('users');
			$users->Get(1);
			if(!empty($users->username)){
				return true;
			}
		}
	}
	return false;
}
function installFile($path='') {
	$install_file = realpath(dirname(__FILE__).'/../install.php');
	$is_installed = isInstalled();
	if(file_exists($install_file)){
		ob_start();
		$css_file = file_exists('./'.EXPANSE_FOLDER.'/css/expanse.css.php') ? './'.EXPANSE_FOLDER.'/css/expanse.css.php': (file_exists('./css/expanse.css.php') ?'./css/expanse.css.php' : (file_exists('../css/expanse.css.php') ? '../css/expanse.css.php' : '')); ?>
		<!DOCTYPE html>
		<html lang="en" class="no-js">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title><?php if($is_installed){ ?>WARNING! - Delete Expanse Install File <?php } else {?> Install Expanse <?php } ?></title>
			<meta name="copyright" content="Little Polar Apps Ltd" />
			<meta name="language" content="EN-US" />
			<meta name="rating" content="General" />
			<meta name="robots" content="index,follow" />
			<meta name="revisit-after" content="7" />
			<meta name="distribution" content="global" />
			<meta name="author" content="Ian Tearle, Nate Cavanaugh, Jason Morrison" />
			<link rel="shortcut icon" href="favicon.ico" />
			<link href="<?php echo $css_file; ?>" rel="stylesheet" type="text/css" />
			<style>
				body { padding-top: 60px; }
				#vanilla #content h1,
				#vanilla #content h2{margin:0.25em 0;}
				#vanilla #content h2{margin:0.25em 0 1em;}
				#vanilla dt{background:none; font-weight:normal;font-size:1em;}
				#vanilla dd{font-size:1.3em;font-weight:bold;}
			</style>
		</head>
		<body id="vanilla">
			<div id="mainContainer">
				<!-- Begin Header -->
				<div class="topbar">
					<div class="topbar-inner">
						<div class="container">
							<a class="brand" href="./"><?php echo CMS_NAME ?></a>
								<p class="pull-right"><?php echo CMS_NAME ?> thinks you're dreamy.</p>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<?php
						if($is_installed){ ?>
							<div class="span8">
								<div class="well">
									<h1>Please delete install.php</h1>
									<p><?php echo CMS_NAME ?> has been installed, but the last step is to delete install.php. It is a security risk, and for your own benefit, please delete it.</p>
									<p>However, make sure you have a copy stored on your computer (where it can't be accessed by the public). This will let you uninstall or reinstall it at a later time.</p>
								</div>
							</div>
							<div class="span8">
								<a href="javascript:window.location.reload();" class="btn primary large">I've deleted it, please let me try again.</a>
								or
								<a href="install.php?step=uninstall" class="btn">Uninstall</a>
							</div>
							<?php
						} else { ?>
							<h1>Please install <?php echo CMS_NAME ?></h1>
							<p>expanse has not been installed yet, but it's yearning with a deep passion for you to give it a whirl.</p>
							<p>How do you do that, exactly? Well, install.php is located inside of your expanse folder. Go ahead and point your browser there, and follow the 2 short steps, and you're on your way.</p>
							<?php
						} ?>
					</div>
				</div>
				<!-- End page content -->
				<!-- Begin Footer -->
				<footer class="footer">
					<div class="container">
						<div class="row">
							<p><a href="<?php echo COMPANY_URL ?>" target="_blank"><?php echo COMPANY_NAME ?></a>. <?php printf(L_COPYRIGHT_FOOTER, date('Y'));?><?php if(!CUSTOM_INSTALL){ ?><?php echo L_MENU_SEPARATOR ?><a href="misc.php?action=license"><?php echo L_LEGAL_FOOTER ?></a><?php echo L_MENU_SEPARATOR ?><a href="http://forums.expansecms.org" target="_blank"><?php echo L_SUPPORT_FOOTER ?></a><?php } ?></p>
						</div>
					</div>
				</footer>
				<!-- End Footer -->
			</div>
		</body>
		</html>
		<?php
		$page = ob_get_contents();
		ob_end_clean();
		if($is_installed || empty($path)){
			die($page);
		}
		header("Location: $path");
	}
	return true;
}
function fixServerSettings() {
	//PHP as CGI
	if (strstr($_SERVER['SCRIPT_NAME'], 'php.cgi')){
		unset($_SERVER['PATH_INFO']);
	}
	//PHP as CGI when it sets SCRIPT_FILENAME to a string ending in php.cgi
	if (isset($_SERVER['SCRIPT_FILENAME']) && (strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7)){
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	}
	//Sets REQUEST_URI for IIS
	if (empty($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		// Append a query string if it's there
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
	//Fix an empty $PHP_SELF
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if(empty($PHP_SELF)){
		$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);
	}
}
function checkFiles($filearr, $uploaddir, $requireimage=false, $filter=array()) {
    $maxsize = ini_get("upload_max_filesize");
    $final = array('errors' => array(),
		'files' => array(),
		'result' => array()
	);
    foreach($filearr as $input => $filearray) {
		//for every file upload field
		if(is_array($filter)) {
			$use = !in_array($input, $filter) ? true : false;
		} else {
			$use = !preg_match($filter, $input) ? true : false;
		}

		if($use) {
			foreach($filearray as $ind => $val) {
				//for every element in each upload
				if ($ind == "name") {
					$file_pieces = remExtension($val, true);
					if(count($file_pieces) == 2){
					$val = dirify($file_pieces[0]).$file_pieces[1];
					}
					$name = time().'_'.$val;
				}
				if ($ind == "tmp_name") {
					$tmp_name = $val;
				}
				if ($ind == "size") {
					$size = $val;
				}
				if ($ind == "type") {
					$type = $val;
				}
				if ($ind == "error") {
					$error = $val;
				}
			}

			if (file_exists($tmp_name)) {
				$final['files'][$input] = $_FILES[$input];
				$final['files'][$input]['name'] = $name;
				unset($final['files'][$input]['tmp_name']);
				$max_size = (isset($_POST['MAX_FILE_SIZE'])) ? $_POST['MAX_FILE_SIZE'] : (intval(ini_get('upload_max_filesize')) * 1024 * 1024);
				if ($size > $max_size) {
					if ($max_size < 1024) {
						$maxfilesize = "$max_size bytes";
					} else if ($max_size < 1048576) {
						$maxfilesize = number_format($max_size/1024) ." KB";
					} else {
						$maxfilesize = number_format($max_size/1048576) ." MB";
					}
					$final['errors'][$input][] = sprintf(L_ERR_UPLOAD_HEAVY, $name, $maxsize);
				} elseif($final['files'][$input]['error'] == 1) {
					$final['errors'][$input][] = sprintf(L_ERR_UPLOAD_HEAVY, $name, $maxsize);
				}
				if ($requireimage) {
					if (!getimagesize($tmp_name)) {
						$final['errors'][$input][] = sprintf(L_ERR_UPLOAD_NOT_IMAGE, $name);
					}
				}
				if (!empty($final['errors'])) {
					$final['result'][$input]['uploaded'] = 0;
				} else {
					if (is_dir($uploaddir)) {
						$upfile = "$uploaddir/$name";
						if (move_uploaded_file($tmp_name, $upfile)) {
							if (substr(sprintf('%o', fileperms($upfile)), -3) < 644) {
								chmod($upfile, 0644);
							}
							$final['result'][$input]['uploaded'] = 1;
							if (getimagesize($upfile)) {
								$dims = getimagesize($upfile);
								$final['files'][$input]['width'] = $dims[0];
								$final['files'][$input]['height'] = $dims[1];
							}
						} else {
							$final['result'][$input]['uploaded'] = 0;
						}
					} else {
						$final['result'][$input]['uploaded'] = 0;
						$final['errors'][$input][] = L_ERR_UPLOAD_MISSING_DIR;
					}
				}
			}
		}// filter
    }
    return $final;
}
function deleteFile($file) {
	$path = UPLOADS;
	$file = $path.'/'.$file;
	if(file_exists($file) && is_file($file)){
		unlink($file);
	}
}
function renameExtracted($filename) {
	$basename = basename($filename);
	$dirname = dirname($filename);
	$file_pieces = remExtension($basename, true);
	if(count($file_pieces) == 2){
		$fname = dirify($file_pieces[0]).$file_pieces[1];
	}
	$fname = time().'_'.$fname;
	$newname = $dirname.'/'.$fname;
	rename($filename,$newname);
	return $newname;
}
function remExtension($name, $return_parts=false) {
	$ext = strrchr($name, '.');
	if($return_parts) {
		return ($ext !== false) ? array(substr($name, 0, -strlen($ext)), $ext) : array($name);
	}
	return ($ext !== false) ? substr($name, 0, -strlen($ext)) : $name;
}
function userDate($ts, $split=' @ ') {
	$optdate = getOption('dateformat').$split.getOption('timeformat');
	$zone = $ts+(3600*getOption('timeoffset'))+date('Z');
	return gmdate($optdate, $zone);
}
function getFiles($dir, $type='all', $recursive=0) {
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			$filearr = array('dirs' => array(), 'files' => array());
			while (($file = readdir($dh)) !== false) {
				if(!is_dir($dir."/".$file) && $file != '.' && $file != '..') {
					$filearr['files'][] = $file;
				} elseif(is_dir($dir."/".$file) && $file != '.' && $file != '..' && !$recursive){
					$filearr['dirs'][] = $file;
				}
				if($recursive){
					if(is_dir($dir."/".$file) && $file != '.' && $file != '..'){
						$filearr['dirs'][$file] = getFiles($dir."/".$file, 'all', 1);
					}
				}
			}
			closedir($dh);
		}
	}
	if($type == 'files') {
		$filearr = $filearr['files'];
	} elseif($type == 'dirs'){
		$filearr = $filearr['dirs'];
	}
	return $filearr;
}
function get_page_dropdown($default = 0, $parent = 0, $level = 0, $mark = false, $start=null) {
	global $item_id;
	$items = isset($GLOBALS['items']) && is_object($GLOBALS['items']) ? $GLOBALS['items'] : new Expanse('items');
	$pages = $items->GetList(array(array('type', '=', 'static'), array('pid', '=', $parent), array('online', '=', '1')));
	if (!empty($pages)) {
		foreach ($pages as $page) {
			if (!empty ($item_id)) {
				if ($page->id == $item_id) {
					continue;
				}
			}
			$pad = str_repeat('&nbsp;', $level * 3);
			$pad = !empty($pad) ? $pad.'&ndash;': $pad;
			if ($page->id == $default || (!is_null($start) && $page->id.':P' == $start)){
				$selected = ' selected="selected"';
			}else{
				$selected = '';
			} ?>
			<option value="<?php echo $page->id.($mark ? ':P' : '') ?>"<?php echo $selected ?>><?php echo $pad.$page->title ?></option> <?php
			get_page_dropdown($default, $page->id, $level+1, $mark, $start);
		}
	} else {
		return false;
	}
}
function get_page_list($default = 0, $parent = 0, $level = 0) {
	global $item_id;
	$items = isset($GLOBALS['items']) && is_object($GLOBALS['items']) ? $GLOBALS['items'] : new Expanse('items');
	$pages = $items->GetList(array(array('type', '=', 'static'), array('pid', '=', $parent), array('online', '=', '1')));
	if (!empty($pages)) {
		foreach ($pages as $page) {
			if (!empty ($item_id)) {
				if ($page->id == $item_id) {
					continue;
				}
			}
			?>
			<li id="page_<?php echo $page->id ?>"><?php echo $page->title.L_MENU_SEPARATOR ?><a href="<?php echo edit_link($page->id); ?>" title="<?php echo L_EDIT_PAGE ?>"><?php echo L_EDIT_PAGE ?></a><?php echo L_MENU_SEPARATOR;  ?><a href="<?php echo edit_link($page->id); ?>>#sharing" title="<?php echo L_SHARE_PAGE ?>"><?php echo L_SHARE_PAGE ?></a><br />
<p class="<?php echo ($page->online == 0) ? 'offline' : 'online'; ?>"><?php echo ($page->online == 0) ? L_PAGE_OFFLINE : L_PAGE_ONLINE; ?><br />
<label for="item_delete_<?php echo $page->id; ?>"><?php echo L_DELETE_ITEM; ?></label><input type="checkbox" name="del[]" value="<?php echo $page->id; ?>" id="item_delete_<?php echo $page->id; ?>" /></p></li>
			<ul>
			<?php
			get_page_list($default, $page->id, $level+1);
			?>
			</ul>
			<?php
		}
	} else {
		return false;
	}
}
function getCatList($catid) {
	global $Database;
	$sections = isset($GLOBALS['sections']) && is_object($GLOBALS['sections']) ? $GLOBALS['sections'] : new Expanse('sections');
	$sec = $sections->Get($catid);
	$catArray = array('parentcat'=>'','parentid'=>'','subcats'=>array());
	$d2=$Database->Query("SELECT t1.sectionname AS lev1, t2.sectionname as lev2, t1.id
						FROM ".PREFIX."sections AS t1
						LEFT JOIN ".PREFIX."sections AS t2 ON t2.pid = t1.id
						WHERE t1.pid = $catid");
	$catArray['parentcat'] = $sec->sectionname;
	$catArray['parentid'] = $catid;
	while($d3=mysql_fetch_array($d2)) {
		$selected = (isset($GLOBALS['cid']) && $GLOBALS['cid'] == $d3['id']) ? ' selected="selected"': '' ;
		$catArray['subcats'][] = array('catname'=>$d3['lev1'], 'id'=>$d3['id']);
	}
	return (object) $catArray;
}
function parseSmilies($text, $smiliespath) {
	$faces = array(';)', ':)', ':O',':(','>:|',':P',':pissed:',':nervous:',':*(',':|', ':D', '<3', '&lt;3');
	$smilies = array(
		'<img src="'.$smiliespath.'/wink.gif" alt=";)" class="smilies" />',
		'<img src="'.$smiliespath.'/smile.gif" alt=":)" class="smilies" />',
		'<img src="'.$smiliespath.'/shock.gif" alt=":O" class="smilies" />',
		'<img src="'.$smiliespath.'/sad.gif" alt=":(" class="smilies" />',
		'<img src="'.$smiliespath.'/frustrated.gif" alt=":|" class="smilies" />',
		'<img src="'.$smiliespath.'/tongue.gif" alt=":P" class="smilies" />',
		'<img src="'.$smiliespath.'/pissed.gif" alt="pissed" class="smilies" />',
		'<img src="'.$smiliespath.'/nervous.gif" alt="nervous" class="smilies" />',
		'<img src="'.$smiliespath.'/depressed.gif" alt=":*(" class="smilies" />',
		'<img src="'.$smiliespath.'/normal.gif" alt=":*(" class="smilies" />',
		'<img src="'.$smiliespath.'/bigsmile.gif" alt=":D" class="smilies" />',
		'<img src="'.$smiliespath.'/heart.gif" alt="<3" class="smilies" />',
		'<img src="'.$smiliespath.'/heart.gif" alt="<3" class="smilies" />'
	);
	$emote = array(
		'faces' => $faces,
		'images' => $smilies
	);
	applyOzone('smilies', $emote);
	return str_replace($emote['faces'], $emote['images'], $text);
}
/*Thank you Adam Kalsey and Gabriel Radic.
Converts text to an SEO friendly, directory-like string.
Adam Kalsey's PHP function, with a ported version of Gabriel Radic's improved PERL function.
*/
function dirify($s) {
     $s = convert_high_ascii($s);
     $s = strtolower($s);
     $s = strip_tags($s);
     $s = preg_replace('!&[^;\s]+;!','',$s);
	 $s = preg_replace('![^\w\s-]!','',$s);
     $s = preg_replace('![\s]+!','-',$s);
     return $s;
}
function convert_high_ascii($s) {
 	$HighASCII = array(
      "\xc3\x80" => "A",
      "\xc3\x81" => "A",
      "\xc3\x82" => "A",
      "\xc3\x83" => "A",
      "\xc3\x84" => "A",
      "\xc3\x85" => "A",
      "\xc4\x80" => "A",
      "\xc4\x82" => "A",
      "\xc4\x84" => "A",
      "\xc7\x8d" => "A",
      "\xc7\x9e" => "A",
      "\xc7\xa0" => "A",
      "\xc7\xba" => "A",
      "\xc8\x80" => "A",
      "\xc8\x82" => "A",
      "\xc8\xa6" => "A",
      "\xc3\xa0" => "a",
      "\xc3\xa1" => "a",
      "\xc3\xa2" => "a",
      "\xc3\xa3" => "a",
      "\xc3\xa4" => "a",
      "\xc4\x81" => "a",
      "\xc4\x83" => "a",
      "\xc4\x85" => "a",
      "\xc7\x8e" => "a",
      "\xc7\x9f" => "a",
      "\xc7\xa0" => "a",
      "\xc7\xbb" => "a",
      "\xc8\x81" => "a",
      "\xc8\x83" => "a",
      "\xc8\xa7" => "a",
      "\xc9\x90" => "a",
      "\xc9\x91" => "a",
      "\xc9\x92" => "a",
      "\xc3\x86" => "Ae",
      "\xc7\xa2" => "Ae",
      "\xc7\xbc" => "AE",
      "\xc3\xa6" => "ae",
      "\xc7\xa3" => "ae",
      "\xc7\xbd" => "ae",
      "\xc6\x81" => "B",
      "\xc6\x82" => "B",
      "\xc9\x93" => "B",
      "\xc6\x80" => "b",
      "\xc6\x83" => "b",
      "\xca\x99" => "b",
      "\xc3\x87" => "C",
      "\xc4\x86" => "C",
      "\xc4\x88" => "C",
      "\xc4\x8a" => "C",
      "\xc4\x8c" => "C",
      "\xc6\x87" => "C",
      "\xc3\xa7" => "c",
      "\xc4\x87" => "c",
      "\xc4\x89" => "c",
      "\xc4\x8b" => "c",
      "\xc4\x8d" => "c",
      "\xc6\x88" => "c",
      "\xc9\x95" => "c",
      "\xca\x97" => "c",
      "\xc3\x90" => "D",
      "\xc4\x8e" => "D",
      "\xc4\x90" => "D",
      "\xc6\x89" => "D",
      "\xc6\x8a" => "D",
      "\xc6\x8b" => "D",
      "\xc3\xb0" => "d",
      "\xc4\x8f" => "d",
      "\xc4\x91" => "d",
      "\xc6\x8c" => "d",
      "\xc6\x8d" => "d",
      "\xc8\xa1" => "d",
      "\xc9\x96" => "d",
      "\xc9\x97" => "d",
      "\xc7\x84" => "DZ",
      "\xc7\x85" => "DZ",
      "\xc7\xb1" => "DZ",
      "\xc7\xb2" => "DZ",
      "\xc7\x86" => "dz",
      "\xc7\xb3" => "dz",
      "\xca\xa3" => "dz",
      "\xca\xa4" => "dz",
      "\xca\xa5" => "dz",
      "\xc3\x88" => "E",
      "\xc3\x89" => "E",
      "\xc3\x8a" => "E",
      "\xc3\x8b" => "E",
      "\xc4\x92" => "E",
      "\xc4\x94" => "E",
      "\xc4\x96" => "E",
      "\xc4\x98" => "E",
      "\xc4\x9a" => "E",
      "\xc6\x8e" => "E",
      "\xc6\x8f" => "E",
      "\xc6\x90" => "E",
      "\xc8\x84" => "E",
      "\xc8\x86" => "E",
      "\xc8\xba" => "E",
      "\xc3\xa8" => "e",
      "\xc3\xa9" => "e",
      "\xc3\xaa" => "e",
      "\xc3\xab" => "e",
      "\xc4\x93" => "e",
      "\xc4\x95" => "e",
      "\xc4\x97" => "e",
      "\xc4\x99" => "e",
      "\xc4\x9b" => "e",
      "\xc7\x9d" => "e",
      "\xc8\x85" => "e",
      "\xc8\x87" => "e",
      "\xc8\xbb" => "e",
      "\xc9\x98" => "e",
      "\xc9\x99" => "e",
      "\xc9\x9a" => "e",
      "\xc9\x9b" => "e",
      "\xc9\x9c" => "e",
      "\xc9\x9d" => "e",
      "\xc9\x9e" => "e",
      "\xca\x9d" => "e",
      "\xc6\x91" => "F",
      "\xc6\x92" => "f",
      "\xc9\xb8" => "f",
      "\xca\xa9" => "fg",
      "\xc4\x9c" => "G",
      "\xc4\x9e" => "G",
      "\xc4\xa0" => "G",
      "\xc4\xa2" => "G",
      "\xc6\x93" => "G",
      "\xc6\x94" => "G",
      "\xc7\xa4" => "G",
      "\xc7\xa6" => "G",
      "\xc7\xb4" => "G",
      "\xc4\x9d" => "g",
      "\xc4\x9f" => "g",
      "\xc4\xa1" => "g",
      "\xc4\xa3" => "g",
      "\xc7\xa5" => "g",
      "\xc7\xa7" => "g",
      "\xc7\xb5" => "g",
      "\xc9\xa0" => "g",
      "\xc9\xa1" => "g",
      "\xc9\xa2" => "g",
      "\xc9\xa3" => "g",
      "\xca\x9c" => "g",
      "\xc4\xa4" => "H",
      "\xc4\xa6" => "H",
      "\xc7\xb6" => "H",
      "\xc8\x9e" => "H",
      "\xc4\xa5" => "h",
      "\xc4\xa7" => "h",
      "\xc6\x95" => "h",
      "\xc8\xa5" => "h",
      "\xc9\xa6" => "h",
      "\xc9\xa7" => "h",
      "\xca\x9c" => "h",
      "\xca\xae" => "h",
      "\xca\xaf" => "h",
      "\xc3\x8c" => "I",
      "\xc3\x8d" => "I",
      "\xc3\x8e" => "I",
      "\xc3\x8f" => "I",
      "\xc4\xa8" => "I",
      "\xc4\xaa" => "I",
      "\xc4\xac" => "I",
      "\xc4\xae" => "I",
      "\xc4\xb0" => "I",
      "\xc6\x96" => "I",
      "\xc6\x97" => "I",
      "\xc7\x8f" => "I",
      "\xc8\x88" => "I",
      "\xc8\x8a" => "I",
      "\xc3\xac" => "i",
      "\xc3\xad" => "i",
      "\xc3\xae" => "i",
      "\xc3\xaf" => "i",
      "\xc4\xa9" => "i",
      "\xc4\xab" => "i",
      "\xc4\xad" => "i",
      "\xc4\xaf" => "i",
      "\xc4\xb1" => "i",
      "\xc7\x90" => "i",
      "\xc8\x89" => "i",
      "\xc8\x8b" => "i",
      "\xc9\xa8" => "i",
      "\xc9\xa9" => "i",
      "\xc9\xaa" => "i",
      "\xc4\xb2" => "IJ",
      "\xc4\xb3" => "ij",
      "\xc4\xb4" => "J",
      "\xc4\xb5" => "j",
      "\xc7\xb0" => "j",
      "\xc9\x9f" => "j",
      "\xca\x84" => "j",
      "\xca\x9d" => "j",
      "\xc4\xb6" => "K",
      "\xc6\x98" => "K",
      "\xc7\xa8" => "K",
      "\xc4\xb7" => "k",
      "\xc4\xb8" => "k",
      "\xc6\x99" => "k",
      "\xc7\xa9" => "k",
      "\xca\x9e" => "k",
      "\xc4\xb9" => "L",
      "\xc4\xbb" => "L",
      "\xc4\xbd" => "L",
      "\xc4\xbf" => "L",
      "\xc5\x81" => "L",
      "\xc8\xb4" => "L",
      "\xc4\xba" => "l",
      "\xc4\xbc" => "l",
      "\xc4\xbe" => "l",
      "\xc5\x80" => "l",
      "\xc5\x82" => "l",
      "\xc6\x9a" => "l",
      "\xc6\x9b" => "l",
      "\xc9\xab" => "l",
      "\xc9\xac" => "l",
      "\xc9\xad" => "l",
      "\xc9\xae" => "l",
      "\xca\x9f" => "l",
      "\xc7\x87" => "LJ",
      "\xc7\x88" => "LJ",
      "\xc7\x89" => "lj",
      "\xca\xaa" => "ls",
      "\xca\xab" => "lz",
      "\xc6\x9c" => "M",
      "\xc9\xaf" => "m",
      "\xc9\xb0" => "m",
      "\xc9\xb1" => "m",
      "\xc3\x91" => "N",
      "\xc5\x83" => "N",
      "\xc5\x85" => "N",
      "\xc5\x87" => "N",
      "\xc5\x8a" => "N",
      "\xc6\x9d" => "N",
      "\xc8\xa0" => "N",
      "\xc3\xb1" => "n",
      "\xc5\x84" => "n",
      "\xc5\x86" => "n",
      "\xc5\x88" => "n",
      "\xc5\x89" => "n",
      "\xc5\x8b" => "n",
      "\xc6\xb5" => "n",
      "\xc8\xb4" => "n",
      "\xc9\xb2" => "n",
      "\xc9\xb3" => "n",
      "\xc9\xb4" => "n",
      "\xc7\x8a" => "NJ",
      "\xc7\x8b" => "NJ",
      "\xc7\xb8" => "NJ",
      "\xc7\x8c" => "nj",
      "\xc7\xb9" => "nj",
      "\xc3\x92" => "O",
      "\xc3\x93" => "O",
      "\xc3\x94" => "O",
      "\xc3\x95" => "O",
      "\xc3\x96" => "O",
      "\xc3\x97" => "O",
      "\xc5\x8c" => "O",
      "\xc5\x8e" => "O",
      "\xc5\x90" => "O",
      "\xc6\x86" => "O",
      "\xc6\x9f" => "O",
      "\xc6\xa0" => "O",
      "\xc7\xb9" => "O",
      "\xc7\xaa" => "O",
      "\xc7\xac" => "O",
      "\xc7\xbe" => "O",
      "\xc8\x8c" => "O",
      "\xc8\x8e" => "O",
      "\xc8\xaa" => "O",
      "\xc8\xac" => "O",
      "\xc8\xae" => "O",
      "\xc8\xb0" => "O",
      "\xc3\xb2" => "o",
      "\xc3\xb3" => "o",
      "\xc3\xb4" => "o",
      "\xc3\xb5" => "o",
      "\xc3\xb6" => "o",
      "\xc3\xb7" => "o",
      "\xc5\x8d" => "o",
      "\xc5\x8f" => "o",
      "\xc5\x91" => "o",
      "\xc6\xa1" => "o",
      "\xc7\x92" => "o",
      "\xc7\xab" => "o",
      "\xc7\xad" => "o",
      "\xc7\xbf" => "o",
      "\xc8\x8d" => "o",
      "\xc8\x8f" => "o",
      "\xc8\xab" => "o",
      "\xc8\xad" => "o",
      "\xc8\xaf" => "o",
      "\xc8\xb1" => "o",
      "\xc9\x94" => "o",
      "\xc9\xb5" => "o",
      "\xc6\xa2" => "OI",
      "\xc6\xa3" => "oi",
      "\xc5\x92" => "Oe",
      "\xc5\x93" => "oe",
      "\xc9\xb6" => "oe",
      "\xc9\xb7" => "oe",
      "\xc6\xa5" => "P",
      "\xca\xa0" => "q",
      "\xc5\x94" => "R",
      "\xc5\x96" => "R",
      "\xc5\x98" => "R",
      "\xc6\xa6" => "R",
      "\xc8\x90" => "R",
      "\xc8\x92" => "R",
      "\xc5\x95" => "r",
      "\xc5\x97" => "r",
      "\xc5\x99" => "r",
      "\xc8\x91" => "r",
      "\xc8\x93" => "r",
      "\xc9\xb9" => "r",
      "\xc9\xba" => "r",
      "\xc9\xbb" => "r",
      "\xc9\xbc" => "r",
      "\xc9\xbd" => "r",
      "\xc9\xbe" => "r",
      "\xca\x80" => "r",
      "\xca\x81" => "r",
      "\xc3\x9f" => "S",
      "\xc5\x9a" => "S",
      "\xc5\x9c" => "S",
      "\xc5\x9e" => "S",
      "\xc5\xa0" => "S",
      "\xc6\xa7" => "S",
      "\xc6\xa9" => "S",
      "\xc8\x98" => "S",
      "\xc5\x9b" => "s",
      "\xc5\x9d" => "s",
      "\xc5\x9f" => "s",
      "\xc5\xa1" => "s",
      "\xc6\xa8" => "s",
      "\xc6\xaa" => "s",
      "\xc8\x99" => "s",
      "\xca\x82" => "s",
      "\xca\x83" => "s",
      "\xca\x84" => "s",
      "\xca\x85" => "s",
      "\xc3\x9e" => "T",
      "\xc5\xa2" => "T",
      "\xc5\xa4" => "T",
      "\xc5\xa6" => "T",
      "\xc6\xac" => "T",
      "\xc6\xae" => "T",
      "\xc8\x9a" => "T",
      "\xc3\xbe" => "t",
      "\xc5\xa3" => "t",
      "\xc5\xa5" => "t",
      "\xc5\xa7" => "t",
      "\xc6\xab" => "t",
      "\xc6\xad" => "t",
      "\xc8\x9b" => "t",
      "\xc8\xb6" => "t",
      "\xca\x87" => "t",
      "\xca\x88" => "t",
      "\xca\xa8" => "tc",
      "\xca\xa6" => "ts",
      "\xca\xa7" => "ts",
      "\xc3\x99" => "U",
      "\xc3\x9a" => "U",
      "\xc3\x9b" => "U",
      "\xc3\x9c" => "U",
      "\xc5\xa8" => "U",
      "\xc5\xaa" => "U",
      "\xc5\xac" => "U",
      "\xc5\xae" => "U",
      "\xc5\xb0" => "U",
      "\xc5\xb2" => "U",
      "\xc6\xaf" => "U",
      "\xc6\xb1" => "U",
      "\xc7\x93" => "U",
      "\xc7\x95" => "U",
      "\xc7\x97" => "U",
      "\xc7\x99" => "U",
      "\xc7\x9b" => "U",
      "\xc8\x94" => "U",
      "\xc8\x96" => "U",
      "\xc8\xa2" => "U",
      "\xc3\xb9" => "u",
      "\xc3\xba" => "u",
      "\xc3\xbb" => "u",
      "\xc3\xbc" => "u",
      "\xc5\xa9" => "u",
      "\xc5\xab" => "u",
      "\xc5\xad" => "u",
      "\xc5\xaf" => "u",
      "\xc5\xb1" => "u",
      "\xc5\xb3" => "u",
      "\xc6\xb0" => "u",
      "\xc7\x94" => "u",
      "\xc7\x96" => "u",
      "\xc7\x98" => "u",
      "\xc7\x9a" => "u",
      "\xc7\x9c" => "u",
      "\xc8\x95" => "u",
      "\xc8\x97" => "u",
      "\xc8\xa3" => "u",
      "\xca\x89" => "u",
      "\xca\x8a" => "u",
      "\xca\x8b" => "u",
      "\xc6\xb2" => "V",
      "\xca\x8c" => "v",
      "\xc5\xb4" => "W",
      "\xc7\xb7" => "W",
      "\xc5\xb5" => "w",
      "\xca\x8d" => "w",
      "\xc3\x9d" => "Y",
      "\xc5\xb6" => "Y",
      "\xc5\xb8" => "Y",
      "\xc6\xb3" => "Y",
      "\xc8\x9c" => "Y",
      "\xc8\xb2" => "Y",
      "\xc3\xbe" => "y",
      "\xc3\xbf" => "y",
      "\xc5\xb7" => "y",
      "\xc6\xb4" => "y",
      "\xc8\x9d" => "y",
      "\xc8\xb3" => "y",
      "\xca\x8e" => "y",
      "\xca\x8f" => "y",
      "\xc5\xb9" => "Z",
      "\xc5\xbb" => "Z",
      "\xc5\xbd" => "Z",
      "\xc6\xb5" => "Z",
      "\xc6\xb7" => "Z",
      "\xc6\xb9" => "Z",
      "\xc7\xae" => "Z",
      "\xc8\xa4" => "Z",
      "\xc5\xba" => "z",
      "\xc5\xbc" => "z",
      "\xc5\xbe" => "z",
      "\xc6\xb6" => "z",
      "\xc6\xb8" => "z",
      "\xc6\xba" => "z",
      "\xc7\xaf" => "z",
      "\xc8\xa4" => "z",
      "\xca\x90" => "z",
      "\xca\x91" => "z",
      "\xca\x92" => "z",
      "\xca\x93" => "z",
      "\xc6\xbb" => "2",
      "\xc6\xbc" => "5",
      "\xc6\xbd" => "5",
      "\xc6\x84" => "6",
      "\xc6\x85" => "6",
 	);
	foreach($HighASCII as $ind=>$val) {
		$HighASCII2["/".$ind."/"] = $val;
	}
 	$find = array_keys($HighASCII2);
 	$replace = array_values($HighASCII2);
 	$s = preg_replace($find,$replace,$s);
    return $s;
}
/*
To be used at a future time...
*/
function getPage($id, $extraVars=array()) {
	global $items, $themetemplates, $Database, $output;
	$tplext = TPL_EXT;
	$option = isset($extraVars['options']) ? $extraVars['options'] : getAllOptions();
	$content = '';
	$pg = $items->GetList(array(array('type', '=', 'static'), array('id', '=', $id)), '', true, 1);
	if(!empty($pg)) {
		foreach($option as $optname => $optval){
			$pg->{$optname} 	= $optval;
		}
		foreach($extraVars as $ek => $ev) {
			$pg->{$ek} 	= $ev;
		}
		$pg[0]->pages = getChildrenPages($pg[0]->id);
		$pagetpl = file_exists("$themetemplates/{$pg[0]->dirtitle}{$tplext}") && is_file("$themetemplates/{$pg[0]->dirtitle}{$tplext}") ?  "$themetemplates/{$pg[0]->dirtitle}{$tplext}" : "$themetemplates/page{$tplext}";
		$content = sprintt($pg, $pagetpl);
	}
	return $content;
}
function getChildrenPages($pid) {
	global $items;
	$content = '';
	return $items->GetList(array(array('type', '=', 'static'), array('pid', '=', $pid)), 'menu_order');
}
function isFlooding($flood_delay) {
	$currtime = (isset($_SESSION['current_time'])) ? $_SESSION['current_time'] : time();
	$diff = (time() != $currtime) ? time()-$currtime : $flood_delay;
	return ($diff < $flood_delay) ? true : false;
}
function wrapIt($str) {
	if(strlen($str) >= 30){
		return wordwrap($str,25, ' ', 1);
	} else {
		return $str;
	}
}
function get_custom_fields() {
	$custom = new Expanse('customfields');
	$cust_fields = array();
	$fields = $custom->GetList(array(array('id', '>', 0)),'id', true, '', 'DISTINCT(field)');
	foreach($fields as $field) {
		$field  = trim($field->field);
		if(!empty($field)){
			$cust_fields[] =  "'".$field."'";
		}
	}
	return $cust_fields = '['.implode(',',$cust_fields).']';
}
function isLoggedIn() {
	$id = isset($_SESSION['id']) && !empty($_SESSION['id']) ? true : false;
	$user = isset($_SESSION['username']) && !empty($_SESSION['username']) ? true : false;
	$password = isset($_SESSION['password']) && !empty($_SESSION['password']) ? true : false;
	$email = isset($_SESSION['email']) && !empty($_SESSION['email']) ? true : false;
	$permissions = isset($_SESSION['permissions']) && !empty($_SESSION['permissions']) && is_array($_SESSION['permissions']) ? true : false;
	if(!$id || !$user || !$password || !$email || !$permissions){
		if(isset($_COOKIE['baked']) && !empty($_COOKIE['baked'])){
			$baked = base64_decode($_COOKIE['baked']);
			$userarray = @unserialize($baked);
			$id = isset($userarray['id']) && !empty($userarray['id']) ? true : false;
			$user = isset($userarray['username']) && !empty($userarray['username']) ? true : false;
			$password = isset($userarray['password']) && !empty($userarray['password']) ? true : false;
			$email = isset($userarray['email']) && !empty($userarray['email']) ? true : false;
			$permissions = isset($userarray['permissions']) && !empty($userarray['permissions']) && is_array($userarray['permissions']) ? true : false;
		}
	}

	return ($id && $user && $password && $email) ? true : false;
}
function dateTimeProcess($created='') {
	if(isset($_POST['resettime'])){
		return time();
	}
	return (!empty($_POST['month']) &&
			!empty($_POST['day']) &&
			!empty($_POST['year']) &&
			!empty($_POST['hour']) &&
			!empty($_POST['minute']) &&
			!empty($_POST['second']))
			? mktime($_POST['hour'], $_POST['minute'], $_POST['second'], $_POST['month'], $_POST['day'], $_POST['year']) : (!empty($created) ? $created : time());
}

function checkEmail($email) {
	if(preg_match('/^([a-zA-Z0-9_\-])+(\.([a-zA-Z0-9_\-])+)*@((\[(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5]))\]))|((([a-zA-Z0-9])+(([\-])+([a-zA-Z0-9])+)*\.)+([a-zA-Z])+(([\-])+([a-zA-Z0-9])+)*))$/si', $email)) {
		list($Username, $Domain) = explode("@",$email);
		$MXHost = !isset($MXHost) ? array() : $MXHost;
		if(getmxrr($Domain, $MXHost)) {
			return true;
		} else {
			if(@fsockopen($Domain, 25, $errno, $errstr, 30)) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}

}
function valid_uri($url, $options = null) {
	$strict = ';/?:@$,';
	$domain_check = false;
	$allowed_schemes = null;
	if(is_array($options)) {
		extract($options);
	}
	if(preg_match(
	'&^(?:([a-z][-+.a-z0-9]*):)?                             	# 1. scheme
	(?://                                                   	# authority start
	(?:((?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'();:\&=+$,])*)@)?    	# 2. authority-userinfo
	(?:((?:[a-z0-9](?:[-a-z0-9]*[a-z0-9])?\.)*[a-z](?:[a-z0-9]+)?\.?)  # 3. authority-hostname OR
	|([0-9]{1,3}(?:\.[0-9]{1,3}){3}))                       	# 4. authority-ipv4
	(?::([0-9]*))?)                                        		# 5. authority-port
	((?:/(?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'():@\&=+$,;])*)*/?)? 	# 6. path
	(?:\?([^#]*))?                                          	# 7. query
	(?:\#((?:%[0-9a-f]{2}|[-a-z0-9_.!~*\'();/?:@\&=+$,])*))? 	# 8. fragment
	$&xi', $url, $matches)) {
		$scheme = isset($matches[1]) ? $matches[1] : '';
		$authority = isset($matches[3]) ? $matches[3] : '' ;
		if (is_array($allowed_schemes) && !in_array($scheme,$allowed_schemes)) {
			return false;
		}
		if (!empty($matches[4])) {
			$parts = explode('.', $matches[4]);
			foreach ($parts as $part) {
				if ($part > 255) {
					return false;
				}
			}
		} elseif ($domain_check && function_exists('checkdnsrr')) {
			if (!checkdnsrr($authority, 'A')) {
				return false;
			}
		}
		if($strict) {
			$strict = '#[' . preg_quote($strict, '#') . ']#';
			if ((!empty($matches[7]) && preg_match($strict, $matches[7])) || (!empty($matches[8]) && preg_match($strict, $matches[8]))) {
				return false;
			}
		}
		return true;
	}
	return false;
}
//Fix for Windows users
if (!function_exists('getmxrr')) {
	function getmxrr($hostname, $mxhosts, $mxweight=array()) {
		if(!is_array($mxhosts)) {
			$mxhosts = array ();
		}
		if(!empty($hostname)) {
			$output = "";
			@exec ("nslookup.exe -type=MX $hostname.", $output);
			$imx=-1;
			foreach ($output as $line) {
				$imx++;
				$parts = "";
				if (preg_match ("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts) ) {
					$mxweight[$imx] = $parts[1];
					$mxhosts[$imx] = $parts[2];
				}
			}
			return ($imx!=-1);
		}
		return false;
	}
}
function delRecursive($dirname) {
	if(is_dir($dirname)) {
		$dir_handle = opendir($dirname);
	}
	while($file=readdir($dir_handle)) {
		if($file!="." && $file!="..") {
			if(!is_dir($dirname."/".$file)){
				if (!unlink ($dirname."/".$file)){
					return false;
				}
			} else {
				delRecursive($dirname."/".$file);
			}
		}
	}
	closedir($dir_handle);
	if(!rmdir($dirname)) {
		return false;
	}
	return true;
}
function rm($dir) {
	if(!$dh = @opendir($dir)) { return; }
	while (($obj = readdir($dh))) {
		if($obj=='.' || $obj=='..') { continue; }
		if(!@unlink($dir.'/'.$obj)) { rm($dir.'/'.$obj); }
	}
	@rmdir($dir);
}
function getRemoteFile($url) {
	$remote = new Snoopy();
	$remote->agent = CMS_NAME.'/'.CMS_VERSION;
	$remote->read_timeout = 2;
	$remote->use_gzip = true;
	@$remote->fetch($url);
	return $remote;
}
function checkTrailingSlash($str) {
	return (substr($str, -1) != '/') ? $str.'/': $str;
}
/**
 * Aidan freaking rocks! - Expanse Team.
 * PS. Aidan, we added an $ignore param, to skip over the upgrade files, as well as alert for errors...
 --------------------------------------------------------------
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/repos/v/function.copyr.php
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       array    $ignore    Files to not copy
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function copyr($source, $dest, $ignore=array()) {
	global $errors;
	// Simple copy for a file
	if (is_file($source)) {
		if(in_array(basename($source),$ignore)) {
			return false;
		}
		if(!copy($source, $dest)) {
			$errors[] = $dest;
			return false;
		}
		return true;
	}
	// Make destination directory
	if(!is_dir($dest)) {
		mkdir($dest);
	}
	// Loop through the folder
	$dir = dir($source);
	while(false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		// Deep copy directories
		if ($dest !== "$source/$entry") {
			copyr("$source/$entry", "$dest/$entry", $ignore);
		}
	}
	// Clean up
	$dir->close();
	return true;
}
//Recursive CHMOD. Thanks haasje@welmers.net
function chmod_R($path, $filemode) {
	if (!is_dir($path)) {
		return chmod($path, $filemode);
	}
	$dh = opendir($path);
	while ($file = readdir($dh)) {
		if($file != '.' && $file != '..') {
			$fullpath = $path.'/'.$file;
			if(!is_dir($fullpath)) {
				if (!chmod($fullpath, $filemode)) {
					return FALSE;
				}
			} else {
				if(!chmod_R($fullpath, $filemode)) {
					return FALSE;
				}
			}
		}
	}
	closedir($dh);
	if(chmod($path, $filemode)) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function is_unsafe() {
	return (substr(sprintf('%o', fileperms('./')), -3) > 755);
}
function language_info($lang_file) {
	$lang_info = file_exists($lang_file) ? file_get_contents($lang_file) : '';
	preg_match('|Language:(.*)|i', $lang_info, $lang_name);
	preg_match('|Translated by:(.*)|i', $lang_info, $lang_translator);
	$name = isset($lang_name[1]) ? trim($lang_name[1]) : '';
	$translator =  isset($lang_translator[1]) ? trim($lang_translator[1]) : '';
	//Huh? Because I like object notation, that's why!
	return (object) array('Language' => $name, 'Translator' => $translator);
}
function theme_info($theme_file) {
	$theme_info = file_exists($theme_file) && is_readable($theme_file) ? file_get_contents($theme_file) : '';
	preg_match('|Theme Name:(.*)|i', $theme_info, $theme_name);
	preg_match('|Theme URL:(.*)|i', $theme_info, $theme_url);
	preg_match('|Description:(.*)|i', $theme_info, $description);
	preg_match('|Author:(.*)|i', $theme_info, $author_name);
	preg_match('|Author URL:(.*)|i', $theme_info, $author_url);
	$version = (preg_match('|Version:(.*)|i', $theme_info, $version)) ? $version[1] : '';
	$name = $theme = isset($theme_name[1]) ? trim($theme_name[1]) : '';
	$authors =  isset($author_name[1]) ? explode(',',$author_name[1]) : array();
	$author_urls = isset($author_url[1]) ?  explode(',',$author_url[1]) : array();
	if(count($authors) > 1){
		$theme_authors = array();
		foreach($authors as $i => $v){
			$theme_authors[] = (isset($author_urls[$i]) && !empty($author_urls[$i])) ? '<a href="'.$author_urls[$i].'" title="'.L_THEME_VISIT_AUTHOR.'" target="_blank">'. $v.'</a>' : $v;
		}
		$author = proper_list($theme_authors);
	} else {
		$author_name = isset($author_name[1]) ? $author_name[1] : '';
		$author = !isset($author_url[1]) || empty($author_url[1]) ? $author_name : '<a href="'.$author_url[1].'" title="'.L_THEME_VISIT_AUTHOR.'" target="_blank">'. $author_name.'</a>';
	}
	//Huh? Because I like object notation, that's why!
	return (object) array('Name' => $name, 'Title' => $theme, 'Description' => (isset($description[1]) ? $description[1] : ''), 'Author' => $author, 'Version' => $version);
}
function has_theme_info($info){
	return (!empty($info->Name) || !empty($info->Title) || !empty($info->Description) || !empty($info->Author) || !empty($info->Version));
}
function proper_list($array, $andor = 'and', $oxfordComma=false) {
	if (!is_array($array)) {
		return $array;
	}
	if (count($array) <= 1) {
		return implode(", ", $array);
	}
	$last = array_pop($array);
	return implode(", ", $array) . ($oxfordComma ? "," : "") . " $andor " . $last;
}

function paginate($table_name, $specifics='', $howmany=10, $return_info = false) {
	if($howmany == 0){ return $table_name; }
	global $Database;
	$base_url = preg_replace('/(&|&amp;)page=([0-9]+)/', '', basename($_SERVER['REQUEST_URI']));
	$paginate = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$chowmany = check_get_id('sort_howmany');
	if(!empty($chowmany) && $chowmany != 0) { $howmany = $chowmany; }
	$limitvalue = $paginate * $howmany - ($howmany);
	$table = new Expanse($table_name);
	if(is_string($table_name)) {
		$all_items = $table->GetList(array(array('id','>',0)), 'id', false, "$limitvalue, $howmany");
		$Database->Query("SELECT COUNT(*) as item_count FROM ".PREFIX."$table_name $specifics");
		$itemcount = $Database->Result(0, 'item_count');
	} elseif(is_array($table_name)) {
		$all_items =& $table_name;
		if(SORT_BY_SUBCATS !== false && SORT_BY_SUBCATS != 0) {
			foreach($all_items as $k => $v) {
				if($v->cid != SORT_BY_SUBCATS) {
					unset($all_items[$k]);
					continue;
				}
			}
		}
		$itemcount = count($all_items);//recount these items
		$all_items = array_slice($all_items, $limitvalue, $howmany);
	}
	$pagecount = ceil($itemcount / $howmany);
	$previous_link = '';
	$next_link = '';
	$page_link = array();
	for ($i = 1; $i <= $pagecount; $i++) {
		$pagenumber = $i;
		$currentpage = $paginate;
		$previouspage = $paginate - 1;
		$nextpage = $paginate + 1;
		$pagecount = $pagecount;
		//Previous link
		$previous_link_url = $base_url;
		if ($previouspage > 0 && $i == 1) {
			$previous_link_url .= '&amp;page=' . $previouspage;
			$previous_link = '<li><a href="' . $previous_link_url .'">'.L_PAGING_PREVIOUS.'</a></li';
		}
		//Next link
		$next_link_url = $base_url;
		if ($nextpage <= $pagecount && $i == $pagecount) {
			$next_link_url .= empty($subcat) ? '' : "&amp;subcat=$subcat";
			$next_link_url .= '&amp;page=' . $nextpage;
			$next_link = '<li><a href="' . $next_link_url . '">'.L_PAGING_NEXT.'</a></li>';
		}
		//Pages links
		$page_link_url = $base_url;
		$page_link_url .= '&amp;page=' . $i;
		$page_link[] = ($paginate != $i) ? '<li><a href="' . $page_link_url . '">' . $i . '</a></li>' : '<li class="active"><a href="#">'. $i. '</a></li>';
	}
	ob_start();
	if($itemcount > 0) {
		echo '<div id="pageList" class="pagination">';
		echo '<ul>';
		//echo "".L_PAGING_PAGES." $previous_link ";
		echo "$previous_link";
		foreach($page_link as $val) { echo " $val "; }
		echo "$next_link";
		echo '</ul>';
		echo '</div>';
	} else {
		printf(FAILURE, vsprintf(L_NO_ENTRIES, CAT_ID));
	}
	$pageList = ob_get_contents();
	ob_end_clean();
	if(!$return_info) {
		echo $pageList;
		return $all_items;
	}
	return array($all_items, $pageList);
}
function insert_between($filename, $marker, $insertion) {
	//Check to make sure the file is either createable or writeable
	if(file_exists($filename) && !is_writable($filename)){return false;}
	$start_mark = "# //-- Start $marker";
	$end_mark = "# //-- End $marker";
	$markerdata = (!file_exists($filename)) ? false : explode("\n", file_get_contents($filename));
	$f = fopen($filename, 'w');
	$found_mark = false;
	if ($markerdata) {
		$user_line = true;
		foreach ($markerdata as $markerline) {
			$is_start = strstr($markerline, $start_mark);
			$is_end  = strstr($markerline, $end_mark);
			if($is_start){$user_line = false;} //ignore lines between mark and starting marker
			if ($user_line) { // write the users lines
				fwrite($f, "{$markerline}\n");
				continue;
			}
			if($is_end) { //we're at the end of the marker, now write the lines start to finish
				fwrite($f, "{$start_mark}\n");
				if(is_array($insertion)) {
					foreach ($insertion as $insertline){
						fwrite($f, "{$insertline}\n");
					}
				} else {
					fwrite($f, "{$insertline}\n");
				}
				fwrite($f, "{$end_mark}\n");
				$user_line = true; //back to being a user line
				$found_mark = true; //and why, yes, we did find the marker
			}
		}
	}
	if(!$found_mark) { //couldn't find the marker, so lets write it
		fwrite($f, "{$start_mark}\n");
		foreach ($insertion as $custom){
			fwrite($f, "{$custom}\n");
		}
		fwrite($f, "{$end_mark}\n");
	}
	fclose($f);
	return true;
}
function unique_dirtitle($string, $table = 'items') {
	if($table == 'items') {
		global $items;
		$table = $items;
	} else{
		global ${$table};
		$table = ${$table};
	}
	$list = $table->GetList(array(array('dirtitle', '=', $string)));
	$clone = $string;
	if(!empty($list)) {
		$uniq = 2;
		$clone = $list[0]->dirtitle;
		while($clone) {
			$try_uniq = "{$clone}-$uniq";
			$list_try =  $table->GetList(array(array('dirtitle', '=', $try_uniq)));
			if(!empty($list_try)) {
				$uniq++;
			} else {
				$clone = false;
			}
		}
		$clone = $try_uniq;
	}
	return $clone;
}
function set_dirtitle($obj, $xobj = 'items') {
	return (!empty($_POST['dir_title'])) ? ((trim($_POST['dir_title']) == $obj->dirtitle) ? $obj->dirtitle : unique_dirtitle(dirify(trim($_POST['dir_title'])), $xobj)) : (!empty($obj->title) ? unique_dirtitle(dirify($obj->title), $xobj) : $obj->dirtitle);
}
function mod_rewrite() {
	if(!_APACHE){return false;}
	if(function_exists('apache_get_modules')){
		if(!in_array('mod_rewrite', apache_get_modules())){return false;}
	}
	return true;
}
/*
------------------------------------------------------------
checks to make sure the passed index is in the GET array,
 and that it's an int. If not, passes back an empty string.
============================================================
*/
function check_get_id($id) {
	return (isset($_GET[$id]) && ctype_digit($_GET[$id])) ? (int) $_GET[$id] : '';
}
function check_get_alphanum($id, $allowable_chars = '') {
	return (isset($_GET[$id]) && !preg_match('[^A-Za-z0-9_-'.$allowable_chars.']', $id)) ? trim($_GET[$id]) : '';
}
function check_array($arr) {
	return (isset($arr) && is_array($arr)) ? $arr : array();
}
function &get_dao($dao, $clone=true) {
	global ${$dao};
	$the_object = ${$dao};
	if((isset($the_object) && is_object($the_object))){
		if($clone){
			$new_object = clone($the_object);
			return $new_object;
		}
		$new_object = $the_object;
	} else {
		$new_object = new Expanse($dao);
	}
	return $new_object;
}
class Module {
	//Default class vars
	var $name = '';
	var $description = '';
	var $authorURL = 'http://expansecms.org';
	var $author = array(
		'Ian Tearle' => 'http://iantearle.com',
		'Nate Cavanaugh' => 'http://alterform.com',
		'Jason Morrison' => 'http://dubtastic.com'
	);
	var $version = '2.0';
	var $modURL = 'http://expansecms.org';
	//You can use this to exclude from the add category list.
	var $Exclude = false;
	//An array of language settings
	var $LEX = array();
	function Module() {
		$this->Database = $GLOBALS['Database']; // Global db connection
		$this->output = $GLOBALS['outmess']; // Global output handler
		$this->auth = $GLOBALS['auth']; // Global authentication object
		// active-record object for the items table
		$this->items = get_dao('items');
		// active-record object for the customfields table
		$this->custom = get_dao('customfields');
		// active-record object for the sections table
		$this->sections = get_dao('sections');
		$this->cat_id = CAT_ID; // Look for valid category id
		$this->item_id = check_get_id('id'); // Look for valid item id
		$this->itemsList = array();
		$this->cats = isset($GLOBALS['cats']) && is_object($GLOBALS['cats']) ? $GLOBALS['cats'] : getCatList(CAT_ID);
		$this->category_action = !empty($_POST['category_action']) ? $_POST['category_action']: false;
		$this->new_home = !empty($_POST['new_home']) ? (int) $_POST['new_home']: 0;
		$this->add_subcat = isset($_POST['add_subcat']) ? trim(strip_tags($_POST['add_subcat'])) : '';
		$this->new_item = null;
		$this->errors = array();
		if(!empty($this->item_id)){
			$this->items->Get($this->item_id);
		}
		//$this->load_language();
	}
	function add_title() {
		$sections = $this->sections;
		$sections->Get($this->cat_id);
		$page_title = '';
		$proper_title = $sections->sectionname;
		if(ADDING) {
			$page_title = L_ADD_ITEM_TITLE;
		} elseif(EDITING) {
			if(EDIT_LIST) {
				$page_title =  L_EDIT_ITEM_TITLE;
			} elseif(EDIT_SINGLE) {
				$items = $this->items;
				if(!empty($items->id)){
					$proper_title = (!empty($items->title)) ? $items->title : L_NO_TEXT_IN_TITLE;
					$page_title = L_CURRENTLY_EDITING_PLAIN;
					add_title(sprintf(L_CURRENTLY_EDITING_PLAIN,$sections->sectionname), 2);
				}
			}
		}
		add_title(sprintf($page_title,$proper_title), 2);
	}
	/*      //-------------------------------*/
	function add() {
		//Declare method vars
		$items = $this->items;
		$cat_id = $this->cat_id;
		$item_id = $this->item_id;
		$itemvars = get_object_vars($items);
		$output = $this->output;
		//Loop over post
		foreach($_POST as $ind=>$val) {
			if(isset($itemvars[$ind])) {
				if(is_array($val)) {
					foreach($val as $k => $v) {
						$val[$k] = trim($v);
						if(empty($v)){
							unset($val[$k]);
						}
					}
					$items->{$ind} = !empty($val) ? serialize($val) : '';
				} else {
					$items->{$ind} = trim($val);
				}
			}
		}
		$items->created = dateTimeProcess();
		$items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $cat_id;
		$items->dirtitle = (!empty($_POST['title'])) ? unique_dirtitle(dirify($_POST['title'])) : unique_dirtitle('untitled');
		//Add a subcat
		$items->cid = $this->addSubcat();
		if($items->SaveNew()) {
			$items = applyOzoneAction('item_add', $items);
			$this->manage_custom_fields($items);
			//Move or copy
			$new_item =& $this->new_item;
			$new_home =& $this->new_home;
			$this->moveOrCopy($items);
			$output->printOut(SUCCESS,vsprintf(L_ADD_SUCCESS, array($items->title, $cat_id, $items->id)));
		} else {
			$output->printOut(FAILURE, vsprintf(L_ADD_FAILURE,array($items->title, mysql_error())));
		}
	}
	/*      //-------------------------------*/
	function edit() {
		//Declare method vars
		$items = $this->items;
		$cat_id = $this->cat_id;
		$item_id = $this->item_id;
		$itemvars = get_object_vars($items);
		$output = $this->output;
		//Loop over post
		foreach($_POST as $ind=>$val) {
			if(isset($itemvars[$ind])){
				$items->{$ind} = $val;
			}
		}
		$items->created = dateTimeProcess($items->created);
		$items->pid = (isset($_POST['pid'])) ? $_POST['pid'] : $cat_id;
		$items->dirtitle = set_dirtitle($items);
		//Add a subcat
		$items->cid = $this->addSubcat();
		if($items->Save()) {
			$items = applyOzoneAction('item_edit', $items);
			$this->manage_custom_fields($items);
			//Item was moved or copied
			if(!$this->moveOrCopy($items)){
				$output->printOut(SUCCESS,vsprintf(L_EDIT_SUCCESS, array($items->title, $cat_id, $items->id)));
			} else {
				$output->printOut(SUCCESS,vsprintf(L_EDIT_MOVE_SUCCESS, array($this->new_item->title, $this->new_home, $this->new_item->id)));
			}
		} else {
			printOut(FAILURE, vsprintf(L_EDIT_FAILURE, array($items->title, mysql_error())));
		}
	}
	/*      //-------------------------------*/
	function delete() {
		if(isset($_POST['del']) && !empty($_POST['del'])) {
			$upfolder = UPLOADS;
			$delete = (isset($_POST['del'])) ? $_POST['del'] : array();
			$items =& $this->items;
			$cat_id = $this->cat_id;
			$custom =& $this->custom;
			$comments = new Expanse('comments');
			$images = new Expanse('images');
			$fileList = array();
			foreach($delete as $val) {
				$delTitle = $items->Get($val);
				if(!empty($items->id)) {
					$delImg = !empty($items->image) ? $upfolder.'/'.$items->image : '';
					$delThmb = !empty($items->thumbnail) ? $upfolder.'/'.$items->thumbnail : '';
					$delTitle = (!empty($delTitle->title)) ? $delTitle->title : 'No title';
					if($items->Delete($val)) {
						if(!empty($delImg) && file_exists($delImg)) {
							unlink($delImg);
						}
						if(!empty($delThmb) && file_exists($delThmb)) {
							unlink($delThmb);
						}
						$itemcomments = $comments->GetList(array(array('cid', '=', $cat_id), array('itemid', '=', $val)));
						$customfields = $custom->GetList(array(array('itemid', '=', $val)));
						$xtraimages = $images->GetList(array(array('itemid', '=', $val)));
						if(!empty($itemcomments)) {
							foreach($itemcomments as $v){
								$comments->Delete($v->id);
							}
						}
						if(!empty($customfields)) {
							foreach($customfields as $v){
								$custom->Delete($v->id);
							}
						}
						if(!empty($xtraimages)) {
							foreach($xtraimages as $img) {
								$delXtraImg = !empty($img->image) ? $upfolder.'/'.$img->image : '';
								if(!empty($delXtraImg) && file_exists($delXtraImg)){
									unlink($delXtraImg);
								}
								$images->Delete($img->id);
							}
						}
						$deleted = true;
						$weredeleted[] = "<li><strong>$delTitle</strong></li>";
					} else {
						$deleted = false;
						$notdeleted[] = "<li><strong>$delTitle</strong></li>";
					}
				} else {
					$invalidid = true;
				}
			}
			if(isset($deleted) && $deleted == true) {
				$weredeleted = implode("\n",$weredeleted);
				printOut(SUCCESS, sprintf(L_DELETE_SUCCESS, $weredeleted));
			} else {
				$error = !isset($invalidid) ? mysql_error() : (count($delete) <> 1) ? L_ERR_ITEM_MISSING_PLURAL : L_ERR_ITEM_MISSING;
				$notdeleted = isset($notdeleted) ? $notdeleted : array('<li><strong>'.L_ERR_ITEM_NOT_FOUND.'</strong></li>');
				$notdeleted = implode("\n",$notdeleted);
				printOut(FAILURE, vsprintf(L_DELETE_FAILURE, array($notdeleted, $error)));
			}
		}
	}
	/*      //-------------------------------*/
	function more() {

	}
	/*      //-------------------------------*/
	function get_single() {
		$items =& $this->items;
		$cat_id = $this->cat_id;
		$item_id = $this->item_id;
		$items->Get($item_id);
		if(empty($items->id) || (!empty($items->id) && $items->pid != $cat_id)){
			printOut(FAILURE,sprintf(L_ENTRY_NOT_FOUND, $cat_id));
			$this->errors[] = 1;
		}
		return ozone_walk($this->items, 'admin_item_');
	}
	/*      //-------------------------------*/
	function get_list() {
		$items = $this->items;
		$cat_id = $this->cat_id;
		$item_id = $this->item_id;
		$auth = $this->auth;
		/*--*/
		$do_sort = check_get_alphanum('do_sort');
		$sortoption = getOption('sortcats');
		$ascending = getOption('sortdirection') == 'ASC' || $sortoption == 'order_rank' ? true : false;
		$conditions = array(array('pid', '=', $cat_id));
		if($do_sort == 'yes') {
			$sort_orderdir = check_get_alphanum('sort_orderdir') == 'ASC' ? 'ASC' : 'DESC';
			$sort_subcat = check_get_id('sort_subcat');
			$sort_orderby = check_get_alphanum('sort_orderby');
			$sortoption = !empty($sort_orderby) ? $sort_orderby: $sortoption;
			$ascending = $sort_orderdir;
		}
		if(!($auth->SectionAdmin || $auth->Admin)) {
			$conditions[] = array('aid', '=', $auth->Id);
		}
		$this->itemsList = $items->GetList($conditions, $sortoption, $ascending);
		if(empty($this->itemsList)) {
			printOut(FAILURE,sprintf(L_NO_ENTRIES,$cat_id));
			$this->errors[] = 1;
		}
		return $this->itemsList;
	}
	/*		//-------------------------------*/
	function addSubcat($new_subcat = '') {
		$items = $this->items;
		$cat_id = $this->cat_id;
		$new_home = $this->new_home;
		$category_action = $this->category_action;
		$add_subcat = $this->add_subcat;
		if(!empty($add_subcat)) {
			$subcats = $this->sections;
			$add_subcat = empty($new_subcat) ? strip_tags($_POST['add_subcat']) : $new_subcat;
			$cat_list = $subcats->GetList(array(array('sectionname', '=', $add_subcat), array('pid','=',$cat_id)));
			if(empty($cat_list)) {
				$subcats->sectionname = $add_subcat;
				$subcats->dirtitle = dirify($add_subcat);
				$subcats->pid = $cat_id;
				$subcats->public = 0;
				$subcats->SaveNew();
				$subcats = applyOzoneAction('add_custom_field', $subcats);
				$cats = getCatList($cat_id);
				$cats->subcats[] = array('catname'=>$subcats->sectionname,'id'=>$subcats->id);
				$items->cid = $subcats->id;
			} else{
				$items->cid = $cat_list[0]->id;
			}
			return $items->cid;
		} elseif(isset($_POST['cid']) && $_POST['cid'] != $cat_id && ctype_digit($_POST['cid'])) {
			return $_POST['cid'];
		}
	}
	function manage_custom_fields(&$item) {
		$Database =& $this->Database;
		$custom =& $this->custom;
		if(EDITING) { //reset the custom fields if we're editing
			$Database->Query("DELETE FROM ".PREFIX."customfields WHERE itemid={$item->id}");
		}
		$custom_array = isset($_POST['custom']) ? $_POST['custom'] : array();
		foreach($custom_array as $i=>$v) {
			$custom_label = trim(strip_tags($v['label']));
			$custom_value = trim($v['value']);
			if($custom_label == L_JS_CUSTOM_LABEL_TEXT || $custom_value == L_JS_CUSTOM_LABEL_TEXT || empty($custom_label) || empty($custom_value)) {
				continue;
			}
			$custom->field = $custom_label;
			$custom->value = $custom_value;
			$custom->itemid = $item->id;
			$custom->SaveNew();
			$custom = applyOzoneAction('add_custom_field', $custom);
		}
	}
	/*		//-------------------------------*/
	function moveOrCopy($items) {
		$new_item = null;
		$new_home = $this->new_home;
		//$items =& $this->items;
		$category_action = $this->category_action;
		$auth = $this->auth;
		$is_moved = false;
		if($category_action) {
			//check for permissions
			if(in_array($new_home, $auth->Permissions)) {
				if($category_action == 'move') {
					$new_item =& $items;
					$new_item->pid = $new_home;
					$new_item->cid = $new_home;
					$new_item->aid = $auth->Id;
					$new_item->Save();
					$new_item = applyOzoneAction('item_move', $new_item);
					$is_moved = true;
				} elseif($category_action == 'copy') {
					$new_item = clone($items);
					$new_item->pid = $new_home;
					$new_item->cid = $new_home;
					$new_item->aid = $auth->Id;
					$new_item->SaveNew();
					$new_item = applyOzoneAction('item_copy', $new_item);
				}
			}
		}
		$this->new_item = $new_item;
		return $is_moved;
	}
	/*      //-------------------------------*/
	function custom_fields() {
		$items =& $this->items;
		$custom =& $this->custom;
		?>
		<fieldset id="customGroup">
			<legend><?php echo L_CUSTOM_FIELDS_TITLE; tooltip(L_CUSTOM_FIELDS_TITLE,L_CUSTOM_FIELDS_HELP); ?></legend>
		<?php
		$itemFields = (EDITING) ? $custom->GetList(array(array('itemid', '=', $items->id))) : array();
		if(!empty($itemFields)) {
			foreach($itemFields as $key => $ifield) {
				$k = $key+1;
				$custom_var = '{custom_var'.$k.'}';
				?>
				<div id="customLabel<?php echo $k ?>Group" class="row customLabelGroup">
					<div class="span8">
						<div class="clearfix">
							<div class="input">
								<input name="custom[<?php echo $k ?>][label]" id="customLabel<?php echo $k; ?>" type="text" class="span6 fieldLabel text" value="<?php echo view($ifield->field); ?>" autocomplete="off" />
							</div>
						</div>
						<div class="clearfix">
							<div class="input">
								<textarea id="customValue<?php echo $k ?>" name="custom[<?php echo $k ?>][value]" class="span6 fieldValue"><?php echo $ifield->value ?></textarea>
							</div>
						</div>
						<div class="clearfix">
							<label for="customVar<?php echo $k ?>"><?php echo L_JS_CUSTOM_VARIABLE_TEXT ?></label>
							<div class="input">
								<input type="text" value="<?php echo view($custom_var); ?>" class="shareField variableField uneditable-input" id="customVar<?php echo $k ?>">
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		} else {	?>
			<div id="customLabel1Group" class="row customLabelGroup">
				<div class="span8">
					<div class="clearfix">
						<div class="input">
							<input type="text" class="span6 fieldLabel text" id="customLabel1" name="custom[1][label]" autocomplete="off" value="<?php echo L_JS_CUSTOM_LABEL_TEXT ?>" />
						</div>
					</div>
					<div class="clearfix">
						<div class="input">
							<textarea id="customValue1" name="custom[1][value]" class="span6 fieldValue"><?php echo L_JS_CUSTOM_FIELD_TEXT ?></textarea>
						</div>
					</div>
					<div class="clearfix">
						<label for="customVar1" id="labelcustomVar1"><?php echo L_JS_CUSTOM_VARIABLE_TEXT ?></label>
						<div class="input">
							<input type="text" class="shareField variableField uneditable-input" id="customVar1">
						</div>
					</div>
				</div>
			</div><?php
		}
		?>
		</fieldset>
		<input type="hidden" value="<?php echo view(get_custom_fields()); ?>" id="customList"  />
		<div id="autosuggest"><ul></ul></div>
		<?php
	}
	//"do" methods
	function doCategories($mode='') {
		$cat_id = $this->cat_id;
		$cats = getCatList($cat_id);
		$items = $this->items;
		$items->Get($this->item_id); // Make sure we have the freshest data
		$sections = $this->sections;
		$auth = $this->auth;
		$more_cats = $sections->GetList(array(array('pid', '=', 0)));
		foreach($more_cats as $k => $v) {
			if(!in_array($v->id, $auth->Permissions) || ($v->id == $cat_id)){
				unset($more_cats[$k]);
			}
		}
		?>
		<div class="row">
		<div class="span7">
		<div class="clearfix">
			<label for="cid"><?php echo L_SUB_CATEGORY ?></label>
			<div class="input">
				<select class="" name="cid" id="cid">
					<option value="<?php echo $cat_id ?>"><?php echo L_SUB_CATEGORY_SELECT ?></option>
					<?php
					foreach($cats->subcats as $v){
					?>
					<option value="<?php echo $v['id'] ?>"<?php echo $items->cid == $v['id'] ? ' selected="selected"' : ''; ?>>&mdash;<?php echo $v['catname'] ?></option>
					<?php
					}
					?>
				</select>
			</div>
		</div>
			<?php if(empty($mode)) { ?>
		<div class="clearfix">
			<label for="add_subcat"><?php echo L_SUB_CATEGORY_ADD ?></label>
			<div class="input">
				<input name="add_subcat" id="add_subcat" type="text" />
			</div>
		</div>
		</div>
		<div class="span7">
		<div class="clearfix">
			<label for="category_action"><?php echo L_CATEGORY_ACTION ?></label>
			<div class="input">
				<select name="category_action" id="category_action">
					<option value="" selected="selected"><?php echo L_MOVE_OR_COPY ?></option>
					<option value="move"><?php echo L_MOVE_TO ?></option>
					<option value="copy"><?php echo L_COPY_TO ?></option>
				</select>
			</div>
		</div>
		<div class="clearfix">
			<label for="new_home">Move into</label>
			<div class="input">
				<select name="new_home" id="new_home">
				<?php
				foreach($more_cats as $other_cat) {
					if($other_cat == $cat_id || $other_cat->cat_type == 'pages'){continue;}
					?>
					<option value="<?php echo $other_cat->id ?>"><?php echo $other_cat->sectionname ?></option>
					<?php

				}?>
				</select>
			</div>
		</div>
		</div>
		</div>
		<?php } ?>
		<?php
	}
	/*---------*/
	function doSharing() {
		$items = $this->items;
		$sections = $this->sections;
		$expanseurl = EXPANSE_URL;
		$yoursite = YOUR_SITE;
		if(CLEAN_URLS) {
			$sections->Get($items->pid);
			$section_id = $sections->dirtitle;
			$the_item_id = $items->dirtitle;
		} else {
			$section_id = $sections->id;
			$the_item_id = $items->id;
		}
		$dynamic_url = $yoursite.((CLEAN_URLS) ? "$section_id/$the_item_id" : INDEX_PAGE."?pcat=$section_id&amp;item=$the_item_id");
		$static_url = $yoursite.((CLEAN_URLS) ? $the_item_id : INDEX_PAGE."?ucat=$items->id");
		$page_link = ($items->type !== 'static') ? $dynamic_url : $static_url; ?>
		<div class="clearfix">
			<label for="pageLink"><?php echo L_SHARING_DIRECT_LINK ?></label>
			<div class="input">
				<input type="text" class="span8 shareField" id="pageLink" value="<?php echo $page_link; ?>" />
			</div>
		</div> <?php
		if(!empty($items->image)) { ?>
			<div class="clearfix">
				<label for="imageLink"><?php echo L_SHARING_IMAGE_LINK ?></label>
				<div class="input">
					<input type="text" class="span8 shareField" id="imageLink" value="<?php echo $expanseurl; ?>uploads/<?php echo $items->image; ?>" />
				</div>
			</div>	 <?php
			$thumbpath = $items->autothumb == 1 ? $expanseurl.'funcs/tn.lib.php?id='.$items->id.'&amp;thumb=1': $expanseurl.'uploads/'.$items->thumbnail;
			if($items->autothumb == 1 || !empty($items->thumbnail)) { ?>
				<div class="clearfix">
					<label for="thumbLink"><?php echo L_SHARING_THUMB_LINK ?></label>
					<div class="input">
						<input type="text" class="span8 shareField" id="thumbLink" value="<?php echo $thumbpath; ?>" />
					</div>
				</div> <?php
			}
		}
	}
	/*---------*/
	function doCleanURLTitles() {
		if(!CLEAN_URLS){return;}
		$items = $this->items; ?>
		<div class="clearfix">
			<label for="dirtitle"><?php echo L_CLEAN_URL_TITLE ?></label>
			<div class="input">
				<input type="text" name="dir_title" id="dirtitle" value="<?php echo $items->dirtitle; ?>" class="span8" />
				<span class="help-block"><?php echo L_CLEAN_URL_HELP ?></span>
			</div>
		</div>
		<?php
	}
	/*---------*/
	function doDateTimeForms() {
		global $months;
		$items =& $this->items;
		$adding = empty($items->created) || is_posting(L_BUTTON_ADD);
		$timestamp = ($adding) ? time() : $items->created;
		?>
		<div class="clearfix">
		<label for="month"><?php echo L_TIME_DATE ?></label>
			<div class="input">
				<select title="<?php echo L_TIME_MONTH ?>" class="infields" name="month" id="month">
					<option value="current"><?php echo L_TIME_MONTH ?></option>
					<?php
					foreach($months as $i => $v){
						?>
						<option value="<?php echo $i ?>"<?php echo (date('m', $timestamp) == $i) ? ' selected="selected"': '' ?>><?php echo $v ?></option>
						<?php
					}
					?>
				</select>
				 &nbsp; <input title="<?php echo L_TIME_DAY ?>" name="day" type="text" class="infields" id="day" value="<?php echo date('d', $timestamp); ?>" size="2" /> , <input title="<?php echo L_TIME_YEAR ?>" name="year" type="text" class="infields" id="year" value="<?php echo date('Y', $timestamp); ?>" size="4" />
			</div>
		</div>
		<div class="clearfix">
		<label for="hour"><?php echo L_TIME_TIME ?></label>
			<div class="input">
				<select title="<?php echo L_TIME_HOUR ?>" class="infields" name="hour" id="hour">
					<?php
					foreach(range(0,23) as $hour) {
						$nice_hour = $hour+1;
						$nice_hour = ($hour > 12) ? ($hour-12)." p.m." : ((($hour != 0) ? $hour : 12).' a.m.');
						$hour = ($hour < 10) ? "0$hour" : $hour;
						?>
						<option value="<?php echo $hour ?>"<?php echo (date('H', $timestamp) == $hour) ? ' selected="selected"': '' ?>><?php echo $nice_hour ?></option>
						<?php
					}
					?>
				</select>
				: <input title="<?php echo L_TIME_MINUTE ?>" name="minute" type="text" class="infields" id="minute" value="<?php echo date('i', $timestamp) ?>" size="2" /> : <input title="<?php echo L_TIME_SECOND ?>" name="second" type="text" class="infields" id="second" value="<?php echo date('s', $timestamp) ?>" size="2" />
			</div>
		</div>
		<label for="resettime"><?php echo (!$adding) ? L_TIME_RESET : L_TIME_USE_CURRENT; ?></label><input type="checkbox" name="resettime" id="resettime" value="1" class="cBox"<?php echo ($adding) ? ' checked="checked"': ''; ?> />
		<blockquote class="helpContents" id="editDateHelp">
		<?php if(!empty($items->created)) { ?>
			<h5><?php echo L_POST_TIME_EDIT ?></h5><?php echo L_TIME_HELP_EDIT ?><?php
			echo userDate($items->created); ?>
		<?php } else { ?>
			<h5><?php echo L_POST_TIME_ADD ?></h5>
			<?php echo L_TIME_HELP_ADD ?>
		<?php } ?>
		</blockquote>
		<?php

	}
	function more_options($str, $method='', $id = '') {
		$args = null;
		if(is_array($str)){
			foreach($str as $k => $v){
				if(is_array($v)){
					$args = $v[1];
					$v = $v[0];
				}
				if(!is_callable(array($this,$v))) {
					continue;
				}
				$camelK = camelize($k); ?>
				<h3 class="stretchToggle" title="<?php echo $camelK; ?>"><span><?php echo $k ?></span></h3>
				<div class="stretch" id="<?php echo $camelK; ?>Container">
					<?php $this->$v($args); ?>
				</div> <?php
			}
			return;
		}
		if(is_array($method)) {
			$args = $method[1];
			$method = $method[0];
		}
		if(!is_callable(array($this,$method))){ return;}
		$idK = (empty($id) ? camelize($str) : $id);
		?>
		<h3 class="stretchToggle" title="<?php echo $idK;  ?>"><span><?php echo $str ?></span></h3>
			<div class="stretch" id="<?php echo $idK;  ?>">
			<?php $this->$method($args); ?>
		</div> <?php
	}
	function doSort() {
		$cats = $this->cats;
		$how_many = array(5,10,20,50,100,200);
		$sort_howmany = check_get_id('sort_howmany');
		$is_subcat = SORT_BY_SUBCATS !== CAT_ID && SORT_BY_SUBCATS !== false ? true : false;
		$category_link = '';
		$clean_subcat_name = $is_subcat ? $this->sections->Get(SORT_BY_SUBCATS) : '';
		if($is_subcat) {
			$subcat_name = $clean_subcat_name->sectionname;
		}
		$clean_subcat_name = is_object($clean_subcat_name) ? $clean_subcat_name->dirtitle : '';
		$category_link = (!$is_subcat) ? YOUR_SITE.(CLEAN_URLS ? CLEAN_CAT_NAME : INDEX_PAGE.'?pcat='.CAT_ID) : YOUR_SITE.(CLEAN_URLS ? CLEAN_CAT_NAME : INDEX_PAGE.'?pcat='.CAT_ID).(CLEAN_URLS ? '/'.$clean_subcat_name : '&amp;subcat='.SORT_BY_SUBCATS);
		?>
		<input type="hidden" name="cat_id" value="<?php echo CAT_ID; ?>" />
		<input type="hidden" name="type" value="edit" />
		<?php echo ($is_subcat) ? '<h3>'.L_SORT_VIEWING.' '.$subcat_name.'</h3>' : ''; ?>
		<div class="stretchContainer">
			<h3 class="stretchToggle" id="wedge"><span><?php echo $is_subcat ? L_SORT_SUBCATEGORY_DETAILS : L_SORT_CATEGORY_DETAILS ?></span></h3>
			<div class="stretch" id="categoryDetails">
				<div class="row">
					<div class="span7">
						<h3><?php echo L_SHARING_TITLE ?></h3>
						<div class="clearfix">
							<label for="direct_link"><?php echo $is_subcat ? L_SORT_DIRECT_LINK_SUBCATEGORY : L_SORT_DIRECT_LINK_CATEGORY ?></label>
							<div class="input">
								<input type="text" id="direct_link" value="<?php echo $category_link  ?>" class="span7 shareField" />
							</div>
						</div>
						<div class="clearfix">
							<label for="rss_feed"><?php echo L_RSS_FEED ?></label>
							<div class="input">
								<input type="text" id="rss_feed" value="<?php echo YOUR_SITE.'feed.php?feed=rss&amp;pcat='.CAT_ID.($is_subcat == true ? '&amp;subcat='.SORT_BY_SUBCATS : '') ?>" class="span7 shareField" />
							</div>
						</div>
						<div class="clearfix">
							<label for="atom_feed"><?php echo L_ATOM_FEED ?></label>
							<div class="input">
								<input type="text" id="atom_feed" value="<?php echo YOUR_SITE.'feed.php?feed=atom&amp;pcat='.CAT_ID.($is_subcat == true ? '&amp;subcat='.SORT_BY_SUBCATS : '') ?>" class="span7 shareField" />
							</div>
						</div>
					</div>
					<div class="span7 offset1">
						<h3><?php echo L_SORT_TITLE ?></h3>
						<div class="clearfix">
							<label for="sort_by_subcat"><?php echo L_SORT_VIEW ?></label>
							<div class="input">
								<select id="sort_by_subcat" name="sort_by_subcat" class="span7">
									<option value="0"><?php echo L_SORT_ALL_SUBCATEGORIES ?></option>
									<option value="<?php echo CAT_ID ?>">Uncategorized</option>
								<?php
								array_unshift($cats->subcats, array('catname' => 'Uncategorized', 'id' => CAT_ID));
								foreach($cats->subcats as $k => $subcat) {
									$selected = SORT_BY_SUBCATS != false && SORT_BY_SUBCATS == $subcat['id'] ? 'selected="selected"' : '';
									?><option value="<?php echo $subcat['id'] ?>" <?php echo $selected ?>><?php echo ($subcat['id'] != CAT_ID ? '&mdash;' : '').$subcat['catname'] ?></option><?php
								}
								?></select>
							</div>
						</div>
						<div class="clearfix">
							<label for="sort_howmany"><?php echo L_SORT_HOWMANY ?></label>
							<div class="input">
								<select name="sort_howmany" id="sort_howmany" class="span7">
								<?php
								foreach($how_many as $val) {
									echo '<option value="'.$val.'"'.($val == $sort_howmany ? 'selected="selected"' : '').'>'.$val.'</option>';
								}
								?>
								</select>
							</div>
						</div>
						<div class="clearfix">
							<label for="sort_orderby"><?php echo L_SORT_SORTBY ?></label>
							<div class="input">
								<select id="sort_orderby" name="sort_orderby" class="span7">
								<?php
								$items = $this->items;
								$sort_orderby = check_get_alphanum('sort_orderby');
								$sort_orderdir = check_get_alphanum('sort_orderdir');
								foreach($items->Fields as $ind => $val) {
									switch($val) {
										case 'id':
											$sorttag = L_SORT_BY_ID;
											break;
										case 'title':
											$sorttag = L_SORT_BY_TITLE;
											break;
										case 'aid':
											$sorttag = L_SORT_BY_USER;
											break;
										case 'created':
											$sorttag = L_SORT_BY_DATE;
											break;
										case 'order_rank':
											$sorttag = L_SORT_BY_USER_RANK;
											break;
										default:
											$sorttag = '';
											break;
									}
									$selected = ($val == $sort_orderby) ? ' selected="selected"' : '';
									echo !empty($sorttag) ? '<option value="'.$val.'"'.$selected.'>'.$sorttag.'</option>' : '';
								}
								?>
								</select>
							</div>
						</div>
						<div class="clearfix">
							<label for="sort_orderdir"><?php echo L_SORT_ORDER_DIRECTION ?></label>
							<div class="input">
								<select name="sort_orderdir" id="sort_orderdir" class="span7">
									<?php
									$order_dirs = array(L_SORT_BY_ASC => 'ASC', L_SORT_BY_DESC => 'DESC');
									foreach($order_dirs as $k => $v){
									echo '<option value="'.$v.'"'.($sort_orderdir == $v ? 'selected="selected"' : '').'>'.$k.'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="actions">
							<input type="hidden" name="do_sort" value="yes" />
							<input id="sort_submit" type="submit" value="<?php echo L_SORT_BUTTON ?>" class="btn" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	/*   Load custom language options   //-------------------------------*/
	function load_language() {
		global $LEX;
		if(isset($this->LEX[LANG]) && !empty($this->LEX[LANG])) {
			$LEX = array_merge($LEX, $this->LEX[LANG]);
		}
	}
}
function camelize($str) {
	$replace = str_replace(" ", "", ucwords(str_replace("_", " ", $str)));
	return strtolower(substr($replace,0,1)).substr($replace,1,strlen($replace));
}
function printOut($message, $more="") {
	global $output, $report;
	if(is_array($message)) {
		foreach($report as $level => $reports){
			$report[$level] = array_unique($report[$level]);
		}
		foreach($report['error'] as $val){
			$output .= '<p class="contenterror">'.$val.'</p>';
		}
		foreach($report['success'] as $val){
			$output .= '<p class="contentsuccess">'.$val.'</p>';
		}
		foreach($report['alert'] as $val){
			$output .= '<p class="contentalert">'.$val.'</p>';
		}
	} else {
		if(empty($more)){
			$output .= $message;
		} elseif(is_array($more)) {
			$output .= vsprintf($message, $more);
		} else {
			$output .= sprintf($message, $more);
		}
	}
}
function add_admin_menu($title='', $menu_links='', $id='', $description='') {
	global $admin_menu;
	$details['title'] = (!empty($title)) ? $title : '';
	$details['links'] = (!empty($menu_links)) ? (is_array($menu_links) ? implode(L_MENU_SEPARATOR, $menu_links) : $menu_links) : '';
	$details['id'] = (!empty($id)) ? $id : '';
	$details['description'] = (!empty($description)) ? $description : '';
	$admin_menu['details'][] = $details;
}
function create_custom_var($str) {
	return strtolower('custom_'.preg_replace('|[^A-Za-z0-9_]|i', '', str_replace(' ', '_', $str)));
}
function view($str) {
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}
function save($str) {
	return html_entity_decode($str, ENT_QUOTES);
}
function edit_link($id) {
	return EXPANSE_URL .'index.php?type=edit&amp;cat_id='.CAT_ID.'&amp;id='.$id;
}
function preview_link() {
	return '<div class="clearfix"><label>&nbsp;</label><div class="input"><a href="'.YOUR_SITE .'index.php?preview=true&amp;pcat='.CAT_ID.'&amp;item='.ITEM_ID.'" target="_blank" class="btn">'.L_PREVIEW_TEXT.'</a></div></div>';
}
function tooltip($title, $help_text) {
	$unique_id = 'tt_'.random_string();
	if(is_array($help_text) && isset($help_text[1])) {
		$help_text = is_array($help_text[1]) ? vsprintf($help_text[0],$help_text[1]) : sprintf($help_text[0],$help_text[1]);
	}
	?>
	<img src="<?php echo EXPANSE_URL ?>images/help.gif" alt="" width="16" height="16" class="hasHelp" id="<?php echo $unique_id ?>" />
    <blockquote class="helpContents" id="<?php echo $unique_id ?>Help">
		<h5><?php echo $title ?></h5>
		<?php echo $help_text ?><br />
    </blockquote>
	<?php
}
function helpBlock($help_text) {
	if(is_array($help_text) && isset($help_text[0])) {
		$help_text = is_array($help_text[1]) ? vsprintf($help_text[0],$help_text[1]) : sprintf($help_text[0],$help_text[1]);
	}
	?> <span class="help-block"><?php echo $help_text ?></span> <?php
}
function random_string($length = 6) {
	return substr(md5(uniqid(microtime())), 0, $length);
}
function is_posting($value, $button='submit') {
	return (isset($_POST[$button]) && ($_POST[$button] == $value || $_POST[$button] == strtolower($value)));
}
function create_admin_menu_block($title, $content) {
	$unique_id = 'admin_'.random_string();
	?>
	<h3 class="stretchToggle" title="<?php echo $unique_id ?>"> <a href="#<?php echo $unique_id ?>"><span><?php echo $title ?></span></a></h3>
		<div class="stretch" id="<?php echo $unique_id ?>">
	<?php echo $content ?>
	</div> <?php
}
/**
 * Returns all the plugin files in the plugins folder
 * @return array $plugins
 */
function get_plugins() {
	global $plugins;
	if (isset ($plugins)) {
		return $plugins;
	}
	$plugins = $plugin_files = array();
	$plugins_folder = PLUGINS.'/';
	$files = getFiles($plugins_folder, 'all', true);
	foreach($files['dirs'] as $folder => $dir) {
		foreach($dir['files'] as $file) {
			if (!preg_match('|\.php$|', $file)){continue;}
			$plugin_files[] = "$folder/$file";
		}

	}
	foreach($files['files'] as $file) {
		if (!preg_match('|\.php$|', $file)){continue;}
		$plugin_files[] = $file;
	}
	if (empty($plugin_files)) {
		return $plugins;
	}
	foreach ($plugin_files as $plugin_file) {
		$plugin_file = "{$plugins_folder}$plugin_file";
		if (!is_readable($plugin_file)){continue;}
		$plugin_info = plugin_info($plugin_file);
		if (empty($plugin_info->Name)){continue;}
		$plugins[str_replace($plugins_folder, '',$plugin_file)] = $plugin_info;
	}
	uasort($plugins, 'sort_plugins');
	return $plugins;
}
function sort_plugins($first_plugin, $second_plugin) {
	return strnatcasecmp($first_plugin->Name, $second_plugin->Name);
}
function plugin_info($plugin_file) {
	$plugin_info = file_exists($plugin_file) && is_readable($plugin_file) ? file_get_contents($plugin_file) : '';
	preg_match('|Plugin Name:(.*)|i', $plugin_info, $plugin_name);
	preg_match('|Plugin URL:(.*)|i', $plugin_info, $plugin_url);
	preg_match('|Description:(.*)|i', $plugin_info, $description);
	preg_match('|Author:(.*)|i', $plugin_info, $author_name);
	preg_match('|Author URL:(.*)|i', $plugin_info, $author_url);
	preg_match('|Installable?:(.*)|i', $plugin_info, $installable);
	$version = (preg_match('|Version:(.*)|i', $plugin_info, $version)) ? $version[1] : '';
	$name = $theme = isset($plugin_name[1]) ? trim($plugin_name[1]) : '';
	$installable = isset($installable[1]) ? strtolower(trim($installable[1])) : '';
	$authors =  isset($author_name[1]) ? explode(',',$author_name[1]) : array();
	$author_urls = isset($author_url[1]) ?  explode(',',$author_url[1]) : array();
	if(isset($authors[1])) {
		$plugin_authors = array();
		foreach($authors as $i => $v) {
			$plugin_authors[] = (isset($author_urls[$i]) && !empty($author_urls[$i])) ? '<a href="'.$author_urls[$i].'" title="'.L_THEME_VISIT_AUTHOR.'" target="_blank">'. $v.'</a>' : $v;
		}
		$author = proper_list($plugin_authors);
	} else {
		$author_name = isset($author_name[1]) ? $author_name[1] : '';
		$author = !isset($author_url[1]) || empty($author_url[1]) ? $author_name : '<a href="'.$author_url[1].'" title="'.L_THEME_VISIT_AUTHOR.'" target="_blank">'. $author_name.'</a>';
	}
	//Huh? Because I like object notation, that's why!
	return (object) array('Name' => $name, 'Title' => $theme, 'Description' => (isset($description[1]) ? $description[1] : ''), 'Author' => $author, 'Version' => $version, 'Installable' => $installable);
}
function add_breadcrumb($text, $priority = 10) {
	global $page_meta;
	$page_meta = check_array($page_meta);
	$page_meta['crumbs'][$priority][] = $text;
}
function make_breadcrumbs() {
	global $page_meta;
	$page_meta = check_array($page_meta);
	$sep = ' '.L_SEPARATOR.' ';
	ksort($page_meta['crumbs']);
	$final = '';
	foreach($page_meta['crumbs'] as $priority => $title) {
		$final .= $sep.implode($sep,$title).' ';
	}
	return ltrim($final, $sep);
}
function add_title($text, $priority = 10) {
	global $page_meta;
	$page_meta = check_array($page_meta);
	$page_meta['title'][$priority][] = $text;
}
function reset_title() {
	global $page_meta;
	$page_meta = array('title' => array());
}
function make_title() {
	global $page_meta;
	$page_meta = check_array($page_meta);
	$sep = ' '.L_SEPARATOR.' ';
	ksort($page_meta['title']);
	$final = '';
	foreach($page_meta['title'] as $priority => $title){
		$final .= $sep.implode($sep,$title).' ';
	}
	return ltrim($final, $sep);
}
function csort_cmp(&$a, &$b) {
	global $csort_cmp;
	if ($a->$csort_cmp['key'] > $b->$csort_cmp['key']) {
		return $csort_cmp['direction'];
	}
	if ($a->$csort_cmp['key'] < $b->$csort_cmp['key']) {
		return -1 * $csort_cmp['direction'];
	}
	return 0;
}
function csort(&$a, $k, $sort_direction='ASC') {
	global $csort_cmp;
	$sort_direction = $sort_direction == 'ASC' ? 1 : -1;
	$csort_cmp = array(
		'key'   => $k,
		'direction'     => $sort_direction
	);
	usort($a, "csort_cmp");
	unset($csort_cmp);
}
function _l($lex) {
	$constant = 'L_'.$lex;
	return defined($constant) ? constant($constant) : '';
}
?>