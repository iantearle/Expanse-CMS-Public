<?php
/**
 * ETS - Easy Template System - 3.06a
 * Copyright (C) 2002, 2003, 2004  Franck Marcia <phpets@hotmail.com>
 * http://ets.sourceforge.net
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */
/**
 * Size reducing behaviour
 */
define('_ETS_REDUCE_NULL',       0x0);
define('_ETS_REDUCE_OFF',        0x1);
define('_ETS_REDUCE_SPACES',     0x2);
define('_ETS_REDUCE_ALL',        0x4);
/**
 * Parsing modes
 */
define('_ETS_DATA',              0x1);
define('_ETS_NAME',              0x2);
define('_ETS_CLOSING_TAG',       0x4);
define('_ETS_VALUE',             0x8);
define('_ETS_COMMENT',          0x10);
define('_ETS_CDATA',            0x20);
/**
 * Parsing mode groups
 */
define('_ETS_GROUP0',  _ETS_COMMENT + _ETS_CDATA);
/**
 * Element types
 */
define('_ETS_NULL',              0x0);
define('_ETS_ROOT',              0x1);
define('_ETS_TEXT',              0x2);
define('_ETS_TAG',               0x4);
define('_ETS_ALT_TAG',           0x8);
define('_ETS_TEMPLATE',         0x10);
define('_ETS_SET',              0x20);
define('_ETS_SETVAL',           0x40);
define('_ETS_MIS',              0x80);
define('_ETS_MISVAL',          0x100);
define('_ETS_PHP',             0x200);
define('_ETS_CONST',           0x400);
define('_ETS_IF',              0x800);
define('_ETS_CODE',           0x1000);
define('_ETS_CHOOSE',         0x2000);
define('_ETS_WHENTEST',       0x4000);
define('_ETS_ELSE',           0x8000);
define('_ETS_CHOOSEVAR',     0x10000);
define('_ETS_WHENVAL',       0x20000);
define('_ETS_CALL',          0x40000);
define('_ETS_ARG',           0x80000);
define('_ETS_MIS_TEMPLATE', 0x100000);
define('_ETS_REDUCE',       0x200000);
define('_ETS_REPEAT',       0x400000);
define('_ETS_INCLUDE',      0x800000);
define('_ETS_INSERT',      0x1000000);
define('_ETS_EVAL',        0x2000000);
define('_ETS_SAFE',        0x4000000);
define('_ETS_ROOT_EVAL',   0x8000000);
define('_ETS_PLACE',      0x10000000);
define('_ETS_RSS',        0x40000000);	// <== added by [ryan]
/**
 * Element type groups
 */
define('_ETS_CODEs',   _ETS_CODE    + _ETS_PHP);
define('_ETS_CHOOSEs', _ETS_CHOOSE  + _ETS_CHOOSEVAR);
define('_ETS_SETs',    _ETS_SET     + _ETS_SETVAL);
define('_ETS_MISs',    _ETS_MIS     + _ETS_MISVAL);
// can't contain cdata, call, const, set, mis, choose, insert, eval, safe, if or repeat elements
define('_ETS_GROUP1',  _ETS_CHOOSEs + _ETS_CALL + _ETS_CODEs + _ETS_ROOT);
// can't contain mask or include elements
define('_ETS_GROUP2',  _ETS_CHOOSEs + _ETS_CALL + _ETS_CODEs);
// can't contain text or simple tag element
define('_ETS_GROUP3',  _ETS_CHOOSEs + _ETS_CALL + _ETS_ROOT);
// doesn't store text when finding closing tag
define('_ETS_GROUP4',  _ETS_CHOOSEs + _ETS_CALL);
/**
 * Building directions
 */
define('_ETS_FORWARD',           0x1);
define('_ETS_BACKWARD',          0x2);
/**
 * Building data types
 */
define('_ETS_MISSING',           0x1);
define('_ETS_SCALAR',            0x2);
define('_ETS_COMPLEX',           0x4);
/**
 * Handler names
 */
define('_ETS_SOURCE_READ', 'ets_source_read_handler');
define('_ETS_CACHE_READ',  'ets_cache_read_handler');
define('_ETS_CACHE_WRITE', 'ets_cache_write_handler');
define('_ETS_STRING_READ', '_printts');
define('_ETS_ENTRY', 'main');

/**
 * Template management class
 * This class is intended to be used by printt functions only
 */
class _ets
{
	/**
	 * Data tree
	 */
	var $datatree;
	/**
	 * Mask tree
	 */
	var $masktree = array();
	/**
	 * Indicate if handler functions are available
	 */
	var $external_source_read = FALSE;
	var $external_cache_read  = FALSE;
	var $external_cache_write = FALSE;
	/**
	 * Names of parsed containers
	 */
	var $containers = NULL;
	/**
	 * Current container name to be parsed
	 */
	var $container = NULL;
	/**
	 * Templates included at the root of other templates
	 */
	var $includes = NULL;
	/**
	 * Flag which indicates if parse must be skipped for the current container
	 */
	var $skip = FALSE;

	/*****   E R R O R   *****/
    /**
     * Check PHP error level
     */
	function check_level($error_level, $errno, $message)
	{
		if (error_reporting() & $error_level) {
			switch ($error_level) {
				case E_NOTICE:  $type = 'notice';  break;
				case E_WARNING: $type = 'warning'; break;
				case E_ERROR:   $type = 'error';   break;
			}
			echo "<b>ETS $type:</b> $message";
		}
		if ($error_level == E_ERROR) {
			exit;
		}
	}
    /**
     * Print out an error message
     */
	function error($error_type, $errno, $message = '', $line = 0, $elt_type = _ETS_NULL)
	{
		switch ($error_type) {
			case 0: // WARNING - wrong element in another or at root
				if ($elt_type == _ETS_ROOT) {
					$this->check_level(E_WARNING, $errno, "$message can't be defined outside a template on line $line of <b>{$this->container}</b><br>");
				} else {
					$this->check_level(E_WARNING, $errno, $this->elt_label($elt_type) . " can't contain $message on line $line of <b>{$this->container}</b><br>");
				}
				break;
			case 1: // WARNING - unexpected closing tag
				$this->check_level(E_WARNING, $errno, 'unexpected closing tag {/' . $message . "} on line $line of <b>{$this->container}</b><br>");
				break;
			case 2: // WARNING - unexpected character or space in tag
				$this->check_level(E_WARNING, $errno, "$message on line $line of <b>{$this->container}</b><br>");
				break;
			case 3: // WARNING - end of comment or cdata not found
				$this->check_level(E_WARNING, $errno, "end of " . $this->mode_label($elt_type) . " starting on line $line not found in <b>{$this->container}</b><br>");
				break;
			case 4: // WARNING - closing tag not found
				$this->check_level(E_WARNING, $errno, "closing tag not found for " . $this->elt_label($elt_type) . " starting on line $line in <b>{$this->container}</b><br>");
				break;
			case 5: // NOTICE - container not found (include element with line number available)
				$this->check_level(E_NOTICE, $errno, "unable to get the content of $message in include element on line $line of <b>{$this->container}</b><br>");
				break;
			case 6: // NOTICE - duplicated use of reduce element (without line number available, with several containers)
				$this->check_level(E_NOTICE, $errno, "$message<br>");
				break;
			case 7: // NOTICE - duplicated use of reduce element, invalid value for reduce element
				$this->check_level(E_NOTICE, $errno, "$message on line $line of <b>{$this->container}</b><br>");
				break;
			case 8: // ERROR - entry mask not found
				$this->check_level(E_ERROR, $errno, "unable to find entry mask $message<br>");
				break;
			case 9: // NOTICE - invalid datatree
				$this->check_level(E_NOTICE, $errno, "datatree is not an array, an object or null<br>");
				break;
			case 10: // ERROR - invalid containers
				$this->check_level(E_ERROR, $errno, "containers are not an array or a string<br>");
				break;
			case 11: // NOTICE - container not found (argument)
				$this->check_level(E_NOTICE, $errno, "unable to get the content of $message given as argument<br>");
				break;
			case 12: // NOTICE - container not found (insert element)
				$this->check_level(E_NOTICE, $errno, "unable to get the content of $message in insert element<br>");
				break;
			case 13: // NOTICE - container not found (include element without line number available)
				$this->check_level(E_NOTICE, $errno, "unable to get the content of $message in include element<br>");
				break;
			case 14: // NOTICE - container not found (eval element)
				$this->check_level(E_NOTICE, $errno, "unable to get the content of $message in eval element<br>");
				break;
			case 15: // NOTICE - wrong element in safe mode
				$this->check_level(E_NOTICE, $errno, $this->elt_label($elt_type) . " disabled for security reasons<br>");
				break;
			case 16: // WARNING - template already defined (without line number available, with several containers) / container already used
				$this->check_level(E_WARNING, $errno, "$message<br>");
				break;
		}
	}
	/**
	 * Define the label of a element type from an id
	 */
	function elt_label($eltid)
	{
		switch($eltid) {
			case _ETS_ROOT:         return 'root element';
			case _ETS_TEXT:         return 'text element';
			case _ETS_TAG:          return 'simple tag element';
			case _ETS_ALT_TAG:      return 'alternate tag element';
			case _ETS_TEMPLATE:     return 'template element';
			case _ETS_SET:          return 'set element';
			case _ETS_SETVAL:       return 'set-value element';
			case _ETS_MIS:          return 'missing element';
			case _ETS_MISVAL:       return 'missing-value element';
			case _ETS_PHP:          return 'PHP element';
			case _ETS_CONST:        return 'constant element';
			case _ETS_IF:           return 'if element';
			case _ETS_CODE:         return 'PHP code or test';
			case _ETS_CHOOSE:       return 'choose element';
			case _ETS_WHENTEST:     return 'when-test element';
			case _ETS_ELSE:         return 'else element';
			case _ETS_CHOOSEVAR:    return 'choose-variable element';
			case _ETS_WHENVAL:      return 'when-value element';
			case _ETS_CALL:         return 'call element';
			case _ETS_ARG:          return 'argument element';
			case _ETS_MIS_TEMPLATE: return 'missing template element';
			case _ETS_REDUCE:       return 'reduce element';
			case _ETS_REPEAT:       return 'repeat element';
			case _ETS_RSS:			return 'rss element';
			case _ETS_INCLUDE:      return 'include element';
			case _ETS_INSERT:       return 'insert element';
			case _ETS_EVAL:         return 'eval element';
			case _ETS_SAFE:         return 'safe eval element';
			case _ETS_ROOT_EVAL:    return 'eval or safe element';
			case _ETS_PLACE:    	return 'place element';
		}
	}
	/**
	 * Define the label of a parsing mode from an id
	 */
	function mode_label($modeid)
	{
		switch($modeid) {
			case _ETS_COMMENT:      return 'comment';
			case _ETS_CDATA:        return 'cdata';
		}
	}

