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
Session class
============================================================
*/

class Session {
	// session-lifetime
	var $lifetime;
	var $Database;

	function __construct($Database) {
		$this->Database = $Database;
		$this->session = new Expanse('sessions');
	}

	function open($savePath, $sessName) {
		if(!is_object($this->Database)) {
			return false;
		}

		// get session-lifetime
		$this->lifetime = get_cfg_var("session.gc_maxlifetime");
		return true;
	}

	function close() {

		// close database-connection
		return $this->Database->Close();
	}

	function read($session_id) {
		$sessions = $this->session;
		$the_session = $sessions->GetList(array(array('id', '=', $session_id), array('expires', '>', time())));
		if(empty($the_session)) {
			return '';
		}
		return $the_session[0]->data;
	}

	function write($session_id,$session_data) {

		// new session-expire-time
		$new_exp = time() + $this->lifetime;
		$session = $this->session;
		$session->Get($session_id);
		$session->id = (!empty($session->id)) ? $session->id : $session_id;
		$session->expires = $new_exp;
		$session->data = $session_data;
		if($session->Save()) {
			return true;
		}
		return false;
	}

	function destroy($session_id) {

		// delete session-data
		$sessions = $this->session;
		if($sessions->Delete($session_id)) {
			return true;
		}
		return false;
	}

	function gc($sessMaxLifeTime) {

		// delete old sessions
		$Database = $this->Database;
		$Database->Query('DELETE FROM '.$Database->Prefix.'sessions WHERE expires < '.time());
	}

}

session_start();

$session = new Session($Database);
session_set_save_handler(array(&$session,"open"),
                        array(&$session,"close"),
                        array(&$session,"read"),
                        array(&$session,"write"),
                        array(&$session,"destroy"),
                        array(&$session,"gc"));
