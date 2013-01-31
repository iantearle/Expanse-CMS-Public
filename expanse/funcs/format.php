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
			  	By Ian Tearle, @iantearle

****************************************************************/

/**
 * Format class.
 * https://github.com/spyrosoft/php-format-html-output
 */
class Format {
	private $input = '';
	private $output = '';
	private $tabs = 0;
	private $in_tag = FALSE;
	private $in_comment = FALSE;
	private $in_content = FALSE;
	private $inline_tag = FALSE;
	private $input_index = 0;

	/**
	 * HTML function.
	 *
	 * @access public
	 * @param mixed $input
	 * @return void
	 */
	public function HTML($input) {
		$this->input = $input;

		$starting_index = 0;

		if(preg_match('/<\!doctype/i', $this->input)) {
			$starting_index = strpos($this->input, '>') + 1;
			$this->output .= substr($this->input, 0, $starting_index);
		}

		for($this->input_index = $starting_index; $this->input_index < strlen($this->input); $this->input_index++) {
			if($this->in_comment) {
				$this->parse_comment();
			} elseif($this->in_tag) {
				$this->parse_inner_tag();
			} else {
				if(preg_match('/[\r\n\t]/', $this->input[$this->input_index])) {
					continue;
				} elseif($this->input[$this->input_index] == '<') {
					if(!$this->is_inline_tag()) {
						$this->in_content = FALSE;
					}
					$this->parse_tag();
				} elseif(!$this->in_content) {
					$this->in_content = TRUE;
				}
				$this->output .= $this->input[$this->input_index];
			}
		}

		return $this->output;
	}

	/**
	 * parse_comment function.
	 *
	 * @access private
	 * @return void
	 */
	private function parse_comment() {
		if($this->is_end_comment()) {
			$this->in_comment = FALSE;
			$this->output .= '-->';
			$this->input_index += 3;
		} else {
			$this->output .= $this->input[$this->input_index];
		}
	}

	/**
	 * parse_inner_tag function.
	 *
	 * @access private
	 * @return void
	 */
	private function parse_inner_tag() {
		if($this->input[$this->input_index] == '>') {
			$this->in_tag = FALSE;
			if(!$this->inline_tag) {
				$this->output .= '>';
			} else {
				$this->output .= '>';
			}
		} else {
			$this->output .= $this->input[$this->input_index];
		}
	}

	/**
	 * parse_tag function.
	 *
	 * @access private
	 * @return void
	 */
	private function parse_tag() {
		if($this->is_comment()) {
			$this->output .= "\n" . str_repeat("\t", $this->tabs);
			$this->in_comment = TRUE;
		} elseif($this->is_end_tag()) {
			$this->in_tag = TRUE;
			$this->inline_tag = FALSE;
			$this->decrement_tabs();
		} else {
			$this->in_tag = TRUE;
			if(!$this->in_content and !$this->inline_tag) {
				$this->output .= "\n" . str_repeat("\t", $this->tabs);
			}
			if(!$this->is_closed_tag()) {
				$this->tabs++;
			}
			if($this->is_inline_tag()) {
				$this->inline_tag = TRUE;
			}
		}
	}

	/**
	 * is_end_tag function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_end_tag() {
		for($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if($this->input[$input_index] == '<' and $this->input[$input_index + 1] == '/') {
				return true;
			} elseif($this->input[$input_index] == '<' and $this->input[$input_index + 1] == '!') {
				return true;
			} elseif($this->input[$input_index] == '>') {
				return false;
			}
		}
		return false;
	}

	/**
	 * decrement_tabs function.
	 *
	 * @access private
	 * @return void
	 */
	private function decrement_tabs() {
		$this->tabs--;
		if($this->tabs < 0) {
			$this->tabs = 0;
		}
	}

	/**
	 * is_comment function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_comment() {
		if($this->input[$this->input_index] == '<'
			and $this->input[$this->input_index + 1] == '!'
			and $this->input[$this->input_index + 2] == '-'
			and $this->input[$this->input_index + 3] == '-') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * is_end_comment function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_end_comment() {
		if($this->input[$this->input_index] == '-'
			and $this->input[$this->input_index + 1] == '-'
			and $this->input[$this->input_index + 2] == '>') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * is_tag_empty function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_tag_empty() {
		$current_tag = $this->get_current_tag($this->input_index + 2);
		$in_tag = FALSE;

		for($input_index = $this->input_index - 1; $input_index >= 0; $input_index--) {
			if(!$in_tag) {
				if($this->input[$input_index] == '>') {
					$in_tag = TRUE;
				} elseif(!preg_match('/\s/', $this->input[$input_index])) {
					return FALSE;
				}
			} else {
				if($this->input[$input_index] == '<') {
					if($current_tag == $this->get_current_tag($input_index + 1)) {
						return TRUE;
					} else {
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * get_current_tag function.
	 *
	 * @access private
	 * @param mixed $input_index
	 * @return void
	 */
	private function get_current_tag($input_index) {
		$current_tag = '';

		for($input_index; $input_index < strlen($this->input); $input_index++) {
			if($this->input[$input_index] == '<') {
				continue;
			} elseif($this->input[$input_index] == '>' or preg_match('/\s/', $this->input[$input_index])) {
				return $current_tag;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}

		return $current_tag;
	}

	/**
	 * is_closed_tag function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_closed_tag() {
		$closed_tags = array(
			'meta', 'link', 'img', 'hr', 'br', 'input',
		);

		$current_tag = '';

		for($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if($this->input[$input_index] == '<') {
				continue;
			} elseif(preg_match('/\s/', $this->input[$input_index])) {
				break;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}

		if(in_array($current_tag, $closed_tags)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * is_inline_tag function.
	 *
	 * @access private
	 * @return void
	 */
	private function is_inline_tag() {
		$inline_tags = array(
			'title', 'a', 'span', 'abbr', 'acronym', 'b', 'basefont', 'bdo', 'big', 'cite', 'code', 'dfn', 'em', 'font', 'i', 'kbd', 'label', 'q', 's', 'samp', 'small', 'strike', 'strong', 'sub', 'sup', 'textarea', 'tt', 'u', 'var', 'del',
		);

		$current_tag = '';

		for($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if($this->input[$input_index] == '<' or $this->input[$input_index] == '/') {
				continue;
			} elseif(preg_match('/\s/', $this->input[$input_index]) or $this->input[$input_index] == '>') {
				break;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}

		if(in_array($current_tag, $inline_tags)) {
			return true;
		} else {
			return false;
		}
	}
}