	/*****   P A R S I N G   *****/
    /**
     * Store the size reducing behavior
     */
	function store_reduce(&$elts, $value)
	{
		switch(strtoupper($value)) {
			case 'OFF':
			case 'NOTHING':
				$elts['0reduce'] = _ETS_REDUCE_OFF;
				return TRUE;
			case 'SPACE':
			case 'SPACES':
				$elts['0reduce'] = _ETS_REDUCE_SPACES;
				return TRUE;
			case 'CRLF':
			case 'ON':
			case 'ALL':
				$elts['0reduce'] = _ETS_REDUCE_ALL;
				return TRUE;
			default:
				return FALSE;
		}
	}
    /**
     * Walk through a slash separated path of a node to build a tree
     */
	function node_path_walk($elements, $rank, $ptype, &$i, &$line, $cvalue, $ncontent, $content, $code)
	{
		if (count($elements) == 1) {
			$elt[$ptype . ':' . $i . ':' . $elements[0] . ':' . $cvalue] = $this->parse($code ? _ETS_CODE : $ptype, $i, $line, $ncontent, $content);
		} else {
			$element1 = array_shift($elements);
			$masktype = ($ptype == _ETS_MIS || $ptype == _ETS_MISVAL) ? _ETS_MIS_TEMPLATE : _ETS_TEMPLATE;
			$elt[$masktype . ':' . $i . '.' . $rank . ':' . $element1] = $this->node_path_walk($elements, $rank + 1, $ptype, $i, $line, $cvalue, $ncontent, $content, $code);
		}
		return $elt;
	}
    /**
     * Store a new node in the template tree
     */
	function store_node(&$elts, $ptype, &$i, &$line, $cname, $cvalue, $ncontent, $content, $code = FALSE)
	{
		$isabsolute = FALSE;
		if ($cname{0} == '/' && $cname{1} == '/') {
			$isabsolute = TRUE;
			$cname = substr($cname, 2);
		}
		$elements = explode('/', $cname);
		if (count($elements) == 1 && !$isabsolute) {
			$elts[$ptype . ':' . $i . ':' . $cname . ':' . $cvalue] = $this->parse($code ? _ETS_CODE : $ptype, $i, $line, $ncontent, $content);
		} else {
			if ($isabsolute) {
				$elts[_ETS_TEMPLATE . ':' . $i . '.1://'] = $this->node_path_walk($elements, 2, $ptype, $i, $line, $cvalue, $ncontent, $content, $code);
			} else {
				$element1 = array_shift($elements);
				$masktype = ($ptype == _ETS_MIS || $ptype == _ETS_MISVAL) ? _ETS_MIS_TEMPLATE : _ETS_TEMPLATE;
				$elts[$masktype . ':' . $i . '.1:' . $element1] = $this->node_path_walk($elements, 2, $ptype, $i, $line, $cvalue, $ncontent, $content, $code);
			}
		}
	}
    /**
     * Walk through a slash separated path of a leaf to build a tree
     */
	function leaf_path_walk($elements, $rank, $ptype, &$i, $cvalue)
	{
		if (count($elements) == 1) {
			$elt[$ptype . ':' . $i . ':' . $elements[0] . ':' . $cvalue] = '';
		} else {
			$element1 = array_shift($elements);
			$elt[_ETS_TEMPLATE . ':' . $i . '.' . $rank . ':' . $element1] = $this->leaf_path_walk($elements, $rank + 1, $ptype, $i, $cvalue);
		}
		return $elt;
	}
    /**
     * Store a new leaf in the template tree
     */
	function store_leaf(&$elts, $ptype, &$i, $cname, $cvalue = NULL)
	{
		$isabsolute = FALSE;
		if ($cname{0} == '/' && $cname{1} == '/') {
			$isabsolute = TRUE;
			$cname = substr($cname, 2);
		}
		$elements = explode('/', $cname);
		if (count($elements) == 1 && !$isabsolute) {
			$elts[$ptype . ':' . $i . ':' . $cname . ':' . $cvalue] = '';
		} else {
			if ($isabsolute) {
				$elts[_ETS_TEMPLATE . ':' . $i . '.1://'] = $this->leaf_path_walk($elements, 2, $ptype, $i, $cvalue);
			} else {
				$element1 = array_shift($elements);
				$elts[_ETS_TEMPLATE . ':' . $i . '.1:' . $element1] = $this->leaf_path_walk($elements, 2, $ptype, $i, $cvalue);
			}
		}
	}
    /**
     * Store a new text in the template tree
     */
	function store_text(&$elts, &$i, $ptype, $ntext, $ctext)
	{
		if ($ntext == 1 && $ptype != _ETS_ROOT) {
			$elts[_ETS_TEXT . ':' . $i] = $ctext;
		}
	}
	/**
	 * Define if the parameter is a non printable character
	 */
	function is_space($char)
	{
		$asc = ord($char);
		if ($asc == 32) {
			return TRUE;
		} elseif ($asc > 8 && $asc < 14) {
			return TRUE;
		}
		return FALSE;
	}
    /**
     * Recursively parse template
     */
	function parse($ptype, &$i, &$line, $ncontent, $content)
	{
		$elts = array();
		$mode = _ETS_DATA;
		$ntext = $nname = $nvalue = $nspace = 0;
		$ctext = $cname = $cvalue = '';
		$nametype = NULL;
		$nspecial = 0;
		$saveline = $line;
		for ( ; $i < $ncontent; ++$i) {
			// skip parsing when error
			if ($this->skip) {
				return array();
			}
			// current character and following
			$c0 = $content{$i};
			$c1 = $content{$i + 1};
			$is_space0 = $this->is_space($c0);
			$a0 = ord($c0);
			// line count
			if ($a0 == 10 || ($a0 == 13 && ord($c1) != 10)) {
				++$line;
			}
			// data acquisition
			if ($mode == _ETS_DATA) {
				// tag?
				if ($c0 == '{') {
					$c2 = $content{$i + 2};
					$c3 = $content{$i + 3};
					$c4 = $content{$i + 4};
					$c5 = $content{$i + 5};
					$c6 = $content{$i + 6};
					$c7 = $content{$i + 7};
					// {* (comment)
					if ($c1 == '*') {
						if ($ptype & _ETS_CODEs) {
							$this->error(0, 1, 'comment', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_COMMENT;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						++$i;
						++$nspecial;
						$saveline = $line;
					// {# (cdata)
					} elseif ($c1 == '#') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 2, 'cdata', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_CDATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						++$i;
						++$nspecial;
						$saveline = $line;
					// {loop:   (formerly "{mask:")
					} elseif ($c1 == 'l' && $c2 == 'o' && $c3 == 'o' && $c4 == 'p' && $c5 == ':') {
						if ($ptype & _ETS_GROUP2) {
							$this->error(0, 3, 'template element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_TEMPLATE;
						$i += 5;
					// {mask:
					} elseif ($c1 == 'm' && $c2 == 'a' && $c3 == 's' && $c4 == 'k' && $c5 == ':') {
						if ($ptype & _ETS_GROUP2) {
							$this->error(0, 3, 'template element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_TEMPLATE;
						$i += 5;
					// {call:
					} elseif ($c1 == 'c' && $c2 == 'a' && $c3 == 'l' && $c4 == 'l' && $c5 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 4, 'call element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 6;
						$index = _ETS_CALL . ':' . $i;
						$elts[$index]['template'] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						$elts[$index]['args'] = $this->parse(_ETS_CALL, $i, $line, $ncontent, $content);
					// {const:
					} elseif ($c1 == 'c' && $c2 == 'o' && $c3 == 'n' && $c4 == 's' && $c5 == 't' && $c6 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 5, 'constant element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 7;
						$elts[_ETS_CONST . ':' . $i] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						--$i;
					// {set:
					} elseif ($c1 == 's' && $c2 == 'e' && $c3 == 't' && $c4 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 6, 'set element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_SET;
						$i += 4;
					// {mis:
					} elseif ($c1 == 'm' && $c2 == 'i' && $c3 == 's' && $c4 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 7, 'missing element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_MIS;
						$i += 4;
					// {choose:
					} elseif ($c1 == 'c' && $c2 == 'h' && $c3 == 'o' && $c4 == 'o' && $c5 == 's' && $c6 == 'e' && $c7 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 8, 'choose element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_CHOOSEVAR;
						$i += 7;
					// {arg:
					} elseif ($c1 == 'a' && $c2 == 'r' && $c3 == 'g' && $c4 == ':') {
						if ($ptype == _ETS_CALL) {
							$mode = _ETS_NAME;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = _ETS_ARG;
							$i += 4;
						} else {
							$this->error(0, 9, 'argument element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {reduce:
					} elseif ($c1 == 'r' && $c2 == 'e' && $c3 == 'd' && $c4 == 'u' && $c5 == 'c' && $c6 == 'e' && $c7 == ':') {
						if ($ptype == _ETS_ROOT) {
							if (!isset($elts['0reduce']) || $elts['0reduce'] == _ETS_REDUCE_NULL) {
								$mode = _ETS_NAME;
								$ntext = $nname = $nvalue = $nspace = 0;
								$ctext = $cname = $cvalue = '';
								$nametype = _ETS_REDUCE;
								$i += 7;
							} else {
								$this->error(7, 10, 'reduce element already used', $line);
							}
						} else {
							$this->error(0, 11, 'reduce element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {include:
					} elseif ($c1 == 'i' && $c2 == 'n' && $c3 == 'c' && $c4 == 'l' && $c5 == 'u' && $c6 == 'd' && $c7 == 'e' && $content{$i + 8} == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 15, 'include element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 9;
						$elts[_ETS_INCLUDE . ':' . $i] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						--$i;
					// {insert:
					} elseif ($c1 == 'i' && $c2 == 'n' && $c3 == 's' && $c4 == 'e' && $c5 == 'r' && $c6 == 't' && $c7 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 14, 'insert element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 8;
						$elts[_ETS_INSERT . ':' . $i] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						--$i;
					// {eval:
					} elseif ($c1 == 'e' && $c2 == 'v' && $c3 == 'a' && $c4 == 'l' && $c5 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 15, 'eval element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 6;
						$elts[_ETS_EVAL . ':' . $i] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						--$i;
					// {safe:
					} elseif ($c1 == 's' && $c2 == 'a' && $c3 == 'f' && $c4 == 'e' && $c5 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 16, 'safe eval element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 6;
						$elts[_ETS_SAFE . ':' . $i] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						--$i;
					// {when:
					} elseif ($c1 == 'w' && $c2 == 'h' && $c3 == 'e' && $c4 == 'n' && $c5 == ':') {
						// of of whentest
						if ($ptype == _ETS_CHOOSE) {
							$mode = _ETS_DATA;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 6;
							$index = _ETS_WHENTEST . ':' . $i;
							$elts['when'][$index]['test'] = $this->parse(_ETS_CODE,     $i, $line, $ncontent, $content);
							$elts['when'][$index]['true'] = $this->parse(_ETS_WHENTEST, $i, $line, $ncontent, $content);
						}
						// of whenval
						elseif ($ptype == _ETS_CHOOSEVAR) {
							$mode = _ETS_VALUE;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = _ETS_WHENVAL;
							switch ($c6) {
								case '\'':	$quotetype = 1; $i += 6; break;
								case '"':	$quotetype = 2; $i += 6; break;
								default:	$quotetype = 0; $i += 5; break;
							}
						} else {
							$this->error(0, 17, 'when element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {if:
					} elseif ($c1 == 'i' && $c2 == 'f' && $c3 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 18, 'if element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 4;
						$index = _ETS_IF . ':' . $i;
						$elts[$index]['test'] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						$elts[$index]['true'] = $this->parse(_ETS_IF, $i, $line, $ncontent, $content);
					// {repeat:
					} elseif ($c1 == 'r' && $c2 == 'e' && $c3 == 'p' && $c4 == 'e' && $c5 == 'a' && $c6 == 't' && $c7 == ':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 19, 'repeat element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_DATA;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = NULL;
						$i += 8;
						$index = _ETS_REPEAT . ':' . $i;
						$elts[$index]['loops'] = $this->parse(_ETS_CODE, $i, $line, $ncontent, $content);
						$elts[$index]['repeated'] = $this->parse(_ETS_REPEAT, $i, $line, $ncontent, $content);
					// {rss:
					} elseif ($c1 =='r' && $c2 =='s' && $c3=='s' && $c4==':') {
						if ($ptype & _ETS_GROUP1) {
							$this->error(0, 19, 'rss element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cname = $cvalue = '';
						$nametype = _ETS_RSS;
						$i += 4;
					// simple tag with absolute path
					} elseif ($c1 == '/' && $c2 == '/') {
						if ($ptype & _ETS_GROUP3) {
							$this->error(0, 20, 'simple tag element with absolute path', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
						$this->store_text($elts, $i, $ptype, $ntext, $ctext);
						$mode = _ETS_NAME;
						$ntext = $nname = $nvalue = $nspace = 0;
						$ctext = $cvalue = '';
						$cname = '//';
						$nametype = _ETS_TAG;
						$i += 2;
					// other simple tag
					} elseif ($c1 != '/' && !$this->is_space($c1)) {
						// {else
						if ($c1 == 'e' && $c2 == 'l' && $c3 == 's' && $c4 == 'e' && ($this->is_space($c5) || $c5 == '}' )) {
							if ($ptype & _ETS_CHOOSEs) {
								$mode = _ETS_NAME;
								$ntext = $nvalue = $nspace = 0;
								$nname = 1;
								$ctext = $cvalue = '';
								$cname = 'else';
								$nametype = _ETS_TAG;
								$i += 4;
							} else {
								$this->error(0, 21, 'else element', $line, $ptype);
								$this->skip = TRUE;
								return array();
							}
						} elseif ($ptype & _ETS_GROUP3) {
							$this->error(0, 22, 'simple tag element', $line, $ptype);
							$this->skip = TRUE;
							return array();
						// other
						} else {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_NAME;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = _ETS_TAG;
						}
					// {/loop     (formerly "{/mask")
					} elseif ($c1 == '/' && $c2 == 'l' && $c3 == 'o' && $c4 == 'o' && $c5 == 'p') {
						if ($ptype == _ETS_TEMPLATE) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						} else {
							$this->error(1, 23, 'loop', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/mask
					} elseif ($c1 == '/' && $c2 == 'm' && $c3 == 'a' && $c4 == 's' && $c5 == 'k') {
						if ($ptype == _ETS_TEMPLATE) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						} else {
							$this->error(1, 23, 'mask', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/set
					} elseif ($c1 == '/' && $c2 == 's' && $c3 == 'e' && $c4 == 't') {
						if ($ptype & _ETS_SETs) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 4;
						} else {
							$this->error(1, 24, 'set', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/mis
					} elseif ($c1 == '/' && $c2 == 'm' && $c3 == 'i' && $c4 == 's') {
						if ($ptype & _ETS_MISs) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 4;
						} else {
							$this->error(1, 25, 'mis', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/php
					} elseif ($c1 == '/' && $c2 == 'p' && $c3 == 'h' && $c4 == 'p') {
						if ($ptype == _ETS_PHP) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 4;
						} else {
							$this->error(1, 26, 'PHP', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/if
					} elseif ($c1 == '/' && $c2 == 'i' && $c3 == 'f') {
						if ($ptype == _ETS_IF) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 3;
						} else {
							$this->error(1, 27, 'if', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/choose
					} elseif ($c1 == '/' && $c2 == 'c' && $c3 == 'h' && $c4 == 'o' && $c5 == 'o' && $c6 == 's' && $c7 == 'e') {
						if ($ptype & _ETS_CHOOSEs) {
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 7;
						} else {
							$this->error(1, 28, 'choose', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/call
					} elseif ($c1 == '/' && $c2 == 'c' && $c3 == 'a' && $c4 == 'l' && $c5 == 'l') {
						if ($ptype == _ETS_CALL) {
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						} else {
							$this->error(1, 29, 'call', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/arg
					} elseif ($c1 == '/' && $c2 == 'a' && $c3 == 'r' && $c4 == 'g') {
						if ($ptype == _ETS_ARG) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 4;
						} else {
							$this->error(1, 30, 'arg', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/when
					} elseif ($c1 == '/' && $c2 == 'w' && $c3 == 'h' && $c4 == 'e' && $c5 == 'n') {
						// of when val
						if ($ptype == _ETS_WHENVAL) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						// of when test
						} elseif ($ptype == _ETS_WHENTEST) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						} else {
							$this->error(1, 31, 'when', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/else
					} elseif ($c1 == '/' && $c2 == 'e' && $c3 == 'l' && $c4 == 's' && $c5 == 'e') {
						if ($ptype == _ETS_ELSE) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 5;
						} else {
							$this->error(1, 32, 'else', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/repeat
					} elseif ($c1 == '/' && $c2 == 'r' && $c3 == 'e' && $c4 == 'p' && $c5 == 'e' && $c6 == 'a' && $c7 == 't') {
						if ($ptype == _ETS_REPEAT) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 7;
						} else {
							$this->error(1, 33, 'repeat', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/rss
					} elseif ($c1 == '/' && $c2 =='r' && $c3 == 's' && $c4 == 's') {
						if ($ptype == _ETS_RSS) {
							$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							$i += 4;
						} else {
							$this->error(1, 35, 'rss', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// {/ (simplified closing tag)
					} elseif ($c1 == '/' && ($c2 == '}' || $this->is_space($c2))) {
						if ($ptype != _ETS_ROOT) {
							if (!($ptype & _ETS_GROUP4)) {
								$this->store_text($elts, $i, $ptype, $ntext, $ctext);
							}
							$mode = _ETS_CLOSING_TAG;
							$ntext = $nname = $nvalue = $nspace = 0;
							$ctext = $cname = $cvalue = '';
							$nametype = NULL;
							++$i;
						} else {
							$this->error(1, 34, '', $line, $ptype);
							$this->skip = TRUE;
							return array();
						}
					// text
					} elseif (!($ptype & _ETS_GROUP3)) {
						$ctext .= $c0;
						$ntext = 1;
					}
				// end of code element
				} elseif ($c0 == '}' && $ptype == _ETS_CODE) {
					$this->store_text($elts, $i, $ptype, $ntext, $ctext);
					++$i;
					return $elts;
				// escape } with \} in code acquisition
				} elseif ($c0 == '\\' && $c1 == '}' && $ptype == _ETS_CODE) {
					$ctext .= '}';
					$ntext = 1;
					++$i;
				// no text in choosevar element
				} elseif ($ptype == _ETS_CHOOSE && !$is_space0) {
					$this->error(2, 35, "unexpected character '$c0' in choose element", $line);
					$this->skip = TRUE;
					return array();
				// no text in choose element
				} elseif ($ptype == _ETS_CHOOSEVAR && !$is_space0) {
					$this->error(2, 36, "unexpected character '$c0' in choose-variable element", $line);
					$this->skip = TRUE;
					return array();
				// no text in call element
				} elseif ($ptype == _ETS_CALL && !$is_space0) {
					$this->error(2, 37, "unexpected character '$c0' in call element", $line);
					$this->skip = TRUE;
					return array();
				// text
				} elseif ($ptype != _ETS_ROOT) {
					$ctext .= $c0;
					$ntext = 1;
				}
			// name acquisition
			} elseif ($mode == _ETS_NAME) {
				// end of name acquisition
				if ($c0 == '}' && $nname == 1) {
					// reduce
					if ($nametype == _ETS_REDUCE) {
						if (!isset($elts['0reduce']) || $elts['0reduce'] == _ETS_REDUCE_NULL) {
							if (!$this->store_reduce($elts, $cname)) {
								$this->error(7, 38, "invalid value $cname for reduce element", $line);
							}
						}
					// template
					} elseif ($nametype == _ETS_TEMPLATE) {
						++$i;
						if ($ptype != _ETS_ROOT) {
							$this->store_node($elts, _ETS_TEMPLATE, $i, $line, $cname, NULL, $ncontent, $content);
						} elseif (isset($elts[$cname])) {
							$this->error(2, 39, "template $cname already defined", $line);
							$this->skip = TRUE;
							return $elts; //array();
						} else {
							$elts[$cname] = $this->parse(_ETS_TEMPLATE, $i, $line, $ncontent, $content);
						}
					// set
					} elseif ($nametype == _ETS_SET) {
						++$i;
						$this->store_node($elts, _ETS_SET, $i, $line, $cname, NULL, $ncontent, $content);
					// mis
					} elseif ($nametype == _ETS_MIS) {
						++$i;
						$this->store_node($elts, _ETS_MIS, $i, $line, $cname, NULL, $ncontent, $content);
					// tag?
					} elseif ($nametype == _ETS_TAG) {
						// php
						if ($cname == 'php') {
							++$i;
							$elts[_ETS_PHP . ':' . $i] = $this->parse(_ETS_PHP, $i, $line, $ncontent, $content);
						// choose
						} elseif ($cname == 'choose') {
							++$i;
							$elts[_ETS_CHOOSE . ':' . $i] = $this->parse(_ETS_CHOOSE, $i, $line, $ncontent, $content);
						// else
						} elseif (($ptype == _ETS_CHOOSE || $ptype == _ETS_CHOOSEVAR) && $cname == 'else') {
							if (isset($elts['else'])) {
								$this->error(2, 40, 'else element already exists in ' . $this->elt_label($ptype), $line);
								$this->skip = TRUE;
								return array();
							} else {
								++$i;
								$elts['else'] = $this->parse(_ETS_ELSE, $i, $line, $ncontent, $content);
							}
						// tag!
						} else {
							$this->store_leaf($elts, _ETS_TAG, $i, $cname);
						}
					// choose var
					} elseif ($nametype == _ETS_CHOOSEVAR) {
						++$i;
						$this->store_node($elts, _ETS_CHOOSEVAR, $i, $line, $cname, NULL, $ncontent, $content);
					// arg
					} elseif ($nametype == _ETS_ARG) {
						++$i;
						$this->store_node($elts, _ETS_ARG, $i, $line, $cname, NULL, $ncontent, $content);
					}
					$mode = _ETS_DATA;
				// space in name acquisition
				} elseif ($is_space0) {
					if ($nname == 1) {
						$nspace = 1;
					} else {
						$this->error(2, 41, "unexpected space before name", $line);
						$this->skip = TRUE;
						return array();
					}
				// start of value acquisition
				} elseif ($c0 == ':' && $nname == 1 && ($nametype == _ETS_SET || $nametype == _ETS_MIS)) {
					$cvalue = '';
					$nvalue = 0;
					$nspace = 0;
					$mode = _ETS_VALUE;
					switch ($c1) {
						case '\'':	$quotetype = 1; ++$i; break;
						case '"':	$quotetype = 2; ++$i; break;
						default:	$quotetype = 0;       break;
					}
				// start of second name acquisition
				} elseif ($c0 == ':' && $nametype == _ETS_TAG && $nname == 1 && $nvalue == 0) {
					++$i;
					$this->store_node($elts, _ETS_ALT_TAG, $i, $line, $cname, $cvalue, $ncontent, $content, TRUE);
					--$i;
					$mode = _ETS_DATA;
					$ntext = $nname = $nvalue = $nspace = 0;
					$ctext = $cname = $cvalue = '';
					$nametype = NULL;
				// data after trailing spaces
				} elseif ($nspace == 1) {
					$this->error(2, 42, "unexpected character '$c0' after spaces in name", $line);
					$this->skip = TRUE;
					return array();
				// name acquisition
				} elseif (($nname == 0 && preg_match('/[a-zA-Z_\.\x7f-\xff]/', $c0)) || ($nname == 1 && preg_match('/[\[\]\'\/a-zA-Z0-9_\.\x7f-\xff]/', $c0))) {
					$cname .= $c0;
					$nname = 1;
				// absolute path at the beginning of name acquisition
				} elseif ($c0 == '/' && $c1 == '/' && $nname == 0) {
					$cname = '//';
					++$i;
				// error in name acquisition
				} else {
					$this->error(2, 43, "unexpected character '$c0' in name", $line);
					$this->skip = TRUE;
					return array();
				}
			// end of closing tag
			} elseif ($mode == _ETS_CLOSING_TAG) {
				if ($c0 == '}') {
					$this->store_text($elts, $i, $ptype, $ntext, $ctext);
					return $elts;
				} elseif (!$is_space0) {
					$this->error(2, 44, "unexpected character '$c0' in closing tag", $line);
					$this->skip = TRUE;
					return array();
				}
			// value acquisition
			} elseif ($mode == _ETS_VALUE) {
				// end of value acquisition
				if ($c0 == '}' && $nvalue == 1 && ($quotetype == 0 || $nspace == 1 || $nspace == 2)) {
					if ($nametype == _ETS_SET) {
						++$i;
						$this->store_node($elts, _ETS_SETVAL, $i, $line, $cname, $cvalue, $ncontent, $content);
					} elseif ($nametype == _ETS_MIS) {
						++$i;
						$this->store_node($elts, _ETS_MISVAL, $i, $line, $cname, $cvalue, $ncontent, $content);
					} elseif ($nametype == _ETS_WHENVAL) {
						++$i;
						$elts['when'][_ETS_WHENVAL . ':' . $i . '::' . $cvalue] = $this->parse(_ETS_WHENVAL, $i, $line, $ncontent, $content);
					}
					$mode = _ETS_DATA;
				// no more character after space for single quoted string
				} elseif ($c0 == '\'' && $quotetype == 1 && $nspace == 0) {
					$nspace = 2;
				// no more character after space for double quoted string
				} elseif ($c0 == '"' && $quotetype == 2 && $nspace == 0) {
					$nspace = 2;
				// space in value acquisition
				} elseif ($is_space0) {
					if ($nvalue == 0 && $quotetype == 0) { // no value without quote
						$this->error(2, 45, "unexpected space at the beginning of a value", $line);
						$this->skip = TRUE;
						return array();
					} else { // value found or with quotes
						if ($quotetype == 0) { // no quote
							$nspace = 1;
						} else { // with quotes
							if ($nspace == 2) { // after quotes
								$nspace = 1;
							} else {			// in quotes
								$cvalue .= $c0;
								$nvalue = 1;
							}
						}
					}
				// escape } with \} in value acquisition without quote
				} elseif ($c0 == '\\' && $c1 == '}' && $nspace == 0) {
					$cvalue .= '}';
					$nvalue = 1;
					++$i;
				// espace ' with \' in value acquisition for single quoted string
				} elseif ($c0 == '\\' && $c1 == '\'' && $quotetype == 1 && $nspace == 0) {
					$cvalue .= '\'';
					$nvalue = 1;
					++$i;
				// espace " with \" in value acquisition for single quoted string
				} elseif ($c0 == '\\' && $c1 == '"' && $quotetype == 2 && $nspace == 0) {
					$cvalue .= '"';
					$nvalue = 1;
					++$i;
				// value acquisition
				} elseif ($nspace == 0) {
					$cvalue .= $c0;
					$nvalue = 1;
				// error in value acquisition
				} else {
					$this->error(2, 46, "unexpected character '$c0' in value", $line);
					$this->skip = TRUE;
					return array();
				}
			// comment
			} elseif ($mode == _ETS_COMMENT) {
				// nested
				if ($c0 == '{' && $c1 == '*') {
					++$i;
					++$nspecial;
				// end
				} elseif ($c0 == '*' && $c1 == '}') {
					++$i;
					--$nspecial;
					// last end
					if ($nspecial == 0) {
						$mode = _ETS_DATA;
					}
				}
			// cdata
			} elseif ($mode == _ETS_CDATA) {
				// nested
				if ($c0 == '{' && $c1 == '#') {
					++$i;
					++$nspecial;
				// end
				} elseif ($c0 == '#' && $c1 == '}') {
					++$i;
					--$nspecial;
					// last end
					if ($nspecial == 0) {
						$mode = _ETS_DATA;
					}
				// text acquisition
				} else {
					switch ($c0) {
						case "\n": $ctext .= "\1n\1"; break;
						case "\r": $ctext .= "\1r\1"; break;
						case "\t": $ctext .= "\1t\1"; break;
						case " " : $ctext .= "\1s\1"; break;
						default  : $ctext .= $c0; break;
					}
					$ntext = 1;
				}
			}
		}
		// end
		if ($mode & _ETS_GROUP0) {
			$this->error(3, 47, '', $saveline, $mode);
			$this->skip = TRUE;
			return array();
		}
		if ($ptype == _ETS_ROOT_EVAL) {
			$this->store_text($elts, $i, $ptype, $ntext, $ctext);
		} elseif ($ptype != _ETS_ROOT) {
			$this->error(4, 48, '', $saveline, $ptype);
			$this->skip = TRUE;
			return array();
		}
		return $elts;
	}
    /**
     * Merge two template trees
     */
	function masktree_merge($masktree1, $masktree2, $maskname)
	{
		$merged = array_merge($masktree1, $masktree2);
		if (count($merged) < count($masktree1) + count($masktree2)) {
			$keys1 = array_keys($masktree1);
			$keys2 = array_keys($masktree2);
			$keysm = array_merge($keys1, $keys2);
			$keysc = array_count_values($keysm);
			foreach ($keysc as $keyn => $keyc) {
				if ($keyc > 1) {
					if ($keyn == '0reduce') {
						$this->error(6, 49, 'reduce element already used');
					} elseif ($keyn != '0include') {
						$this->error(16, 60, "template $keyn already defined in <b>$maskname</b>");
					}
				}
			}
		}
		return $merged;
	}

	/*****   C O N T E N T   *****/
    /**
     * Read a stream and return its content or FALSE if fail
     */
	function read_content()
	{
		if ($this->external_source_read) {
			$fct = $this->source_read_name;
			return $fct($this->container);
		} else {
			$content = FALSE;
			if ($handle = @fopen($this->container, 'rb')) {
				$size = @filesize($this->container);
				$content = @fread($handle, $size);
				fclose($handle);
			}
			return $content;
		}
	}
    /**
     * Return container content or masktree in container content
     */
	function read_container($container, $parse)
	{
		$this->container = $container = trim($container);
		$this->skip = FALSE;
		// content must be parsed...
		if ($parse != _ETS_TEXT) {
			// null containers are avoid
			if ($this->container === '' || strtoupper($this->container) == 'NULL') {
				return array();
			}
			// check if container is already used
			if ($parse == _ETS_ROOT) {
				if (isset($this->containers[$container])) {
					$this->error(16, 50, "container $container already used");
					return array();
				}
			}
			// cache handlers are available...
			if ($this->external_cache_read && $this->external_cache_write) {
				// the cache exists and is not obsolete
				$fct = $this->cache_read_name;
				if ($envelope = $fct($this->container)) {
					// the cache is a valid envelope
					if (isset($envelope) && $envelope{0} == 'E' && $envelope{1} == 'T' && $envelope{2} == 'S' && $envelope{3} == "\1") {
						$masktree = unserialize(substr($envelope, 4));
						// the envelope contains valid templates
						if ($masktree && is_array($masktree)) {
							$this->containers[$container] = TRUE;
							// the container calls other containers
							if (isset($masktree['0include'])) {
								foreach ($masktree['0include'] as $includedname) {
									$included = $this->read_container($includedname, _ETS_ROOT);
									if ($included === FALSE) {
										$this->error(13, 51, $includedname);
									} else {
										$masktree = $this->masktree_merge($masktree, $included, $includedname);
									}
								}
							}
							return $masktree;
						}
					}
				}
				// refresh the cache
				$content = $this->read_content();
				if ($content === FALSE) {
					return FALSE;
				}
				$this->containers[$container] = TRUE;
				$i = 0;
				$line = 1;
				$temp = strlen($content);
				$masktree = $this->parse($parse, $i, $line, $temp, "$content       ");
				$fct = $this->cache_write_name;
				$fct($this->container, "ETS\1" . serialize($masktree));
				return $masktree;
			// .. or not
			} else {
				$content = $this->read_content();
				if ($content === FALSE) {
					return FALSE;
				}
				$this->containers[$container] = TRUE;
				$i = 0;
				$line = 1;
				return $this->parse(
					$parse,
					$i,
					$line,
					(string) strlen($content),
					"$content       ");
			}
		// .. or not
		} else {
			// null containers are avoid
			if ($this->container === '' || strtoupper($this->container) == 'NULL') {
				return '';
			}
			return $this->read_content();
		}
	}
    /**
     * Read containers then parse their content to build a template tree
     */
	function parse_containers($containers)
	{
		// Construct an array of container names
		if (!is_array($containers)) {
			$containers = explode(',', $containers);
		}
		// Parse each container
		foreach ($containers as $container) {
			$masktree = $this->read_container($container, _ETS_ROOT);
			if ($masktree === FALSE) {
				$this->error(11, 52, $this->container);
			} else {
				$this->masktree = $this->masktree_merge($this->masktree, $masktree, $container);
			}
		}
	}
	/*****   M A T C H I N G   *****/
    /**
     * Retrieve the value of a string representation of a variable
     */
	function get_value($parent, $varname)
	{
		if (isset($parent->$varname)) {
			return $parent->$varname;
		} else {
			$elements = explode('[', $varname);
			if (count($elements) == 1) {
				return NULL;
			} else {
				$vartest = $parent;
				foreach ($elements as $elementid => $element) {
					if ($elementid == 0) {
						$vartest = $parent->$element;
						if (!isset($vartest)) {
							return NULL;
						}
					} else {
						$index = substr($element, 0, -1);
						if ($index == '_first') {
							$keys = array_keys($vartest);
							$index = $keys[0];
						} elseif ($index == '_last') {
							$keys = array_keys($vartest);
							$index = $keys[count($keys) - 2];
						}
						if (!isset($vartest[$index])) {
							return NULL;
						} else {
							$vartest = $vartest[$index];
						}
					}
				}
			}
			return $vartest;
		}
	}
    /**
     * Define the type of the current data, the direction and parent property
     */
	function get_datatype($maskname, $carray, $incode, &$cindex, &$ckey, &$clast, &$datatree, &$datatype, &$direction, &$currentdata, $safemode)
	{
		// . from root
		if ($maskname == '//' && !$safemode) {
			$datatype = _ETS_COMPLEX;
			$currentdata = $this->datatree;
			if ($direction == _ETS_FORWARD) {
				if (is_array($currentdata)) {
					$currentdata['_parent'] = &$datatree;
				} elseif (is_object($currentdata)) {
					$currentdata->_parent = &$datatree;
				}
			}
		// . parent
		} elseif (($maskname == '..' || $maskname == '_parent') && !$safemode) {
			if (is_array($datatree)) {
				$datatype = _ETS_COMPLEX;
				$currentdata = $datatree['_parent'];
				$direction = _ETS_BACKWARD;
			} elseif (is_object($datatree)) {
				$datatype = _ETS_COMPLEX;
				$currentdata = $datatree->_parent;
				$direction = _ETS_BACKWARD;
			} else {
				$datatype = _ETS_MISSING;
				$currentdata = NULL;
				$direction = _ETS_FORWARD;
			}
		// . first sibling in an array
		} elseif ($maskname == '_start') {
			$direction = _ETS_FORWARD;
			$keys = array_keys($carray);
			$cindex = 0;
			if (isset($keys[$cindex]) && isset($carray[$keys[$cindex]])) {
				$ckey = $keys[$cindex];
				$clast = ($cindex == count($carray) - 2);
				$currentdata = $carray[$ckey];
				$datatype = _ETS_COMPLEX;
			} else {
				$currentdata = NULL;
				$datatype = _ETS_MISSING;
			}
		// . previous sibling in an array
		} elseif ($maskname == '_previous') {
			$direction = _ETS_FORWARD;
			$keys = array_keys($carray);
			--$cindex;
			if (isset($keys[$cindex]) && isset($carray[$keys[$cindex]])) {
				$ckey = $keys[$cindex];
				$clast = FALSE;
				$currentdata = $carray[$ckey];
				$datatype = _ETS_COMPLEX;
			} else {
				$currentdata = NULL;
				$datatype = _ETS_MISSING;
			}
		// . next sibling in an array
		} elseif ($maskname == '_next') {
			$direction = _ETS_FORWARD;
			$keys = array_keys($carray);
			++$cindex;
			if (isset($keys[$cindex]) && isset($carray[$keys[$cindex]]) && $keys[$cindex] != '_parent') {
				$ckey = $keys[$cindex];
				$clast = ($cindex == count($carray) - 2);
				$currentdata = $carray[$ckey];
				$datatype = _ETS_COMPLEX;
			} else {
				$currentdata = NULL;
				$datatype = _ETS_MISSING;
			}
		// . last sibling in an array
		} elseif ($maskname == '_end') {
			$direction = _ETS_FORWARD;
			$keys = array_keys($carray);
			$cindex = count($keys) - 2;
			if (isset($keys[$cindex]) && isset($carray[$keys[$cindex]])) {
				$ckey = $keys[$cindex];
				$clast = TRUE;
				$currentdata = $carray[$ckey];
				$datatype = _ETS_COMPLEX;
			} else {
				$currentdata = NULL;
				$datatype = _ETS_MISSING;
			}
		// . real data
		} else {
			// retrieve the value
			$currentdata = $this->get_value($datatree, $maskname);
			if (isset($currentdata)) {
				if (is_scalar($currentdata)) {
					$direction = _ETS_FORWARD;
					if ($currentdata === FALSE && !$incode) {
						$datatype = _ETS_MISSING;
					} elseif ($currentdata === '' && !$incode) {
						$datatype = _ETS_MISSING;
					} else {
						$datatype = _ETS_SCALAR;
					}
				} elseif (is_object($currentdata) && count(get_object_vars($currentdata)) > 0) {
					$datatype = _ETS_COMPLEX;
					if ($direction == _ETS_FORWARD && !$safemode) {
						$currentdata->_parent = &$datatree;
					}
				} elseif (is_array($currentdata) && count($currentdata) > 0) {
					$datatype = _ETS_COMPLEX;
					if ($direction == _ETS_FORWARD && !$safemode) {
						$currentdata['_parent'] = &$datatree;
					}
				} else {
					$direction = _ETS_FORWARD;
					$datatype = _ETS_MISSING;
				}
			} else {
				$direction = _ETS_FORWARD;
				$datatype = _ETS_MISSING;
			}
		}
	}
	/**
	 * Add system variables to an object
	 */
	function add_system_var(&$datatree, $index, $last, $key)
	{
		$datatree->_key = $key;
		$datatree->_index = $index;
		$datatree->_rank = $index + 1;
		$datatree->_odd = $datatree->_not_even = (1 == $datatree->_rank % 2);
		$datatree->_even = $datatree->_not_odd = (0 == $datatree->_rank % 2);
		$datatree->_first = (0 == $index);
		$datatree->_middle = !$datatree->_first && !$last;
		$datatree->_last = $last;
		$datatree->_not_first = !$datatree->_first;
		$datatree->_not_last = !$last;
		$datatree->_not_middle = !$datatree->_middle;
	}
    /**
     * Excerpt info defined in the index of each node of the template tree
     */
	function parse_info($info)
	{
		$elements = explode(':', $info);
		$count = count($elements);
		if ($count > 4) {
			for ($i = 4; $i < $count; ++$i) {
				$elements[3] .= ':' . $elements[$i];
			}
		} else {
			$elements = array_pad($elements, 4, '');
		}
		return array($elements[0], $elements[2], $elements[3]);
	}
    /**
     * Protect non printable characters
     */
	function protect_spaces($data)
	{
		$data = str_replace("\n", "\1n\1", $data);
		$data = str_replace("\r", "\1r\1", $data);
		$data = str_replace("\t", "\1t\1", $data);
		return  str_replace(" " , "\1s\1", $data);
	}
    /**
     * Recursively match the template tree with the data tree
     */
	function build_mask($datatree, $masktree, $direction = _ETS_FORWARD, $index = -1, $last = FALSE, $key = '', $incode = FALSE, $carray = array(), $safemode = FALSE)
	{
		$built = array();
		// array
		if (isset($datatree) && is_array($datatree) && count($datatree) > 0) {
			$lindex = 0;
			$count = count($datatree) - 1; // don't count parent element
			foreach ($datatree as $dk => $dv) {
				if (!is_scalar($dv) && $dk !== '_parent') {
					if (is_object($dv)) {
						// For some reason, PHP 5 is throwing 500's when the parent datatree has an array in the object. Retarded.
						if (version_compare(phpversion(), '5.0.0', '>')){
						$my_datatree = new stdClass;
							foreach($datatree['_parent'] as $k => $v){
								if(is_array($v)){continue;}
								$my_datatree->{$k} = $v;
							}
							$dv->_parent = $my_datatree;
						} else {
							$dv->_parent = &$datatree['_parent'];
						}
					} elseif (is_array($dv)) {
						$dv['_parent'] = &$datatree['_parent'];
					}
					$built[] = $this->build_mask($dv, $masktree, $direction, $lindex, ($count == $lindex + 1), $dk, $incode, $datatree);
				}
				++$lindex;
			}
			return implode('', $built);
		}
		// define system variables
		if (is_object($datatree) && $index > -1 && !isset($datatree->_key)) {
			$this->add_system_var($datatree, $index, $last, $key);
		}
		// loop through each child element
		foreach ($masktree as $maskinfo => $child) {
			// save array information
			$cindex = $index;
			$clast = $last;
			$ckey = $key;
			// retrieve info from index
			list($masktype, $maskname, $maskvalue) = $this->parse_info($maskinfo);
			// in safe mode, only a subset of elements are available
			if ($safemode) {
				// retrieve datatype and direction and define parent property if necessary
				$this->get_datatype($maskname, $carray, $incode, $cindex, $ckey, $clast, $datatree, $datatype, $direction, $currentdata, TRUE);
				switch ($masktype) {
					// content data element
					case _ETS_TEXT:
						$built[] = $child;
						break;
					// simple tag element are only used to place scalar values
					case _ETS_TAG:
						if ($datatype == _ETS_SCALAR) {
							$built[] = $this->protect_spaces($currentdata);
						}
						break;
					// set element
					case _ETS_SET:
						if ($datatype != _ETS_MISSING) {
							$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray, TRUE);
						}
						break;
					// mis element
					case _ETS_MIS:
						if ($datatype == _ETS_MISSING) {
							$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray, TRUE);
						}
						break;
					// set val element
					case _ETS_SETVAL:
						if ($datatype == _ETS_SCALAR) {
							if ($currentdata == $maskvalue) {
								$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray, TRUE);
							}
						}
						break;
					// mis val element
					case _ETS_MISVAL:
						if ($datatype == _ETS_MISSING || ($datatype == _ETS_SCALAR && $currentdata != $maskvalue)) {
							$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray, TRUE);
						}
						break;
					// template element
					case _ETS_TEMPLATE:
						if ($datatype == _ETS_SCALAR) {
							$built[] = $this->build_mask($datatree, $child, _ETS_FORWARD, $cindex, $clast, $ckey, $incode, $carray, TRUE);
						} elseif ($datatype == _ETS_COMPLEX) {
							$built[] = $this->build_mask($currentdata, $child, _ETS_FORWARD, $cindex, $clast, $ckey, $incode, $carray, TRUE);
						}
						break;
					// other element: error
					default:
						$this->error(15, 53, '', 0, $masktype);
						break;
				}
			// normal mode
			} else {
				switch ($masktype) {
					// content data element
					case _ETS_TEXT:
						$built[] = $child;
						break;
					// php element
					case _ETS_PHP:
						$return = NULL;
						@eval('$return=(string)(' . $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray) . ');');
						if (isset($return)) {
							$built[] = $return;
						}
						break;
					// const element
					case _ETS_CONST:
						$template = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray);
						$return = NULL;
						@eval('$return=(string)(' . $template . ');');
						if (isset($return) && isset($this->masktree[$return])) {
							$built[] = $this->build_mask($datatree, $this->masktree[$return], $direction, $cindex, $clast, $ckey, $incode, $carray);
						}
						break;
					// call element
					case _ETS_CALL:
						$template = $this->build_mask($datatree, $child['template'], $direction, $cindex, $clast, $ckey, TRUE, $carray);
						$return = NULL;
						@eval('$return=(string)(' . $template . ');');
						if (isset($return) && isset($this->masktree[$return]) && isset($child['args'])) {
							$argdatatree = $datatree;
							foreach ($child['args'] as $arginfo => $argchild) {
								list($argtype, $argname, $argvalue) = $this->parse_info($arginfo);
								$argdatatree->$argname = $this->build_mask($datatree, $argchild, $direction, $cindex, $clast, $ckey, $incode, $carray);
							}
							$built[] = $this->build_mask($argdatatree, $this->masktree[$return], $direction, $cindex, $clast, $ckey, $incode, $carray);
						}
						break;
					// include element
					case _ETS_PLACE:
						$container = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray);
						$return = NULL;
						@eval('$return=(string)(' . $container . ');');
						if (isset($return)) {
							$included = $this->read_container($return, _ETS_ROOT);
							if ($included === FALSE) {
								$this->error(13, 54, $return);
							} else {
								$this->masktree = $this->masktree_merge($this->masktree, $included, $maskvalue);
							}
						}
						break;
					// insert element
					case _ETS_INSERT:
						$container = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray);
						$return = NULL;
						@eval('$return=(string)(' . $container . ');');
						if (isset($return)) {
							$inserted = $this->read_container($return, _ETS_TEXT);
							if ($inserted === FALSE) {
								$this->error(12, 55, $return);
							} else {
								$built[] = $inserted;
							}
						}
						break;
					// eval and safe elements or include
					case _ETS_EVAL:
					case _ETS_SAFE:
					case _ETS_INCLUDE:
						$container = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray);
						$return = NULL;
						@eval('$return=(string)(' . $container . ');');
						if (isset($return)) {
							$evaluated = $this->read_container($return, _ETS_ROOT_EVAL);
							if ($evaluated === FALSE) {
								$this->error(14, 56, $return);
							} else {
								$built[] = $this->build_mask($datatree, $evaluated, $direction, $cindex, $clast, $ckey, $incode, $carray, $masktype == _ETS_SAFE);
							}
						}
						break;
					// other types of element
					default:
						// retrieve datatype and direction and define parent property if necessary
						$this->get_datatype($maskname, $carray, $incode, $cindex, $ckey, $clast, $datatree, $datatype, $direction, $currentdata, $safemode);
						switch ($masktype) {
							// simple tag element
							case _ETS_TAG:
								if ($datatype == _ETS_SCALAR && isset($this->masktree[$maskname])) {
									$built[] = $this->build_mask($datatree, $this->masktree[$maskname], $direction, $cindex, $clast, $ckey, $incode, $carray);
								} elseif ($datatype == _ETS_SCALAR) {
									if ($incode) {
										if ($currentdata === TRUE) {
											$built[] = 'TRUE';
										} elseif ($currentdata === FALSE) {
											$built[] = 'FALSE';
										} elseif (is_string($currentdata)) {
											$built[] = '"' . addcslashes($currentdata, "\0..\31\"") . '"';
										} else {
											$built[] = $currentdata;
										}
									} else {
										$built[] = $this->protect_spaces($currentdata);
									}
								} elseif ($datatype == _ETS_COMPLEX && isset($this->masktree[$maskname])) {
									$built[] = $this->build_mask($currentdata, $this->masktree[$maskname], $direction, $cindex, $clast, $ckey, $incode, $carray);
								} elseif ($datatype == _ETS_MISSING && $incode) {
									$built[] = 'NULL';
								}
								break;
							// alternate tag element
							case _ETS_ALT_TAG:
								$template = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, TRUE, $carray);
								$return = NULL;
								@eval('$return=(string)(' . $template . ');');
								if (isset($return)) {
									if ($datatype == _ETS_SCALAR && isset($this->masktree[$return])) {
										$built[] = $this->build_mask($datatree, $this->masktree[$return], $direction, $cindex, $clast, $ckey, $incode, $carray);
									} elseif ($datatype == _ETS_COMPLEX && isset($this->masktree[$return])) {
										$built[] = $this->build_mask($currentdata, $this->masktree[$return], $direction, $cindex, $clast, $ckey, $incode, $carray);
									} elseif ($datatype == _ETS_SCALAR) {
										$built[] = $currentdata;
									}
								}
								break;
							// template element
							case _ETS_TEMPLATE:
								if ($datatype == _ETS_SCALAR) {
									$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								} elseif ($datatype == _ETS_COMPLEX) {
									$built[] = $this->build_mask($currentdata, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								} elseif ($datatype == _ETS_MISSING && $incode) {
									$built[] = $this->build_mask($currentdata, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// if element
							case _ETS_IF:
								$test = $this->build_mask($datatree, $child['test'], $direction, $cindex, $clast, $ckey, TRUE, $carray);
								$return = NULL;
								@eval('$return=(bool)(' . $test . ');');
								if ($return === TRUE) {
									$built[] = $this->build_mask($datatree, $child['true'], $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// repeat element
							case _ETS_REPEAT:
								$loop = $this->build_mask($datatree, $child['loops'], $direction, $cindex, $clast, $ckey, TRUE, $carray);
								$return = NULL;
								@eval('$return=(int)(' . $loop . ');');
								for ($i = 0; $i < $return; ++$i) {
									if (is_object($datatree)) {
										$datatree->_count = $i + 1;
									}
									$built[] = $this->build_mask($datatree, $child['repeated'], $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// choose element
							case _ETS_CHOOSE:
								$notfound = TRUE;
								if (isset($child['when'])) {
									foreach ($child['when'] as $grandchild)  {
										$test = $this->build_mask($datatree, $grandchild['test'], $direction, $cindex, $clast, $ckey, TRUE, $carray);
										$return = NULL;
										@eval('$return=(bool)(' . $test . ');');
										if ($return === TRUE) {
											$notfound = FALSE;
											$built[] = $this->build_mask($datatree, $grandchild['true'], $direction, $cindex, $clast, $ckey, $incode, $carray);
											break;
										}
									}
								}
								if ($notfound && isset($child['else'])) {
									$built[] = $this->build_mask($datatree, $child['else'], $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// choose variable element
							case _ETS_CHOOSEVAR:
								if ($datatype == _ETS_SCALAR) {
									$notfound = TRUE;
									if (isset($child['when'])) {
										foreach ($child['when'] as $gcmaskinfo => $grandchild)  {
											list($gcmasktype, $gcmaskname, $gcmaskvalue) = $this->parse_info($gcmaskinfo);
											if ($currentdata == $gcmaskvalue) {
												$built[] = $this->build_mask($datatree, $grandchild, $direction, $cindex, $clast, $ckey, $incode, $carray);
												$notfound = FALSE;
											}
										}
									}
									if ($notfound && isset($child['else'])) {
										$built[] = $this->build_mask($datatree, $child['else'], $direction, $cindex, $clast, $ckey, $incode, $carray);
									}
								}
								break;
							// set element
							case _ETS_SET:
								if ($datatype != _ETS_MISSING) {
									$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// mis element
							case _ETS_MIS:
								if ($datatype == _ETS_MISSING) {
									$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// set val element
							case _ETS_SETVAL:
								if ($datatype == _ETS_SCALAR) {
									if ($currentdata == $maskvalue) {
										$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
									}
								}
								break;
							// mis val element
							case _ETS_MISVAL:
								if ($datatype == _ETS_MISSING || ($datatype == _ETS_SCALAR && $currentdata != $maskvalue)) {
									$built[] = $this->build_mask($datatree, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
							// mis mask element
							case _ETS_MIS_TEMPLATE:
								if ($datatype == _ETS_MISSING || $datatype == _ETS_COMPLEX) {
									$built[] = $this->build_mask($currentdata, $child, $direction, $cindex, $clast, $ckey, $incode, $carray);
								}
								break;
						}
						break;
				}
			}
		}
		// done
		return implode('', $built);
	}

	/*****   I N T E R F A C E   *****/
    /**
     * Build the result
     */
	function build_all($datatree, $entry)
	{
		// No entry: stop
		if (!isset($this->masktree[$entry])) {
			$this->error(8, 57, $entry);
		}
		// Data tree
		$this->datatree = $datatree;
		if (is_array($this->datatree)) {
			$this->datatree['_parent'] = NULL;
		} elseif (is_object($this->datatree)) {
			$this->datatree->_parent = NULL;
		} elseif (isset($this->datatree)) {
			$this->error(9, 58);
			$this->datatree = NULL;
		}
		// Build
		$built = $this->build_mask($this->datatree, $this->masktree[$entry]);
		// Reduce and return
		if (!isset($this->masktree['0reduce'])) {
			$this->masktree['0reduce'] = _ETS_REDUCE_OFF;
		}
		switch ($this->masktree['0reduce']) {
			case _ETS_REDUCE_OFF:
				break;
			case _ETS_REDUCE_SPACES:
				$built = preg_replace('/(\r\n|\r|\n)+/sm', "\n", preg_replace('/[ \t]*?(\r\n|\r|\n)+[\t ]*/sm', "\n", $built));
				break;
			case _ETS_REDUCE_ALL:
				$built = preg_replace('/[ \t]*?(\r\n|\r|\n)+[\t ]*/sm', '', $built);
				break;
		}
		$built = str_replace("\1n\1", "\n", $built);
		$built = str_replace("\1r\1", "\r", $built);
		$built = str_replace("\1t\1", "\t", $built);
		$built = str_replace("\1s\1", " ",  $built);
		return $built;
	}
    /**
     * Contructor: create the template tree
     */
	function _ets($containers, $hsr, $hcr, $hcw)
	{
		$this->source_read_name = $hsr;
		$this->cache_read_name  = $hcr;
		$this->cache_write_name = $hcw;
		$this->external_source_read = function_exists($hsr);
		$this->external_cache_read  = function_exists($hcr);
		$this->external_cache_write = function_exists($hcw);
		if (is_array($containers) || is_string($containers)) {
			$this->parse_containers($containers);
		} else {
			$this->error(10, 59);
		}
	}
}
/**
 * Source read handler for template string
 */
function _printts($id)
{
	return $id;
}
/**
 * Return a built template
 */
function sprintt($datatree, $containers, $entry = 'main', $hsr = _ETS_SOURCE_READ, $hcr = _ETS_CACHE_READ, $hcw = _ETS_CACHE_WRITE)
{
	$ets = new _ets($containers, $hsr, $hcr, $hcw);
	return $ets->build_all($datatree, $entry);
}
/**
 * Print out a built template
 */
function printt($datatree, $containers, $entry = 'main', $hsr = _ETS_SOURCE_READ, $hcr = _ETS_CACHE_READ, $hcw = _ETS_CACHE_WRITE)
{
	$ets = new _ets($containers, $hsr, $hcr, $hcw);
	echo $ets->build_all($datatree, $entry);
}
/**
 * Return the same value than missing element in PHP code
 */
function mis($value)
{
	return is_null($value) || $value === '' || $value === FALSE || !is_scalar($value);
}
/**
 * Return the same value than set element in PHP code
 */
function set($value)
{
	return !mis($value);
}
/**
 * Return a built template string
 */
function sprintts($datatree, $containers, $entry = 'main')
{
	return sprintt($datatree, $containers, $entry, _ETS_STRING_READ, '', '');
}
/**
 * Print out a built template string
 */
function printts($datatree, $containers, $entry = 'main')
{
	printt($datatree, $containers, $entry, _ETS_STRING_READ, '', '');
}

// read a source container
function ets_source_read_handler($id)
{
	global $themetemplates;
	$custom = strpos($id,'@') ? true : false;
	if($custom){
		$id = str_replace('@', '', $id);
	}

	$id = (!empty($themetemplates)) ? "$themetemplates/".basename($id) : $id;
	$content = FALSE;
	if ($handle = @fopen("$id", 'rb')) {
		$size = @filesize("$id");
		$content = @fread($handle, $size);
		fclose($handle);
	}
	$content = ($custom) ? '{loop:main}'.$content.'{/loop}' : $content;

	return $content;
}
