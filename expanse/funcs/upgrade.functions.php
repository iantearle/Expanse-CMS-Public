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

/**
CLASS: dbDelta
PURPOSE: compare existing database to schema and generate SQL to update database.
DEPENDENCY: Expanse Database object
COPYRIGHT: (c) 2005 Zach Shelton - http://zachofalltrades.net
LICENSE: LGPL - http://www.gnu.org/licenses/lgpl.html
CREDITS:
dbDelta - A script by this name was found buried in the WordPress project,
and someone on that project deserves significant credit for the REGEX
and SQL that forms the heart of the findDifferences() function
Zach Shelton - turned the wordpress dbDelta script into a generic reusable class
Nate Cavanaugh - made it work for Expanse/expanse's Database class, and cleaned up the code to actually work.
***************************
* Changes made by Nate
* Now uses the Expanse/expanse Database class for querying
* Fixed the many missing slashes (\) in looking for new lines
* Fixed the explode(';') to instead explode(";\n") to allow for serialized values
* The drop table logic was broken (messed up parameter order)
*/

/**
* one of these constants must be passed to perform_queries() to tell it
* which types of queries it should perform.
* You can choose to concatenate the string constants together in order to
* select multiples.
*/

define('DBDELTA_CREATE_TABLES', 'CREATE_T');
define('DBDELTA_DROP_TABLES', 'DROP_T');
define('DBDELTA_ADD_INDEXES', 'ADD_IND');
define('DBDELTA_ADD_COLS', 'ADD_C');
define('DBDELTA_DROP_COLS', 'DROP_C');
define('DBDELTA_ALTER_COLS', 'ALTER_C');
define('DBDELTA_INSERTS', 'INSERT');
define('DBDELTA_UPDATES', 'UPDATE');

/* In the world of distributing open source software that interacts heavily a database, thereis usually no harm in the following: create new tables, adding new columns, adding new indexes, inserting new data, and updating existing data. We therefore have a constant that joins these query types. Altering columns has the potential to destroy data (truncate a field that a user had expanded for some customization need. Dropping columns or tables is certain to cause a loss of data, so these query types are not as 'safe'. It should be noted though, that inserts and updates, while considered 'safe' have the potential to fail if column alterations are not done first. dbDelta makes no assumptions, and empowers the script developer to make these decisions at the time of script implementation, or even the potential to wrap this with an interactive interface for the installer, where decisions can be made at install time. */
define('DBDELTA_SAFE', DBDELTA_CREATE_TABLES.DBDELTA_ADD_INDEXES.DBDELTA_ADD_COLS.DBDELTA_INSERTS.DBDELTA_UPDATES);
define('DBDELTA_MOST', DBDELTA_SAFE.DBDELTA_ALTER_COLS);
define('DBDELTA_ALL', DBDELTA_MOST.DBDELTA_DROP_COLS.DBDELTA_DROP_TABLES);
define('DBDELTA_DEFAULT', DBDELTA_SAFE);

/***
* given a Expanse Database object and a set of queries (sql dump)
* generate a set of queries that can be used make the database match
*/
class dbDelta {
	/* all variables declared here should be considered 'private'
	please use the 'get_' and 'set_' methods for access */
	var $_dbCon; //reference to an Expanse Database object
	var $_table_prefix; //this is set by the Expanse Database object
	var $_dbSchema; //the set of queries we are checking against

	//each query set variable is an array of sub-array items
	//the sub-array will contain key/value pairs where with keys 'description' and 'query'
	var $qs_create_tables;
	var $qs_drop_tables;
	var $qs_add_indexes;
	var $qs_add_cols;
	var $qs_drop_cols;
	var $qs_alter_cols;
	var $qs_inserts;
	var $qs_updates;
	var $qs_ignored;

	/**
	* constructor requires a reference to an Expanse Database object
	*/
	function dbDelta($db, $dbSchema=null) {
		$this->_dbCon = $db;
		$this->_table_prefix = $db->Prefix;
		if($dbSchema) {
			$this->setSchema($dbSchema);
		}
		if(!is_object($db)) {
			return false;
		}
		return true;
	}

	/**
	* by setting a table prefix here, you are telling dbDelta to alter the
	* table names in the schema that will be passed to findDfifferences()
	* -this must therefore be called before findDifferences()
	*/
	function setTablePrefix($str) {
		$this->_table_prefix = $str;
	}

