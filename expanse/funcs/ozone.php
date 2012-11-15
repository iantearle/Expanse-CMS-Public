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

$ozone = array();
$ozoneAction = array();
/*
Filter API
Hooks into template processing & display
*/
function fuseOzone($field) {
	global $ozone;
	if(isset($ozone['all'])) {
		foreach($ozone['all'] as $priority => $functions) {
			if(isset($ozone[$field][$priority])) {
				$ozone[$field][$priority] = array_merge($ozone['all'][$priority], $ozone[$field][$priority]);
			} else {
				$ozone[$field][$priority] = array_merge($ozone['all'][$priority], array());
				$ozone[$field][$priority] = array_unique($ozone[$field][$priority]);
			}
		}
	}
	if(isset($ozone[$field])) {
		ksort($ozone[$field]);
	}
}

function applyOzone($field, $content) {
	global $ozone;
	fuseOzone($field);
	$args = array_slice(func_get_args(), 2);
	if(isset($ozone[$field])) {
		foreach($ozone[$field] as $priority => $functions) {
			if(!is_null($functions)) {
				foreach($functions as $function) {
					if(!function_exists($function['function'])) { continue; }
					$all_args = array_merge(array($content), $args);
					$function_name = $function['function'];
					$accepted_args = $function['accepted_args'];
					if($accepted_args == 1) {
						$the_args = array($content);
					} elseif($accepted_args > 1) {
						$the_args = array_slice($all_args, 0, $accepted_args);
					} elseif($accepted_args == 0) {
						$the_args = null;
					} else {
						$the_args = $all_args;
					}
					$content = call_user_func_array($function_name, $the_args);
				}
			}
		}
	}
	return $content;
}

function ozone_filter($field, $func, $priority = 10, $accepted_args = 1) {
	global $ozone;
	if(isset($ozone[$field][$priority])) {
		foreach($ozone[$field][$priority] as $filter) {
			if($filter['function'] == $func) {
				return true;
			}
		}
	}
	return $ozone[$field][$priority][] = array('function' => $func, 'accepted_args' => $accepted_args);
}

function dropOzone($field, $func, $priority = 10, $accepted_args = 1) {
	global $ozone;
	if(isset($ozone[$field][$priority])) {
		$newfunclist = array();
		foreach($ozone[$field][$priority] as $filter) {
			if($filter['function'] != $func) {
				$newfunclist[] = $filter;
			}
		}
		$ozone[$field][$priority] = $newfunclist;
	}
	if(empty($ozone[$field][$priority])) {
		unset($ozone[$field][$priority]);
	}
	return true;
}

/*
Action API
Hooks into form processing
*/
function fuseOzoneAction($field) {
	global $ozoneAction;
	if(isset($ozoneAction['all'])) {
		foreach($ozoneAction['all'] as $priority => $functions) {
			if(isset($ozoneAction[$field][$priority])) {
				$ozoneAction[$field][$priority] = array_merge($ozoneAction['all'][$priority], $ozoneAction[$field][$priority]);
			} else {
				$ozoneAction[$field][$priority] = array_merge($ozoneAction['all'][$priority], array());
				$ozoneAction[$field][$priority] = array_unique($ozoneAction[$field][$priority]);
			}
		}
	}
	if(isset($ozoneAction[$field])) {
		ksort($ozoneAction[$field]);
	}
}

function applyOzoneAction($field, $content='') {
	global $ozoneAction;
	fuseOzoneAction($field);
	$more_args = array_slice(func_get_args(), 2);
	$all_args = is_array($content) ? array_merge($content, $more_args) : array_merge(array($content), $more_args);
	if(isset($ozoneAction[$field])) {
		foreach($ozoneAction[$field] as $priority => $functions) {
			if(!is_null($functions)) {
				foreach($functions as $function) {
					$function_name = $function['function'];
					$accepted_args = $function['accepted_args'];
					if($accepted_args == 1) {
						$the_args = array($content);
					} elseif($accepted_args > 1) {
						$the_args = array_slice($all_args, 0, $accepted_args);
					} elseif($accepted_args == 0) {
						$the_args = null;
					} else {
						$the_args = $all_args;
					}
					$content = call_user_func_array($function_name, $the_args);
				}
			}
		}
	}
	return $content;
}

/*
*
* @param
* @return
*/
function ozone_action($field, $func, $priority = 10, $accepted_args = 1) {
	global $ozoneAction;
	if(isset($ozoneAction[$field][$priority])) {
		foreach($ozoneAction[$field][$priority] as $filter) {
			if($filter['function'] == $func) {
				return true;
			}
		}
	}
	return $ozoneAction[$field][$priority][] = array('function' => $func, 'accepted_args' => $accepted_args, );
}

function dropOzoneAction($field, $func, $priority = 10, $accepted_args = 1) {
	global $ozoneAction;
	if(isset($ozoneAction[$field][$priority])) {
		$newfunclist = array();
		foreach($ozoneAction[$field][$priority] as $filter) {
			if($filter['function'] != $func) {
				$newfunclist[] = $filter;
			}
		}
		$ozoneAction[$field][$priority] = $newfunclist;
	}
	if(empty($ozoneAction[$field][$priority]) && isset($ozoneAction[$field][$priority])) {
		unset($ozoneAction[$field][$priority]);
	}
	return true;
}

function ozone_walk(&$content, $prefix = '') {
	if(!is_array($content) && !is_object($content)) {
		return;
	}
	if(is_object($content)) {
		foreach($content as $k => $v) {
			if(is_object($v) || is_array($v)) {
				$content->{$k} = ozone_walk($v);
				continue;
			}
			$content->{$k} = applyOzone($prefix.$k, $v);
		}
	} else {
		foreach($content as $k => $v) {
			if(is_array($v) || is_object($v)) {
				$content[$k] = ozone_walk($v);
				continue;
			}
			$content[$k] = applyOzone($prefix.$k, $v);
		}
	}
	return $content;
}
