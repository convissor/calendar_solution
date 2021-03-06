<?php

/**
 * SQL Solution's SQLite specific code
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's SQLite specific methods
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_SQLiteSpecifics extends SQLSolution_Customizations {

	/*
	 * C O N N E C T I O N      S E C T I O N
	 */

	/**
	 * Establishes a connection to the database server
	 *
	 * @return void
	 *
	 * @uses SQLSolution_SQLiteUser  for the authentication information
	 * @link http://www.SqlSolution.info/sql-man.htm#Connect
	 */
	public function Connect($FileName, $FileLine) {
		ini_set('track_errors', 1);
		$php_errormsg = '';

		$this->SQLConnection = @sqlite_open($this->SQLDbName, $this->SQLPermissions)
				or die ($this->KillQuery($FileName, $FileLine, $php_errormsg));
	}

	/**
	 * Establishes a persistent connection to the database server
	 *
	 * @return void
	 *
	 * @uses SQLSolution_SQLiteUser  for the authentication information
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#PersistentConnect
	 */
	public function PersistentConnect($FileName, $FileLine) {
		ini_set('track_errors', 1);
		$php_errormsg = '';

		$this->SQLConnection = @sqlite_popen($this->SQLDbName, $this->SQLPermissions)
				or die ($this->KillQuery($FileName, $FileLine, $php_errormsg));
	}

	/**
	 * This extension doesn't have this feature; calls are
	 * forwarded to Connect()
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#ObtainHandle
	 */
	public function ObtainHandle($FileName, $FileLine) {
		$this->Connect($FileName, $FileLine);
	}

	/**
	 * Closes the current database server connection
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#Disconnect
	 */
	public function Disconnect($FileName, $FileLine) {
		if ($this->CheckConnection()) {
			@sqlite_close($this->SQLConnection);
		}
		$this->SQLConnection = null;
	}

	/**
	 * Determines if a database connection exists
	 *
	 * @return bool  does a connection exist?
	 *
	 * @since Method available since release 7.0
	 */
	public function CheckConnection() {
		if (is_resource($this->SQLConnection)
			&& get_resource_type($this->SQLConnection) == 'sqlite database')
		{
			return true;
		} else {
			return false;
		}
	}


	/*
	 * Q U E R Y      S E C T I O N
	 */


	/**
	 * Executes $this->SQLQueryString
	 *
	 * Creates a database connection if one doesn't exist yet.
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RunQuery
	 */
	public function RunQuery($FileName, $FileLine) {
		if (empty($this->SQLQueryString)) {
			$this->KillQuery($FileName, $FileLine, 'Must set SQLQueryString first');
		}

		if (!$this->CheckConnection()) {
			$this->Connect($FileName, $FileLine);
		}

		$php_errormsg = '';

		$this->SQLRecordSet = @sqlite_query($this->SQLConnection,
				$this->SQLQueryString);

		if ($this->SQLRecordSet) {
			if (!$this->SQLRecordSetFieldCount =
					@sqlite_num_fields($this->SQLRecordSet))
			{
				$this->SQLRecordSetFieldCount = 0;
			}

			if (!$this->SQLRecordSetRowCount =
					@sqlite_num_rows($this->SQLRecordSet))
			{
				$this->SQLRecordSetRowCount = 0;
			}
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Executes $this->SQLQueryString and ensures the insert did not create
	 * duplicate records
	 *
	 * Creates a database connection if one doesn't exist yet.
	 *
	 * @return mixed  1 if the insert went well, null if not
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RunQuery_NoDuplicates
	 */
	public function RunQuery_NoDuplicates($FileName, $FileLine) {
		if (empty($this->SQLQueryString)) {
			$this->KillQuery($FileName, $FileLine, 'Must set SQLQueryString first');
		}

		if (!$this->CheckConnection()) {
			$this->Connect($FileName, $FileLine);
		}

		$php_errormsg = '';
		$this->SQLRecordSetFieldCount = 0;
		$this->SQLRecordSetRowCount = 0;

		$this->SQLRecordSet = @sqlite_query($this->SQLConnection,
				$this->SQLQueryString);

		if ($this->SQLRecordSet) {
			return 1;
		} else {
			if (strpos($php_errormsg, 'unique') === false) {
				$this->KillQuery($FileName, $FileLine, $php_errormsg);
			}
		}
	}

	/**
	 * Frees the current query result
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#ReleaseRecordSet
	 */
	public function ReleaseRecordSet($FileName, $FileLine) {
		$this->SQLRecordSet = null;
	}


	/*
	 * F I E L D      D E F I N I T I O N S      S E C T I O N
	 */


	/**
	 * Returns the name of the column at the specified offset in the current
	 * result set
	 *
	 * @return string  the column's name
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldName
	 */
	public function FieldName($FileName, $FileLine, $FieldNumber) {
		$php_errormsg = '';

		$Output = @sqlite_field_name($this->SQLRecordSet, $FieldNumber);
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * This extension does not have this capability
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldType
	 */
	public function FieldType($FileName, $FileLine, $FieldNumber) {
		$this->KillQuery($FileName, $FileLine, 'DBMS not capable');
	}

	/**
	 * This extension does not have this capability
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldLength
	 */
	public function FieldLength($FileName, $FileLine, $FieldNumber) {
		$this->KillQuery($FileName, $FileLine, 'DBMS not capable');
	}


	/*
	 * R E C O R D      D A T A      S E C T I O N
	 */


	/**
	 * Places the next record's data into an associative array
	 *
	 * @param array $SkipSafeMarkup  an array of field names to not parse
	 *                               safe markup on
	 *
	 * @return array  an associative array containing the current record's data
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordAsAssocArray
	 */
	public function RecordAsAssocArray($FileName, $FileLine, $SkipSafeMarkup = array()) {
		if (empty($this->SQLRecordSet)) {
			return null;
		}

		$php_errormsg = '';

		$Row = @sqlite_fetch_array($this->SQLRecordSet, SQLITE_ASSOC);
		if ($Row) {
			return $this->processRow($Row, $SkipSafeMarkup);
		} elseif ($php_errormsg != '') {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Places the next record's data into an enumerated array
	 *
	 * @param array $SkipSafeMarkup  an array of field numbers (starting at 0)
	 *                               to not parse safe markup on
	 *
	 * @return array  an enumerated array containing the record's data
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordAsEnumArray
	 */
	public function RecordAsEnumArray($FileName, $FileLine, $SkipSafeMarkup = array()) {
		if (empty($this->SQLRecordSet)) {
			return null;
		}

		$php_errormsg = '';

		$Row = @sqlite_fetch_array($this->SQLRecordSet, SQLITE_NUM);
		if ($Row) {
			return $this->processRow($Row, $SkipSafeMarkup);
		} elseif ($php_errormsg != '') {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Returns the auto increment ID from the last record inserted
	 *
	 * @return int  the insert id
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#InsertID
	 */
	public function InsertID($FileName, $FileLine, $Table = '', $Field = '',
			$Where = '', $Sequence = '')
	{
		$php_errormsg = '';

		$Output = @sqlite_last_insert_rowid($this->SQLConnection);
		if ($Output) {
			return $Output;
		} elseif ($php_errormsg == '') {
			$this->KillQuery($FileName, $FileLine, 'No auto_increment id. '
					. 'This query does not generate one or this table does '
					. 'not have one.');
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Moves the internal pointer to the specified row in a result set
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GoToRecord
	 */
	public function GoToRecord($FileName, $FileLine, $Row = 0) {
		@sqlite_seek($this->SQLRecordSet, $Row);
	}

	/**
	 * Makes input safe for use as a value in queries
	 *
	 * Surrounds the string with quote marks.  If the value is NULL, change it
	 * to the unquoted string "NULL".
	 *
	 * @param mixed $Value  the value to be escaped
	 *
	 * @return string  the escaped string
	 */
	public function Escape($FileName, $FileLine, $Value) {
		if ($Value === null) {
			return 'NULL';
		} else {
			return "'" . sqlite_escape_string($Value) . "'";
		}
	}
}