	/**
	* If you did not pass the schema into the constructor, you can set it
	* using this function. It is better to use this function than to go
	* directly to the var $this->_dbSchema, because the funciton will also perform
	* the necessary re-initialization of the 'query set' arrays. It is also
	* good to use the function for future-proofing, as there may be additional
	* initialization or validation added at a later time.
	*
	*@param queries can be passed as an array of queries, or a single string,
	* with queries separated by a semi-colon.
	*/
	function setSchema($queries) {
		$this->_dbSchema = $queries;
		$this->initArrays();
	}

	/**
	* returns a one dimensional array of query descriptions that were generated by findDifferences()
	*
	* @param $strConst one of the DBDELTA_ constants indicating which query set
	* should be returned
	*/
	function get_descriptions($strConst=DBDELTA_DEFAULT) {
		$qs = $this->_merge_query_sets($strConst);
		$ret = array();
		foreach($qs as $q) {
			$ret[] = $q['description'];
		}
		return $ret;
	}

	/**
	* returns all of the actual SQL queries matching the selected filter constant
	*/
	function get_queries($strConst=DBDELTA_DEFAULT) {
		$qs = $this->_merge_query_sets($strConst);
		$ret = array();
		foreach($qs as $q) {
			$ret[] = $q['query'];
		}
		return $ret;
	}

	/**
	* returns an array of table names that exist in the database, but were not
	* found in the schema. If a table prefix was specified, this will omit
	* existing tables that do not share the prefix.
	*/
	function get_extra_tables() {
		$ret = array();
		foreach($this->qs_drop_tables as $key => $value) {
			$ret[] = $key;
		}
		return $ret;
	}

	/**
	* returns an array of table.column names that exist in the database, but
	* were not in the schema.
	*/
	function get_extra_columns() {
		$ret = array();
		foreach($this->qs_drop_cols as $key=>$value) {
			$ret[] = $key;
		}
		return $ret;
	}

	/**
	* Run a subset of the generated queries. This is the function most likely
	* to be used for automated updates, which would be called after
	* findDifferences().
	*/
	function perform_queries($strConst=DBDELTA_DEFAULT) {
		$queries = $this->get_queries($strConst);
		return $this->do_these_queries($queries);
	}

	/**
	* Runs a set of queries against the database.
	*
	* If your update script will run in an automated fashion, use
	* perform_queries(). But if you want to run in a user interface where a
	* user would select which queries to run, you could then pass the selected
	* queries, (including your own or other user-defined) to this function for
	* batch processing.
	*/
	function do_these_queries($queries) {
		$dbCon = $this->_dbCon;
		$ret = array();
		// Seperate individual queries into an array
		if(!is_array($queries)) {
			$queries = $this->_normalize($queries);
			$queries = explode( ";\n", $queries );
			if('' == $queries[count($queries) - 1]) {
				array_pop($queries);
			}
		}
		foreach($queries as $query) {
			$item = array (
				'query' => $query,
				'result' => $dbCon->Query($query)
			);
			$ret[] = $item;
		}
		return $ret; //returns an array of ordinal array of query/result pairs
	}

	/******************************************************************************
	PRIVATE FUNCTIONS
	If Php supported private functions, these would not be availabel outside of
	this class.
	******************************************************************************/

	//private
	function _merge_query_sets($strConst=DBDELTA_DEFAULT) {
		$qs = array();
		if(strstr($strConst, DBDELTA_CREATE_TABLES)) {
			$qs = array_merge($qs, $this->qs_create_tables);
		}
		if(strstr($strConst, DBDELTA_DROP_TABLES)) {
			$qs = array_merge($qs, $this->qs_drop_tables);
		}
		if(strstr($strConst, DBDELTA_ADD_COLS)) {
			$qs = array_merge($qs, $this->qs_add_cols);
		}
		if(strstr($strConst, DBDELTA_DROP_COLS)) {
			$qs = array_merge($qs, $this->qs_drop_cols);
		}
		if(strstr($strConst, DBDELTA_ALTER_COLS)) {
			$qs = array_merge($qs, $this->qs_alter_cols);
		}
		if(strstr($strConst, DBDELTA_ADD_INDEXES)) {
			$qs = array_merge($qs, $this->qs_add_indexes);
		}
		if(strstr($strConst, DBDELTA_INSERTS)) {
			$qs = array_merge($qs, $this->qs_inserts);
		}
		if(strstr($strConst, DBDELTA_UPDATES)) {
			$qs = array_merge($qs, $this->qs_updates);
		}

		return $qs;
	}

	//private
	function _getDb() {
		return $this->_dbCon;
	}

