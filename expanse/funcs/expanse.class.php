<?php
/********* Expanse ***********/
class Expanse {
	var $pog_query;
	var $Fields = array();

	function __construct($tablename) {
		$Database = new DatabaseConnection();
		$this->TableName = $Database->Prefix . $tablename;
		$this->RootName = $tablename;
		if(isset($_SESSION["{$tablename}_fields"]) && !empty($_SESSION["{$tablename}_fields"])) {
			$this->Fields = $_SESSION["{$tablename}_fields"];
			foreach ($this->Fields as $val) {
				$this->{$val} = '';
			}
		} else {
			if(isset($_SESSION["{$tablename}_fields"]) && empty($_SESSION["{$tablename}_fields"])) {
				$this->FlushCache();
			}
			$this->Fields = $_SESSION["{$tablename}_fields"] = $this->GetFields($tablename);
		}
	}

	function GetFields($tablename) {
		$Database = new DatabaseConnection();
		$result = $Database->Query("SHOW COLUMNS FROM $this->TableName");
		$properties = func_get_args();
		$Fields = array();
		if($Database->Rows()) {
			$i = 1;
			while($row = mysql_fetch_object($result)) {
				$this->{$row->Field} = isset($properties[$i]) ? $properties[$i] : '';
				if($row->Key == 'PRI') {
					$Fields['primary'] = $row->Field;
				} else {
					$Fields[] = $row->Field;
				}
				$Fields['primary'] = isset($Fields['primary']) ? $Fields['primary'] : 'id';
				$i++;
			}
		}
		return $Fields;
	}

	function FlushCache() {
		unset($_SESSION["{$this->RootName}_fields"]);
	}

