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
------------------------------------------------------------
COMMON FUNCTIONS
------------------------------------------------------------
This file contains functions that clean up various server
differences and set up error handling.

These are not specific to expanse and are not the core
functionality.
============================================================
*/
if(isset($_GET['debug']) && $_GET['debug'] == 'true') {
	set_error_handler('expanse_error_handler');
}

function stripslashes_deep($value) {
	if(is_array($value)) {
		$value = array_map('stripslashes_deep', $value);
	} elseif(is_object($value)) {
		$vars = get_object_vars($value);
		foreach($vars as $key=>$data) {
			$value->{$key} = stripslashes_deep($data);
		}
	} else {
		$value = stripslashes($value);
	}

    return $value;
}

$_POST = array_map('stripslashes_deep', $_POST);
$_GET = array_map('stripslashes_deep', $_GET);
$_COOKIE = array_map('stripslashes_deep', $_COOKIE);

function turnOffGlobals() {
	if(isset($_REQUEST['GLOBALS'])) {
		die('GLOBALS overwrite attempt detected');
	}
	// Variables that shouldn't be unset
	$exempt = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');
	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach($input as $ind => $val) {
		if(!in_array($ind, $exempt) && isset($GLOBALS[$ind])) {
			unset($GLOBALS[$ind]);
		}
	}
}

/**
* Returns a portion of a string while leaving
* intact any words in that string. Also removes all HTML.
* @param string $text
* @param string $alt_text
* @param bool $keep_line_breaks
* @return string $text
*/
function trim_excerpt($text, $alt_text = L_NO_TEXT_IN_DESCRIPTION, $keep_line_breaks = false, $newlines = false) {
	$text = str_replace(']]>', ']]&gt;', $text);
	$paragraph = explode('</p>', $text, 2);
	$paragraph = $paragraph[0];
	if($paragraph != '') {
		if($paragraph[strlen($paragraph)-1] = '.') {
			$paragraph[strlen($paragraph)-1] = ' ';
		}
	}
	$paragraph .= '&hellip;</p>';
	$text = $paragraph;
	return $text;
}

/**
* Returns the title, while inserting line breaks if the title is too long
* @param string $text
* @param string $alt_text
* @param bool $keep_line_breaks
* @return string $text
*/
function trim_title($title, $alt_text = L_NO_TEXT_IN_TITLE) {
	$title = trim($title);
	$title = empty($title) ? $alt_text : view($title);
	$title = wordwrap($title, WORDWRAP, "<br />\n"/*, 1*/);
	return $title;
}

function expanse_error_handler( $errno, $errstr, $errfile, $errline, $errcontext) {
	if(error_reporting() == 0) {
		return;
	}
	echo '<div style="background:#fff;color:#000;border:1px solid #333;padding:10px;">'.
	"\n<br /><strong>Error:</strong>". print_r( $errstr, true).
	"\n<br /><strong>In file:</strong>". print_r( $errfile, true).
	"\n<br /><strong>on line:</strong>". print_r( $errline, true).
	"\n<br /><strong>In context:</strong>".print_r( $errcontext, true).
	"\n<br />Backtrace of expanse_error_handler()".
	print_r( backtrace(), true)
	.'</div>';
}

function backtrace() {
	$debug_array = debug_backtrace();
	$counter = count($debug_array);
	echo '
	<style>
	.debug_table table, .called_by{
	position:relative;
	}
	.debug_table table{
	border:1px solid #777;
	}
	.func_name{
	color:#f30;
	}
	.func_args{
	color:#2020F0;
	}
	.calling_function td{
	color:#000;
	border-bottom:1px solid #777;
	background:#c2c2c2;
	padding: 5px 10px;
	}
	.error_details td{
	color:#000;
	background:#dfdfdf;
	padding: 5px 10px;
	}
	.called_by{
	font-weight:bold;
	margin-bottom:10px;
	}
	</style>
	<div class="debug_table">';
	for($tmp_counter = 0; $tmp_counter != $counter; ++$tmp_counter) {
		$margin = $tmp_counter*10;
		?>
		<table style="left:<?php echo $margin; ?>px" border="0" cellpadding="5" cellspacing="0">
			<tr class="calling_function">
				<td>function <span class="func_name">
					<?php
					echo isset($debug_array[$tmp_counter]["function"]) ? $debug_array[$tmp_counter]["function"] : '';?>(</span> <span class="func_args"><?php
					//count how many args a there
					$args_counter = isset($debug_array[$tmp_counter]["args"]) ? count($debug_array[$tmp_counter]["args"]) : 0;
					//print them
					for($tmp_args_counter = 0; $tmp_args_counter != $args_counter; ++$tmp_args_counter) {
						echo isset($debug_array[$tmp_counter]["args"]) ? (	is_array($debug_array[$tmp_counter]["args"][$tmp_args_counter]) ||  is_object($debug_array[$tmp_counter]["args"][$tmp_args_counter]) ? print_r($debug_array[$tmp_counter]["args"][$tmp_args_counter], true) : $debug_array[$tmp_counter]["args"][$tmp_args_counter] ) : '';
						echo (($tmp_args_counter + 1) != $args_counter) ? ', ' : ' ';
					}
					?>
					</span><span class="func_name">)</span>
				</td>
			</tr>
			<tr class="error_details">
				<td>
					{<br>
					file: <?php echo isset($debug_array[$tmp_counter]["file"]) ? $debug_array[$tmp_counter]["file"] : '';?><br>
					line: <?php echo isset($debug_array[$tmp_counter]["line"]) ? $debug_array[$tmp_counter]["line"] : '';?><br>
					}
				</td>
			</tr>
		</table>
		<?php
		if(($tmp_counter + 1) != $counter) {
			echo("<div class=\"called_by\" style=\"left:{$margin}px\">was called by:</div>");
		}
	}
	return $error_contents;
}