	//private
	function initArrays() {
		$this->qs_create_tables = array();
		$this->qs_drop_tables = array();
		$this->qs_add_indexes = array();
		$this->qs_add_cols = array();
		$this->qs_drop_cols = array();
		$this->qs_alter_cols = array();
		$this->qs_inserts = array();
		$this->qs_updates = array();
		$this->qs_ignored = array();
	}

	//private
	function _normalize($t) {
		$t = str_replace("\r\n", "\n", $t);
		$t = str_replace("\r", "\n", $t);
		return $t;
	}

	/******************************************************************************
	THE LOGICAL MONSTER WITHIN - with thanks to the developers at WordPress
	******************************************************************************/
	/**
	* This function will compare the given database against the given SQL
	* queries (schema) and create the necessary ALTER queries to bring the
	* database into harmony with the schema (table creation queries). There
	* should not be any ALTER queries in the schema that is passed, although
	* you can include INSERT and UPDATE queries.
	*
	* @param mixed $queries - one big string or an array of individual queries
	*/
	function findDifferences($queries=null) {
		$dbCon = $this->_getDb();
		$this->initArrays();
		$prefix = $this->_table_prefix;
		$len_prefix = count_chars($prefix);
		/* $cqueries is a local array for processing table CREATE queries extracted from schema (which must be present for this helper class to do anything interesting). It is conceivable that the schema could also contain some default data as INSERTS, or if it is an upgrade, there may also be some UPDATE queries */
		$cqueries = array();

		if($queries==null) {
			$queries = $this->_dbSchema;
		}

		// Seperate individual queries into an array
		if(!is_array($queries)) {
			$queries = str_replace('`','', $queries);
			$queries = $this->_normalize($queries);
			$queries = explode( ";\n", $queries );
			if('' == $queries[count($queries) - 1]) {
				array_pop($queries);
			}
		}

		// Create a tablename index for an array ($cqueries) of queries
		foreach($queries as $qry) {
			if(preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
				$tableName = strtolower($matches[1]);
				if(!empty($prefix)) {
					$qry = preg_replace("/$tableName/", $prefix.$tableName, $qry, 1);
				}
				$cqueries[$prefix.$tableName] = $qry; //schema queries for further analysis
				$item = array(
					'description' => 'Create table '.$prefix.$tableName,
					'query' => $qry
				);
				$this->qs_create_tables[$prefix.$tableName] = $item;
			} elseif(preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
				array_unshift($cqueries, $qry);
			} elseif(preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
				$tableName = strtolower($matches[1]);
				if($prefix!='') {
					$qry = preg_replace("/$tableName/", $prefix.$tableName, $qry, 1);
				}
				$item = array(
					'description' => 'Insert data into '.$prefix.$tableName,
					'query' => $qry
				);
				$this->qs_inserts[] = $item;
			} elseif(preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
				$tableName = strtolower($matches[1]);
				if($prefix!='') {
					$qry = preg_replace("/$tableName/", $prefix.$tableName, $qry, 1);
				}
				$item = array(
					'description' => 'Update data in '.$prefix.$tableName,
					'query' => $qry
				);
				$this->qs_updates[] = $item;
			} else {
				// Unrecognized query type
				$item = array(
					'description' => 'Unrecognized query',
					'query' => $qry
				);
				$this->qs_ignored[] = $item;
			}
		}
		//debug($this->qs_create_tables);
		// Check to see which tables and fields exist
		if($tables = $dbCon->GetCol('SHOW TABLES;')) {
			// For every current table in the existing database
			foreach($tables as $table) {
				// If a table query exists for the database table...
				if(array_key_exists(strtolower($table), $cqueries)) {
					unset($this->qs_create_tables[strtolower($table)]);
					// Clear the field and index arrays
					//unset($cfields);
					//unset($indices);
					$cfields = array();
					$indices = array();
					// Get all of the field names in the query from between the parens
					preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
					$qryline = trim($match2[1]);
					// Separate field lines into an array
					$flds = explode("\n", $qryline);
					// For every field line specified in the query
					foreach($flds as $fld) {
						// Extract the field name
						preg_match("|^([^ ]*)|", trim($fld), $fvals);
						$fieldname = $fvals[1];
						// Verify the found field name
						$validfield = true;
						switch(strtolower($fieldname)) {
							case '':
							case 'primary':
							case 'index':
							case 'fulltext':
							case 'unique':
							case 'key':
								$validfield = false;
								$indices[] = trim(trim($fld), ", \n");
							break;
						}
						$fld = trim($fld);
						// If it's a valid field, add it to the field array
						if($validfield) {
							$cfields[strtolower($fieldname)] = trim($fld, ", \n");
						}
					}

					// Fetch the table column structure from the database
					$tablefields = $dbCon->GetResults("DESCRIBE {$table};");
					// For every field in the table
					foreach($tablefields as $tablefield) {
						// If the table field exists in the field array...
						if(array_key_exists(strtolower($tablefield->Field), $cfields)) {
							// Get the field type from the query
							preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
							$fieldtype = $matches[1];
							// Is actual field type different from the field type in query?
							if($tablefield->Type != $fieldtype) {
								// Add a query to change the column type
								$query = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
								$descr = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
								$item = array(
									'description' => $descr,
									'query' => $query
								);
								$this->qs_alter_cols[$table.$tablefield->Field.'_TYPE('.$fieldtype.')'] = $item;
							}

							// Get the default value from the array
							//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
							if(preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
								$default_value = $matches[1];
								if($tablefield->Default != $default_value) {
									// Add a query to change the column's default value
									$query = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
									$descr = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
									$item = array(
										'description' => $descr,
										'query' => $query
									);
									$this->qs_alter_cols[$table.$tablefield->Field.'_DEFAULT('.$default_value.')'] = $item;
								}
							}

							// Remove the field from the array (so it's not added in next foreach loop)
							unset($cfields[strtolower($tablefield->Field)]);
						} else {
							// This field exists in the table, but not in the creation queries
							$query = "ALTER TABLE {$table} DROP COLUMN {$tablefield->Field}";
							$descr = "Drop column $table.{$tablefield->Field}";
							$item = array(
								'description' => $descr,
								'query' => $query
							);
							$this->qs_drop_cols[$table.'.'.$tablefield->Field] = $item;
						}
					}

					// For every remaining field specified for the table
					foreach($cfields as $fieldname => $fielddef) {
						// add a query that adds the field to that table
						$query = "ALTER TABLE {$table} ADD COLUMN $fielddef";
						$descr = 'Added column '.$table.'.'.$fieldname;
						$item = array(
							'description' => $descr,
							'query' => $query
						);
						$this->qs_add_cols[$table.'.'.$fieldname] = $item;
					}

					// Index stuff goes here
					// Fetch the table index structure from the database
					$tableindices = $dbCon->GetResults("SHOW INDEX FROM {$table};");
					$tableindices = !empty($tableindices) ? $tableindices : false;
					if($tableindices) {
						// Clear the index array
						//unset($index_ary);
						$index_ary = array();
						// For every index in the table
						foreach($tableindices as $tableindex) {
							// Add the index to the index data array
							$keyname = $tableindex->Key_name;
							$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
							$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
						}

						// For each actual index in the index array
						foreach($index_ary as $index_name => $index_data) {
							// Build a create string to compare to the query
							$index_string = '';
							if($index_name == 'PRIMARY') {
								$index_string .= 'PRIMARY ';
							} else if($index_data['unique']) {
								$index_string .= 'UNIQUE ';
							}
							$index_string .= 'KEY ';
							if($index_name != 'PRIMARY') {
								$index_string .= $index_name;
							}
							$index_columns = '';
							// For each column in the index
							foreach($index_data['columns'] as $column_data) {
								if($index_columns != '') {
									$index_columns .= ',';
								}
								// Add the field to the column list string
								$index_columns .= $column_data['fieldname'];
								if($column_data['subpart'] != '') {
									$index_columns .= '('.$column_data['subpart'].')';
								}
							}
							// Add the column list to the index create string
							$index_string .= ' ('.$index_columns.')';

							if(!(($aindex = array_search($index_string, $indices)) === false)) {
								unset($indices[$aindex]);
							}
						}
					}

					// For every remaining index specified for the table
					foreach($indices as $index) {
						// add a query that adds the index to that table
						$query = "ALTER TABLE {$table} ADD $index";
						$descr = 'Added index '.$table.' '.$index;
						$item = array(
							'description' => $descr,
							'query' => $query
						);
						$this->qs_add_indexes[$table.'_INDEX('.$index.')'] = $item;
					}
				} else {
					// This table exists in the database, but not in the creation queries
					//ignore it unless it begins with table prefix.
					//TODO: verify logic that ignores existing tables in database that do not share prefix
					//if (substr($table,0, $len_prefix)==$prefix) {
					if(strpos($table, $prefix) === 0) {
						$item = array(
							'description' => 'Drop table '.$table,
							'query' => 'DROP TABLE '.$table
						);
						$this->qs_drop_tables[$table] = $item;
					}//end if
				}//end IF (query exists for table)
			}//end FOREACH (tables in database)
		}//end IF
		//debug($this->qs_drop_tables);
		return;
	}//end findDifferences()
} //end class dbDelta

