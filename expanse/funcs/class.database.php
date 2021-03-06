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

class DatabaseConnection {
	var $connection;
	var $databaseName;
	var $result;

	function __construct() {
		global $CONFIG;
		$this->databaseName = $CONFIG['db'];
		$serverName = $CONFIG['host'];
		$databaseUser = $CONFIG['user'];
		$databasePassword = $CONFIG['pass'];
		$this->Prefix = $CONFIG['prefix'];
		$this->connection = @ new mysqli('p:'.$serverName, $databaseUser, $databasePassword); //@mysql_pconnect($serverName, $databaseUser, $databasePassword);
		if(!$this->connection) {
			$_GET['reason'] = 'db_nocnx';
			include(dirname(__FILE__) . "/message.php");
			exit();
		}
		$select_db = @mysqli_select_db($this->connection, $this->databaseName); //mysql_select_db($this->databaseName);
		if(!$select_db) {
			$_GET['reason'] = 'db_noselect';
			include(dirname(__FILE__) . "/message.php");
			exit();
		}
	}

	function Close() {
		mysqli_close($this->connection);
	}

	function GetConnection() {
		return $this->connection;
	}

	function Query($query) {
		$this->result = mysqli_query($this->connection, $query);//mysql_query($query, $this->connection);
		if(!$this->result) {
			return('Invalid query: ' . mysqli_error($this->connection)); //mysql_error());
		}
		return $this->result;
	}

	function Rows() {
		if($this->result != false) {
//			$stmt = mysqli_prepare($this->connection, $this->result);
			return mysqli_num_rows($this->result); //mysql_num_rows($this->result);
		}
		return null;
	}

	function AffectedRows() {
		return mysqli_affected_rows($this->connection); //mysql_affected_rows();
	}

	function db_result($result, $row, $field) {
		if($result->num_rows==0) {
			return 'unknown';
		}
		$result->data_seek($row);
		$ceva=$result->fetch_assoc();
		$rasp=$ceva[$field];
		return $rasp;
	}

	function Result($row, $name) {
		if($this->Rows() > 0) {
			return $this->db_result($this->result, $row, $name); // mysql_result($this->result, $row, $name);
		}
		return null;
	}

	function InsertOrUpdate($query) {
		$this->result = mysqli_query($this->connection, $query); //mysql_query($query, $this->connection);
		return($this->AffectedRows() > 0);
	}

	function Escape($text) {
		if(!is_numeric($text)) {
			if(get_magic_quotes_gpc() && !defined('MAGIC_QUOTES_OFF')) {
				$text = stripslashes($text);
			}
			$text = (function_exists('mysql_real_escape_string')) ? mysqli_real_escape_string($this->connection, $text) : mysqli_escape_string($this->connection, $text);//mysql_real_escape_string($text) : mysql_escape_string($text);
		}
		return $text;
	}

	function Fetch($type = 'object') {
		$type = "mysql_fetch_$type";
		if(function_exists($type)) {
			return $type($this->result);
		} else {
			return mysqli_fetch_object($this->restult); //mysql_fetch_object($this->result);
		}
	}

	function Unescape($text) {
		return $text;
	}

	function GetCurrentId() {
		return intval(mysqli_insert_id($this->connection)); //mysql_insert_id($this->connection));
	}

	function GetCol($query='', $x=0) {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($array = mysqli_fetch_array($query)) { //mysql_fetch_array($query)) {
			$tableList[] = isset($array[$x]) ? $array[$x] : '';
		}
		return $tableList;
	}

	function GetResults($query='') {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($object = mysqli_fetch_object($query)) { //mysql_fetch_object($query)) {
			$tableList[] = $object;
		}
		return $tableList;
	}

	function GetAssoc($query='') {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($object =  mysqli_fetch_assoc($query)) { //mysql_fetch_assoc($query)) {
			$tableList[] = $object;
		}
		return $tableList;
	}
}

$Database = new DatabaseConnection();
