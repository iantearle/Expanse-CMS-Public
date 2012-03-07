<?php
/********* Expanse ***********/

/*
------------------------------------------------------------
Session class
============================================================
*/

class Session {
   // session-lifetime
   var $lifetime;
   var $Database;
   function __construct($Database){
   $this->Database = $Database;
   $this->session = new Expanse('sessions');
   }
   function open($savePath, $sessName) {
   		if(!is_object($this->Database)){
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
		if(empty($the_session)){
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
			if($session->Save()){
			return true;
			}
		return false;
   }
   function destroy($session_id) {
       // delete session-data
	   $sessions = $this->session;
	   if($sessions->Delete($session_id)){
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
session_set_save_handler(array($session,"open"),
                        array($session,"close"),
                        array($session,"read"),
                        array($session,"write"),
                        array($session,"destroy"),
                        array($session,"gc"));

?>