/*
-------------------------------------------------
Upgrade functions
-------------------------------------------------
*/
function upgrade() {
	global $Database,$schema,$default_install_values;
	$version = VERSION;

	if(!isset($schema['structure']) || !IS_WRITABLE) {
		return false;
	}

	if(!isset($_POST['manual'])) {
		copyr('.','..', array('index.html','upgrade.php'));	// Copies (recursively) everything except index.html and upgrade.php
	}
	unset($schema['prepare']);
	$params = $default_install_values;
	$dbDelta = new dbDelta($Database);
	$dbDelta->findDifferences($schema['structure']);
	$results['db_structure'] = $dbDelta->perform_queries(DBDELTA_ALL);
	//debug($results);
	if(is_numeric($version) && is_numeric(CMS_VERSION)) {
		while($version < CMS_VERSION) {
			$upgrade_function = str_replace('.', '_', 'upgrade_'.$version);
			if(function_exists($upgrade_function)) {
				$upgrade_function($params);
			}
			$version += '0.01';
		}
	}

	//Add the expanse version
	setOption('expanseversion',CMS_VERSION);
	return true;
}
// function upgrade_1_0($params){
// 	//set the default index file option
// 	setOption('index_file', 'index.php');
// 	//set the default usage for using clean urls
// 	setOption('use_clean_urls', 0);
// }
// function upgrade_1_1($params){
// 	$files_to_delete = array(
// 		'css/expanse.css',
// 		'funcs/misc/theme_prototype/css/theme_prototype.css',
// 		'funcs/misc/theme_prototype/javascript/theme_prototype.js',
// 		'modules/blog/blog.def.php',
// 		'modules/blog/blog.mod.php',
// 		'modules/events/events.def.php',
// 		'modules/events/events.mod.php',
// 		'modules/gallery/gallery.def.php',
// 		'modules/gallery/gallery.mod.php',
// 		'modules/links/links.def.php',
// 		'modules/links/links.mod.php',
// 		'modules/pages/pages.def.php',
// 		'modules/pages/pages.mod.php',
// 		'modules/press/press.def.php',
// 		'modules/press/press.mod.php');
// 	foreach($files_to_delete as $file){
// 		if(!file_exists(EXPANSEPATH."/$file")){continue;}
// 		unlink(EXPANSEPATH."/$file");
// 	}
// }
// function upgrade_1_2($params){
// 	global $Database;
// 	if(!getOption('active_plugins')){
// 		$active_plugins = $params['active_plugins'];
// 		setOption('active_plugins', $active_plugins);
// 	}
// 	setOption('language', 'en-us');
// 	$Database->Query("UPDATE {$Database->Prefix}sections as a SET a.public = 1, a.order_rank = a.id WHERE a.cat_type != 'pages' AND a.pid=0");
// 	$Database->Query("UPDATE {$Database->Prefix}items as a SET a.menu_order = a.id WHERE a.type = 'static'");
// }
/*function upgrade_1_1(){
global $Database;
//add section admin setting
$Database->Query("ALTER TABLE `{$Database->Prefix}users` ADD `section_admin` tinyint(1) NOT NULL default '0' AFTER `admin`");
//set the default index file option
setOption('index_file', 'index.php');
//set the default usage for using clean urls
setOption('use_clean_urls', 0);
}
function updateAdmins(){
$sections = new Expanse('sections');
$users = new Expanse('users');
$ids = array();
$allsections = $sections->GetList(array(array('pid', '=', 0)));
$alladmins = $users->GetList(array(array('admin', '=', 1)));
foreach($allsections as $k => $v){
$ids[] = $v->id;
}
$_SESSION['permissions'] = $ids;
unset($_SESSION['menu']);
$ids = serialize($ids);
foreach($alladmins as $i => $val){
$users->Get($val->id);
$users->permissions = $ids;
$users->Save();
}
}*/
function same_user($php, $ftp) {
	$phpuser = posix_getpwuid(fileowner($php));
	$phpuser = trim($phpuser['name']);
	$ftpuser = posix_getpwuid(fileowner($ftp));
	$ftpuser = trim($ftpuser['name']);
	return ($phpuser == $ftpuser);
}
