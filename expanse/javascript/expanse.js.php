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
$files =  array('jquery.min', 'jquery-ui.min', 'jquery-cookie', 'jquery.nearest.min', 'underscore.min', 'backbone.min', 'bootstrap', 'expanse.main');
if(isset($_GET['full'])) {
	//$files = array_merge($files, array('effects','dragdrop','controls','slider'));
}
$extended = array();
/*   Do not edit below this line   //-------------------------------*/
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = ((60 * 60) * 24) * 14 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
$folder = dirname(__FILE__).'/';
foreach($files as $include) {
	$include = trim($include);
	$the_file = $folder.$include.'.js';
	if(empty($include) || !file_exists($the_file)) {
		continue;
	}
	include($the_file);
	echo "\n";
}
$get_extended = isset($_GET['extend']) && ctype_alnum($_GET['extend']) ? $_GET['extend'] : '';
if(!empty($get_extended) && isset($extended[$get_extended])) {
	include($folder.$get_extended.'.js');
	echo "\n";
}
