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
/*   Error reporting level   //---------------------------*/
error_reporting(E_ALL);
session_start();
/*   Define constants   //-------------------------------*/
define('LOGGED_IN', false);
define('PROTOCOL',(((!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != "on")) ? 'http://' : 'https://'));
define('CURRENT_PAGE', PROTOCOL.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
if(!isset($_GET['step'])) {
	header('Location: '.CURRENT_PAGE.'?step=install');
}
define('MIN_VERSION', '5.0');
define('VERSION_CORRECT',version_compare(phpversion(), MIN_VERSION, ">="));
define('MYSQL_INSTALLED', extension_loaded('mysql'));
define('IS_WRITABLE', is_writable(dirname(__FILE__)));
define('SAFE_MODE', ((bool) ini_get('safe_mode')));
define('INSTALLABLE', can_install());
define('EXPANSE_PATH', realpath(dirname(__FILE__)).'/');
define('EXPANSE_URL', PROTOCOL.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/');
define('CURRENT_STEP', (isset($_GET['step']) ? strtolower($_GET['step']) : ''));

$errors = array();
$results = array();
/*   Require files   //-------------------------------*/
require(EXPANSE_PATH.'funcs/common.functions.php');
if(file_exists(EXPANSE_PATH.'config.php')) {
	require(EXPANSE_PATH.'config.php');
	require_once(EXPANSE_PATH.'funcs/database.class.php');
}
require(EXPANSE_PATH.'funcs/output.class.php');
require(EXPANSE_PATH.'funcs/xajax.inc.php');
require(EXPANSE_PATH.'funcs/ozone.php');
require(EXPANSE_PATH.'funcs/ozone.default.php');
require(EXPANSE_PATH.'funcs/pclzip.lib.php');
require_once(EXPANSE_PATH.'funcs/expanse.class.php');
require(EXPANSE_PATH.'funcs/functions.php');
require(EXPANSE_PATH.'funcs/common.vars.php');
require(EXPANSE_PATH.'funcs/upgrade.functions.php');
function can_install() {
	return (VERSION_CORRECT && MYSQL_INSTALLED && IS_WRITABLE && !SAFE_MODE);
}
function check_post($str) {
	return (isset($_POST[$str]) && !empty($_POST[$str]) ? trim($_POST[$str]) : '');
}
function has_uploads() {
	return (file_exists(EXPANSE_PATH.'uploads') && is_dir(EXPANSE_PATH.'uploads'));
}
$outmess = new outputMessages;
$install = check_post('install');
$eula = check_post('eula_read');
if(isset($_POST['delete_install'])) {
	@unlink(EXPANSE_PATH.basename(CURRENT_PAGE));
	header('Location:'.EXPANSE_URL);
}
if(CURRENT_STEP == 'install') {
	if(INSTALLABLE) {
		if(!empty($install)) {
			if(empty($eula)) {
				$errors['missing_eula'] = L_EULA_NOTICE;
				printOut(FAILURE, $errors['missing_eula']);
			} else {
				//Install logic
				$yourname = isset($_POST['yourname']) ? trim(stripslashes($_POST['yourname'])): '';
				$adminemail = isset($_POST['adminemail']) ? trim(stripslashes($_POST['adminemail'])): '';
				$sitename = isset($_POST['sitename']) ? trim(stripslashes($_POST['sitename'])): '';
				$yourname = preg_replace("/[^A-Za-z0-9_-\s]/i", '', $yourname);
				$adminemail = checkEmail($adminemail) ? $adminemail : '';
				$sitename = preg_replace("/[^A-Za-z0-9_-\s]/i", '', $sitename);
				if(empty($adminemail) || empty($sitename) || empty($yourname)) {
					$errors['missing_details'] = 'Please make sure you enter in your Name, e-mail, and your site name. Also, make sure that your email is valid, and that there arent any strange characters in the fields (things like ^ & * etc.)';
					printOut(FAILURE,$errors['missing_details']);
				} else {
					$sqlhost = trim(check_post('dbhost'));
					$sqluser = trim(check_post('dbuser'));
					$sqlpass = trim(check_post('dbpassword'));
					$sqldb = trim(check_post('db'));
					$can_connect = @mysql_connect($sqlhost,$sqluser,$sqlpass);
					$can_select = @mysql_select_db($sqldb);
					if(!$can_connect || !$can_select) {
						$errors['db_cnx_incorrect'] = 'Sorry, but no connection could be made to the supplied settings. Please check those settings and make sure they\'re correct.';
						printOut(FAILURE,$errors['db_cnx_incorrect']);
					} else {
						$sqlprefix = check_post('prefix');
						$sqlprefix 	= (!empty($sqlprefix)) ? $sqlprefix : 'exp_';
						$sqlprefix = preg_replace('|[^A-Z0-9_]|i','_', $sqlprefix);
						$config_data =
"<?php
/*
------------------------------------------------------------
Expanse Config File
============================================================
*/
//DATABASE VARIABLES
\$CONFIG['host']		= '$sqlhost'; //Database host; usually localhost, or something like mysql.server.com
\$CONFIG['user']		= '$sqluser'; //Database User
\$CONFIG['pass']		= '$sqlpass'; //Database Password
\$CONFIG['db']			= '$sqldb'; //Database Name
\$CONFIG['prefix']		= '$sqlprefix'; //Table Prefix
//Stop editing
\$CONFIG['home']		= dirname(__FILE__);";
						$config_file = fopen(EXPANSE_PATH.'config.php','w+');
						fwrite($config_file,$config_data);
						$wrote_config = fclose($config_file);
						$has_uploads = has_uploads();
						if(!$has_uploads) {
							if(!mkdir(EXPANSE_PATH.'uploads')) {
								$dirsuccess = false;
								$errors['cant_write_uploads'] = 'Sorry, but there was a problem creating the necessary directories.';
								printOut(FAILURE,$errors['cant_write_uploads']);
							} else {
								$has_uploads = true;
							}
						}
						if($has_uploads && $wrote_config) {
							$CONFIG['host']		= $sqlhost;
							$CONFIG['user']		= $sqluser;
							$CONFIG['pass']		= $sqlpass;
							$CONFIG['db']		= $sqldb;
							$CONFIG['prefix']	= $sqlprefix;
							if(!class_exists('DatabaseConnection')) {
								require_once(dirname(__FILE__).'/funcs/database.class.php');
							} else {
								//Refresh connection
								$Database->Close();
								unset($Database);
								$Database = new DatabaseConnection;
							}
							$cms_version = CMS_VERSION;
							require(EXPANSE_PATH.'funcs/schema.sql.php');
							$dbDelta = new dbDelta($Database);
							$dbDelta->findDifferences($schema['prepare']);
							$results['db_prepare'] = $dbDelta->perform_queries(DBDELTA_ALL);
							$dbDelta->findDifferences($schema['structure']);
							$results['db_structure'] = $dbDelta->perform_queries(DBDELTA_MOST);
							$dbDelta->findDifferences($schema['populate']);
							$results['db_populate'] = $dbDelta->perform_queries(DBDELTA_MOST);

							printOut('
							<div class="span12">
							<div class="page-header">
								<h1>Congratulations!</h1>
							</div>
							<p>You have now installed expanse, the cms for creative people.</p>
							<p><strong>You must now remove install.php from the server before you can continue.</strong> Leaving install.php on the server is a major security risk, and you must delete it.</p>
							<p>After you have deleted <code>install.php</code> from the server, you can login to expanse. </p>
							</div>
							</div>
							<div class="row-fluid">
							<div class="span12">
							<div class="well">
							<h3>Here are your login details</h3>
							<p id="loginDetails">
							<strong>Login:</strong> <a href="'.EXPANSE_URL.'index.php">'.EXPANSE_URL.'index.php</a><br />
							<strong>Username:</strong> admin<br />
							<strong>Password:</strong> '.$default_install_values['random_password'].'<br />
							Please be sure to remember this information so that you may login (though you can  change your username and password once you login).</p>
							</div>
							</div>
							</div>
							<div class="row-fluid">
							<div class="span12">
							<p>Be sure to keep a copy install.php somewhere on your local computer just in case you wish to uninstall Expanse later on (though we really hope you don\'t).</p>
							<p>If you want us to try and delete <code>install.php</code> for you, go ahead and press this button. If successful, you\'ll be taken to the login page, but if it\'s not, you will have to do it manually.</p>
							<div class="actions">
							<input type="submit" name="delete_install" id="delete_install" value="Delete my install.php file" class="btn danger" />
							</div>
							</div>
							</div>
							');
							require(EXPANSE_PATH.'funcs/mail.class.php');
							require(EXPANSE_PATH.'funcs/template.class.php');
							if(!ini_get('sendmail_from')) {
								$sendmail_from = $adminemail;
								ini_set('sendmail_from', $sendmail_from);
							}
							$users = new Expanse('users');
							$users->Get(1);
							$install_object = new stdClass();
							$install_object->expanseurl = EXPANSE_URL;
							$install_object->username = 'admin';
							$install_object->password = $default_install_values['random_password'];
							$templatebody = sprintt($install_object, EXPANSE_PATH.'/funcs/misc/@installmailer.tpl.html');
							$plaintext = trim(strip_tags($templatebody));
							$fromaddr = '<'.$adminemail.'>';
							$fromname = 'Expanse Installer';
							$mail = new htmlMimeMail;
							$mail->setFrom($fromname.' '.$fromaddr);
							$mail->setSubject('Expanse is installed. Happiness ensues.');
							$mail->setHTML($templatebody, $plaintext);
							$mail->send(array($adminemail));
						} else {
							$errors['mail_not_sent'] = 'Sorry, but there was a problem installing Expanse. It seems that your system is unwritable.';
							printOut(FAILURE,$errors['mail_not_sent']);
						}
					}
				} // has admin info

			} // agrees to eula
		} // installing
	} else {
		$errors['not_up_to_snuff'] = 'Your server did not meet one of the requirements below. Please correct this and refresh the page.';
		printOut(FAILURE, $errors['not_up_to_snuff']);
	}
}

$outmess->write_header('', 1, 1);

?>
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="./"><?php echo CMS_NAME ?></a>
			<p class="navbar-text pull-right"><?php echo CMS_NAME ?> thinks you're dreamy.</p>
		</div>
	</div>
</div>
<div class="container-narrow">
	<form action="" method="post" class="form-stacked">
	<?php
	if(CURRENT_STEP == 'install') { ?>
			<div class="row-fluid"> <?php
				if(INSTALLABLE) {
					echo $output;
					if(isInstalled() && empty($install)) {
						printf(ALERT.'<br />', 'It appears that Expanse is already installed. If you are looking to uninstall it, you can go <a href="'.CURRENT_PAGE.'?step=uninstall">here</a>.');
					}
					/*   At the first step   //-------------------------------*/
					if(empty($install) || !empty($errors)) { ?>
						<div class="span12">
							<div class="page-header">
								<h1>Settings</h1>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<fieldset>
								<legend>Your information</legend>
								<div class="control-group">
									<label for="yourname" class="control-label">Your name</label>
									<div class="controls">
										<input name="yourname" id="yourname" value="<?php echo @$_POST['yourname']; ?>" type="text" />
									</div>
								</div>
								<div class="control-group">
									<label for="adminemail" class="control-label">Your email</label>
									<div class="controls">
										<input name="adminemail" id="adminemail" value="<?php echo @$_POST['adminemail']; ?>" type="text" />
									</div>
								</div>
								<div class="control-group">
									<label for="sitename" class="control-label">Your site name</label>
									<div class="controls">
										<input name="sitename" id="sitename" value="<?php echo @$_POST['sitename']; ?>" type="text" />
									</div>
								</div>
							</fieldset>
						</div>
						<div class="span6">
							<fieldset>
								<legend>MySQL Settings</legend>
								<div class="control-group">
									<label for="dbhost" class="control-label">MySQL Hostname</label>
									<div class="controls">
										<input name="dbhost" value="<?php echo @$_POST['dbhost']; ?>" type="text" class="formfields" id="dbhost" <?php popOver('right', 'MySQL Hostname', 'Address to the server (eg. localhost, or mysqlserver.domain.com)'); ?> />
									</div>
								</div>
								<div class="control-group">
									<label for="dbuser" class="control-label">MySQL Username</label>
									<div class="controls">
										<input name="dbuser" value="<?php echo @$_POST['dbuser']; ?>" type="text" class="formfields" id="dbuser"  />
									</div>
								</div>
								<div class="control-group">
									<label for="dbpassword" class="control-label">MySQL Password</label>
									<div class="controls">
										<input name="dbpassword" value="" type="password" class="formfields" id="dbpassword" />
									</div>
								</div>
								<div class="control-group">
									<label for="db" class="control-label">MySQL Database</label>
									<div class="controls">
										<input name="db" value="<?php echo @$_POST['db']; ?>" type="text" class="formfields" id="db" <?php popOver('right', 'MySQL Database', 'Name of the database to use'); ?> />
									</div>
								</div>
								<div class="control-group">
									<label for="prefix" class="control-label">Table Prefix</label>
									<div class="controls">
										<input name="prefix" type="text" class="formfields" value="<?php echo isset($_POST['prefix']) ? $_POST['prefix'] : 'exp_'; ?>" id="prefix" <?php popOver('right', 'Table Prefix', 'This is the prefix to give the database tables. If you leave it empty, it will default to &quot;exp_&quot;, otherwise you can enter in whatever you wish. Note that if a table exists with the same name, it will be overwritten. Also, please note that only alphanumeric characters and underscores are allowed. Anything that is not either an alphanumeric character, or an underscore will be replaced with underscores.'); ?> />
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="row-fluid">
						<div id="eula" class="well">
							<p>expanse is licensed under the <a href="http://opensource.org/licenses/mit-license.php"><span class="caps">MIT</span> open-source license</a>. That means the code is copyright Ian Tearle, but you have permission to do almost anything you like with it. <strong>It’s free, both as in free beer and free speech.</strong></p>
							<p>This includes using all or parts of the code in commercial applications. I request, but don’t require, that you give explicit credit and a link to expanse cms (<a href="http://expansecms.org">http://expansecms.org</a>), without using the expanse name or logo to advertise your product without written permission from the trademark owner (as specified by international trademark laws). You must, however, include the following license and notice with anything you distribute.</p>
							<div id="boilerplate">
							<p>Copyright &copy; 2012 Ian Tearle (http://expansecms.org)</p>
							<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>
							<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>
							<p>THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUR OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
							</div>
							<div class="control-group">
								<div class="input-list">
									<label for="eula_read" class="checkbox">
										<input name="eula_read" id="eula_read" type="checkbox" value="1" />
										I have read and understood the End User License Agreement
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="form-actions">
							<p class="alert alert-warning">Once you press install, the installer will attempt to connect to your database, and if successful, will create all the necessary tables. If there are any tables with the same name, they will be overwritten.<br>
							The installer will also attempt to create an upload directory for your files.</p>
							<input name="install" id="install" type="submit" class="btn large primary" value="Install" /> <input type="reset" class="btn danger" value="Reset" />
						</div>
					</div>
				<?php
				}
			} else { ?>
					<div class="span12">
						<div class="page-header">
							<h1>System Requirements</h1>
						</div>
						<form action="" method="post" id="eulaStep1">
							<?php echo $output; ?>
							<table class="table">
								<thead>
									<tr>
										<th align="center">&nbsp;</th>
										<th align="center">Required</th>
										<th colspan="2" align="center">Your System</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th align="left">Minimum PHP Version</th>
										<td align="center"><?php echo MIN_VERSION; ?></td>
										<td align="center" class="<?php echo (!VERSION_CORRECT) ? "text-error" : "text-success"; ?>"><?php echo phpversion(); ?></td>
										<td align="left"><?php echo (!VERSION_CORRECT) ? '<i class="icon-remove"></i> Expanse requires a newer version of PHP' : '<i class="icon-ok"></i> Your PHP version is capable of running Expanse';?></td>
									</tr> <?php
									if(!VERSION_CORRECT) { ?>
										<tr>
											<td colspan="4">
												<div class="alert alert-info">
													<h3>How can I fix this?</h3>
													<p>In order to fix this, you can either ask your host to upgrade their version of PHP, or signup with one of our <a href="http://expansecms.com/?p=hosting" target="_blank">recommended hosts</a>.</p>
												</div>
											</td>
										</tr><?php
									} ?>
									<tr>
										<th align="left">Is MySQL Installed? </th>
										<td align="center">Yes</td>
										<td align="center" class="<?php echo (!MYSQL_INSTALLED) ? "text-error" : "text-success"; ?>"><?php echo (MYSQL_INSTALLED) ? 'Yes' : 'No'; ?></td>
										<td align="left"><?php echo (!MYSQL_INSTALLED) ? '<i class="icon-remove"></i> MySQL must be installed to run Expanse' : '<i class="icon-ok"></i> MySQL is installed'; ?></td>
									</tr> <?php
									if(!MYSQL_INSTALLED) { ?>
										<tr>
											<td colspan="4">
												<div class="alert alert-info">
													<h3>How can I fix this?</h3>
													<p>In order to fix this, you can either ask your host to install MySQL, or signup with one of our <a href="http://expansecms.com/?p=hosting" target="_blank">recommended hosts</a>.</p>
												</div>
											</td>
										</tr> <?php
									} ?>
									<tr>
										<th align="left">Writing permissions to directory?</th>
										<td align="center">Yes</td>
										<td align="center" class="<?php echo (!IS_WRITABLE) ? "text-error" : "text-success"; ?>"><?php echo (IS_WRITABLE) ? "Yes" : "No" ?></td>
										<td align="left"><?php echo (!IS_WRITABLE) ? '<i class="icon-remove"></i> Expanse requires that this directory be writable during installation' : '<i class="icon-ok"></i> Your installation directory is writable'; ?></td>
									</tr> <?php
									if(!IS_WRITABLE) { ?>
										<tr>
											<td colspan="4">
												<div class="alert alert-info">
													<h3>How can I fix this?</h3>
													<p>In order to fix this, the easiest way to do this is to use your FTP program to change the permissions of the directory you installed Expanse in. If you're not sure how to do that, view your FTP program's help documentation about setting the CHMOD.</p>
												</div>
											</td>
										</tr> <?php
									} ?>
									<tr>
										<th align="left">Safe mode off?</th>
										<td align="center">Yes</td>
										<td align="center" class="<?php echo (SAFE_MODE) ? "text-error" : "text-success" ?>"><?php echo (SAFE_MODE) ? 'No' : 'Yes' ?></td>
											<td align="left"><?php echo (SAFE_MODE) ? '<i class="icon-remove"></i> Expanse requires that safe mode be off.' : '<i class="icon-ok"></i> Safe mode is off. Tell your host they\'re good people.'; ?></td>
									</tr> <?php
									if(SAFE_MODE) { ?>
										<tr>
											<td colspan="4">
												<div class="alert alert-info">
													<h3>How can I fix this?</h3>
													<p>You can ask your host to change this setting for you. Many hosts will allow you to change this setting, many times just by simply uploading a single file. If they will not change it for you or allow you to change it, you're better off looking for a new host from one of our <a href="http://expansecms.com/?p=hosting" target="_blank">recommended hosts</a>.</p>
												</div>
											</td>
										</tr> <?php
									} ?>
								</tbody>
							</table>
						<div class="actions">
							<a href="<?php echo CURRENT_PAGE ?>" class="btn primary">Reload</a>
						</div>
					</div>
			</div>
		</form>
<?php
		} //End not installable

	} elseif(CURRENT_STEP == 'uninstall') {
		$uninstall_type = isset($_GET['uninstall_type']) ? $_GET['uninstall_type'] : '';
		$result = array(
			'errors' => array(),
			'success' => array()
		);

		function uninstall_expanse() {
			global $result;
			if(isInstalled()) {
				if(has_uploads()) {
					uninstall_uploads();
				}
				if(db_installed()) {
					uninstall_db();
				}
				if(has_config()) {
					uninstall_config();
				}
			} else {
				$result['errors'][] = 'not installed';
			}
			return $result;
		}

		function uninstall_uploads() {
			global $Database, $CONFIG, $result;
			$uploaddir = EXPANSE_PATH.'uploads';
			if(delRecursive($uploaddir)){
				$result['success'][] = 'uploads';
			} else {
				$result['errors'][] = 'uploads';
			}
		}

		function db_installed() {
			if(has_config()){
				global $Database, $CONFIG;
				$table_list = array();
				$query = $Database->Query('SHOW tables FROM '.$CONFIG['db']);
				while($row = mysql_fetch_assoc($query)){
					$table_list[] =  $row['Tables_in_'.$CONFIG['db']];
				}
				return in_array($Database->Prefix.'items',$table_list);
			}
			return false;
		}

		function uninstall_db() {
			global $Database, $CONFIG, $result;
			$table_array = array('items','comments', 'customfields', 'hackattempts', 'prefs', 'sections', 'users', 'images', 'sessions');
			foreach($table_array as $table){
				$Database->Query("DROP TABLE IF EXISTS `{$Database->Prefix}$table`");
			}
			if(!db_installed()) {
				$result['success'][] = 'db';
			} else {
				$result['errors'][] = 'db';
			}
		}

		function has_config() {
			return file_exists(dirname(__FILE__).'/config.php');
		}

		function uninstall_config() {
			global $result;
			if(unlink(EXPANSE_PATH.'/config.php')){
				$result['success'][] = 'config';
			} else {
				$result['errors'][] = 'config';
			}
		}

		if(isset($_POST['delete_uploads']) && has_uploads()) {
			uninstall_uploads();
			if(!empty($result['success'])){
				printOut(SUCCESS, 'Your uploads folder has been deleted.');
			}
		}

		if(isset($_POST['delete_db']) && db_installed()){
			uninstall_db();
			if(has_config()){
				uninstall_config();
			}
		}

		if(isset($_POST['delete_config']) && has_config()) {
			uninstall_config();
		}

		if(isset($_POST['uninstall'])) {
			$uninstall = uninstall_expanse();
			if(count($uninstall['errors']) > 0) {
				foreach($uninstall['errors'] as $val) {
					if($val == 'uploads') {
						printOut(FAILURE, 'Sorry, but the uploads directory could not be deleted. Do you have writing permissions to that directory?');
					}
					if($val == 'db') {
						printOut(FAILURE, 'Sorry, but the database tables could not be removed. Is the information in your config file correct?');
					}
					if($val == 'config') {
						printOut(FAILURE, 'Sorry, but the config file could not be deleted. Do you have writing permissions for that file?');
					}
					if($val == 'not installed') {
						printOut(FAILURE, 'Sorry, but Expanse has not yet been installed. Please install it by going <a href="?step=install">here</a>.');
					}
				}
			} else {
				printOut(SUCCESS, 'Expanse was uninstalled. Your server is lonely now.');
			}
		}

		if(isInstalled()) {
			if(empty($uninstall_type)) { ?>
				<h1>Uninstall Expanse</h1>
				<?php echo $output; ?>
				<p>If you wish to uninstall the Expanse, click on the &quot;Uninstall Expanse&quot; button. Once you click the button, the uninstaller will attempt to delete your uploads folder, all the files in the folder, the database tables, and the configuration file. To finish the uninstallation process, please delete files you first uploaded to your server. </p>
				<div class="alert-message info" data-alert="alert"><a class="close" href="#">×</a>
					<p><strong>Warning: </strong>Once you press this button, all of your content will be deleted. The Expanse will need to be installed again. <br />
					<strong>Only do this if you are positive you wish to remove the Expanse.</strong></p>
				</div>
				<div class="actions">
					<input name="uninstall" type="submit" class="btn danger" id="uninstall" value="Uninstall Expanse" />
				</div>

				<p>If you'd like to uninstall just portions of Expanse, you <a href="?step=uninstall&amp;uninstall_type=manual">can visit this page</a>.</p>
				<?php
			} else { ?>
				<h1>Manually Uninstall Expanse</h1> <?php
				if(has_uploads()) { ?>
					<p>If you'd like to just delete the uploads folder you can click the button below.</p>
					<div class="control-group">
						<div class="controls">
							<input name="delete_uploads" type="submit" class="btn primary" id="delete_uploads" value="Delete the uploads folder." />
						</div>
					</div> <?php
				}
				if(db_installed()) { ?>
					<p>If you'd like to just clear all of your records from the database and delete the config file, click this button. (This will only delete the tables created by Expanse). Once you do this, Expanse will be considered to be uninstalled. </p>
					<div class="control-group">
						<div class="controls">
							<input name="delete_db" type="submit" class="btn" id="delete_db" value="Clear the database and delete the config file." />
						</div>
					</div> <?php
				}
				if(has_config()) { ?>
					<p>If you'd like to JUST delete the config file, without clearing the database (helpful if you're moving locations, and FTP wont delete the config file), click this button below.</p>
					<div class="control-group">
						<div class="controls">
							<input name="delete_config" type="submit" class="btn" id="delete_config" value="Just delete the config file." />
						</div>
					</div> <?php
				}
			}
		} elseif(!isset($_POST['uninstall'])) { ?>
			<div class="alert-message warning" data-alert="alert"><a class="close" href="#">×</a>Sorry, but Expanse has not yet been installed. Please install it by going <a href="?step=install">here</a>.</div> <?php
	} else {
		echo $output;
	}

} else {
	printf(ALERT,'<p>Wisdom may be attained by experimentation, but CMS installation is not.</p>');
} //everything else
?>
</form>
</div>
<?php

$outmess->write_footer();
