<?php
/********* Expanse ***********/
class DatabaseConnection {
	var $connection;
	var $databaseName;
	var $result;
	// -------------------------------------------------------------
	function __construct() {
		global $CONFIG;
		$this->databaseName = $CONFIG['db'];
		$serverName = $CONFIG['host'];
		$databaseUser = $CONFIG['user'];
		$databasePassword = $CONFIG['pass'];
		$this->Prefix = $CONFIG['prefix'];
		$this->connection = @mysql_pconnect($serverName, $databaseUser, $databasePassword);
		if(!$this->connection) {
			$_GET['reason'] = 'db_nocnx';
			include(dirname(__FILE__) . "/message.php");
			exit();
		}
		$select_db = mysql_select_db($this->databaseName);
		if(!$select_db) {
			$_GET['reason'] = 'db_noselect';
			include(dirname(__FILE__) . "/message.php");
			exit();
		}
	}
	// -------------------------------------------------------------
	function Close() {
		mysql_close($this->connection);
	}
	// -------------------------------------------------------------
	function GetConnection() {
		return $this->connection;
	}
	// -------------------------------------------------------------
	function Query($query) {
		$this->result = mysql_query($query, $this->connection);
		if(!$this->result) {
			return('Invalid query: ' . mysql_error());
		}
		return $this->result;
	}
	// -------------------------------------------------------------
	function Rows() {
		if($this->result != false) {
			return mysql_num_rows($this->result);
		}
		return null;
	}
	// -------------------------------------------------------------
	function AffectedRows() {
		return mysql_affected_rows();
	}
	// -------------------------------------------------------------
	function Result($row, $name) {
		if($this->Rows() > 0) {
			return mysql_result($this->result, $row, $name);
		}
		return null;
	}

	// -------------------------------------------------------------
	function InsertOrUpdate($query) {
		$this->result = mysql_query($query, $this->connection);
		return($this->AffectedRows() > 0);
	}
	function Escape($text) {
		if(!is_numeric($text)) {
			if(get_magic_quotes_gpc() && !defined('MAGIC_QUOTES_OFF')) {
				$text = stripslashes($text);
			}
			$text = (function_exists('mysql_real_escape_string')) ? mysql_real_escape_string($text) : mysql_escape_string($text);
		}
		return $text;
	}
	// -------------------------------------------------------------
	function Fetch($type = 'object') {
		$type = "mysql_fetch_$type";
		if(function_exists($type)) {
			return $type($this->result);
		} else {
			return mysql_fetch_object($this->result);
		}
	}
	// -------------------------------------------------------------
	function Unescape($text) {
		return $text;
	}
	// -------------------------------------------------------------
	function GetCurrentId() {
		return intval(mysql_insert_id($this->connection));
	}
	// -------------------------------------------------------------
	function GetCol($query='', $x=0) {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($array = mysql_fetch_array($query)) {
			$tableList[] = isset($array[$x]) ? $array[$x] : '';
		}
		return $tableList;
	}
	// -------------------------------------------------------------
	function GetResults($query='') {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($object = mysql_fetch_object($query)) {
			$tableList[] = $object;
		}
		return $tableList;
	}

	function GetAssoc($query='') {
		$query = empty($query) ? $this->result : $this->Query($query);
		$tableList = array();
		while($object = mysql_fetch_assoc($query)) {
			$tableList[] = $object;
		}
		return $tableList;
	}
}
$Database = new DatabaseConnection();
?>