	/**
	* Gets object from database
	* @param integer $id
	* @return object $this
	*/
	function Get($id) {
		$Database = new DatabaseConnection();
		$this->pog_query = "SELECT * FROM `{$this->TableName}` WHERE `{$this->Fields['primary']}`='" . intval($id) . "' LIMIT 1";
		$Database->Query($this->pog_query);
		foreach ($this->Fields as $k => $v) {
			$this->{$v} = $Database->Unescape($Database->Result(0, $v));
		}
		return $this;
	}

	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...}
	* @param string $sortBy
	* @param boolean $ascending
	* @param int limit
	* @param string $what
	* @return array $tableList
	*/
	function GetList($fcv_array, $sortBy = '', $ascending = 'ASC', $limit = '', $what = '') {
		$Database = new DatabaseConnection();
		if(is_string($fcv_array)) {
			$fcv_array = trim($fcv_array);
			if(empty($fcv_array)){
				return array();
			}
			$this->pog_query = str_replace('*table*', "`{$this->TableName}`", $fcv_array);
		} else {
			if(count($fcv_array) == 0){
				return $fcv_array;
			}

			$asc = $ascending == 'ASC' ? 'ASC' : 'DESC';
			$orderBy = !empty($sortBy) ? $sortBy : (isset($this->Fields['primary']) ? $this->Fields['primary'] : 'id');
			$sqlLimit = empty($limit) ? '' : "LIMIT $limit";
			$what = empty($what) ? '*' : $what;
			$tableList = array();
			$this->pog_query = "SELECT $what FROM `{$this->TableName}` WHERE ";
			for ($i = 0, $c = sizeof($fcv_array) - 1; $i < $c; $i++) {
				$this->pog_query .= "`" . strtolower($fcv_array[$i][0]) . "` " . $fcv_array[$i][1] . " '" . $Database->Escape($fcv_array[$i][2]) . "' AND ";
			}
			$this->pog_query .= "`" . strtolower($fcv_array[$i][0]) . "` " . $fcv_array[$i][1] . " '" . $Database->Escape($fcv_array[$i][2]) . "' ORDER BY `$orderBy` $asc $sqlLimit";
		}
		$query = $Database->Query($this->pog_query);
		$tableList = array();
		while ($array = mysql_fetch_object($query)) {
			$tableList[] = $array;
		}
		return $tableList;
	}

	/**
	* @param
	* @return array $tableList
	*/
	function Search($fcv_array, $search_array, $sortBy = '', $ascending = true, $limit = '', $what = '') {
		$Database = new DatabaseConnection();
		if(is_string($fcv_array)) {
			$fcv_array = trim($fcv_array);
			if(empty($fcv_array)) {
				return array();
			}
			$this->pog_query = str_replace('*table*', "`{$this->TableName}`", $fcv_array);
		} else {
			if(count($fcv_array) == 0) {
				return $fcv_array;
			}
			$search_array = strtoupper($search_array);
			$search_array = strip_tags($search_array);
			$search_array = trim ($search_array);
			$asc = $ascending == true ? 'ASC' : 'DESC';
			$orderBy = !empty($sortBy) ? $sortBy : (isset($this->Fields['primary']) ? $this->Fields['primary'] : 'id');
			$sqlLimit = empty($limit) ? '' : "LIMIT $limit";
			$what = empty($what) ? '*' : $what;
			$tableList = array();
			$this->pog_query = "SELECT $what FROM `{$this->TableName}` WHERE ";
			for($i = 0, $c = sizeof($fcv_array) - 1; $i < $c; $i++) {
				$this->pog_query .= "`" . strtolower($fcv_array[$i][0]) . "` " . $fcv_array[$i][1] . " " . $Database->Escape($fcv_array[$i][2]) . " AND ";
			}
			$this->pog_query .= "`" . strtolower($fcv_array[$i][0]) . "` " . $fcv_array[$i][1] . " " . $Database->Escape($fcv_array[$i][2]) . " AND ";
			$this->pog_query .= "((upper(`descr`) LIKE '%" . $search_array . "%') OR (upper(`title`) LIKE '%" . $search_array . "%')) ORDER BY $orderBy $asc $sqlLimit";
		}
		$query = $Database->Query($this->pog_query);
		$tableList = array();
		while($array = mysql_fetch_object($query)) {
			$tableList[] = $array;
		}
		return $tableList;
	}

	/**
	* Saves the object to the database
	* @return integer $id
	*/
	function Save() {
		$Database = new DatabaseConnection();
		$this->pog_query = "SELECT {$this->Fields['primary']} FROM `{$this->TableName}` WHERE `{$this->Fields['primary']}`='" . $this->{$this->Fields['primary']} . "' LIMIT 1";
		$Database->Query($this->pog_query);
		if($Database->Rows() > 0) {
			$start = "update `{$this->TableName}` set";
			$upd = array();
			foreach($this->Fields as $f) {
				$upd[] = "`" . $f . "`='" . $Database->Escape($this->{$f}) . "'";
			}
			$middle = implode(', ', $upd);
			$end = "where `{$this->Fields['primary']}`='" . $this->{$this->Fields['primary']} . "'";
			$this->pog_query = "$start $middle $end";
		} else {
			$start = "insert into `{$this->TableName}`";
			$fields = array();
			$vals = array();
			foreach($this->Fields as $f) {
				$fields[] = "`$f`";
				$val = applyOzoneAction($f, $this->{$f});
				$vals[] = "'" . $Database->Escape($val) . "'";
			}
			$middle = '(' . implode(', ', $fields) . ')';
			$end = '(' . implode(', ', $vals) . ')';
			$this->pog_query = "$start $middle values $end";
		}
		$Database->InsertOrUpdate($this->pog_query);
		if(empty($this->id)) {
			$this->id = $Database->GetCurrentId();
		}
		return $this->id;
	}

	/**
	* Clones the object and saves it to the database
	* @return integer $id
	*/
	function SaveNew() {
		$this->{$this->Fields['primary']} = '';
		return $this->Save();
	}

	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete($id = '') {
		$this->{$this->Fields['primary']} = !empty($id) ? $id : $this->{$this->Fields['primary']};
		$Database = new DatabaseConnection();
		$this->pog_query = "delete from `{$this->TableName}` where `{$this->Fields['primary']}`='" . $this->{$this->Fields['primary']} . "'";
		$Database->Query($this->pog_query);
		return $Database->AffectedRows() > 0 ? true : false;
	}
}
?>