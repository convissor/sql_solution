<?php

/**
 * SQL Solution's ODBC specific code
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's ODBC specific methods
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_ODBCSpecifics extends SQLSolution_Customizations {

	/*
	 * C O N N E C T I O N      S E C T I O N
	 */

	/**
	 * Establishes a connection to the database server
	 *
	 * @return void
	 *
	 * @uses SQLSolution_ODBCUser  for the authentication information
	 * @link http://www.SqlSolution.info/sql-man.htm#Connect
	 */
	public function Connect($FileName, $FileLine) {
		ini_set('track_errors', 1);
		$php_errormsg = '';

		$this->SQLConnection = @odbc_connect(
			$this->SQLDSN,
			$this->SQLUser,
			$this->SQLPassword,
			$this->SQLCursor
		) or die ($this->KillQuery($FileName, $FileLine, $php_errormsg));
	}

	/**
	 * Establishes a persistent connection to the database server
	 *
	 * @return void
	 *
	 * @uses SQLSolution_ODBCUser  for the authentication information
	 * @link http://www.SqlSolution.info/sql-man.htm#PersistentConnect
	 */
	public function PersistentConnect($FileName, $FileLine) {
		ini_set('track_errors', 1);
		$php_errormsg = '';

		$this->SQLConnection = @odbc_pconnect(
			$this->SQLDSN,
			$this->SQLUser,
			$this->SQLPassword,
			$this->SQLCursor
		) or die ($this->KillQuery($FileName, $FileLine, $php_errormsg));
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
			@odbc_close($this->SQLConnection);
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
			&& strpos(get_resource_type($this->SQLConnection), 'odbc link') !== false)
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
		$this->SQLRecordSetFieldCount = 0;
		$this->SQLRecordSetRowCount = 0;

		$this->SQLRecordSet = @odbc_exec($this->SQLConnection, $this->SQLQueryString);
		if ($this->SQLRecordSet) {
			$this->SQLRecordSetFieldCount =
					@odbc_num_fields($this->SQLRecordSet);

			if ($this->SQLRecordSetFieldCount) {
				$Rows = @odbc_num_rows($this->SQLRecordSet);
			} else {
				$Rows = 0;
			}
			if ($Rows == -1) {
				/*
				 * The ODBC Driver being used is so stupid that it doesn't
				 * handle num_rows.  So, manually count the rows instead.
				 */
				if (@odbc_fetch_row($this->SQLRecordSet)) {
					$Counter = 1;

					while (@odbc_fetch_row($this->SQLRecordSet)) {
						$Counter++;
					}

					@odbc_fetch_row($this->SQLRecordSet, 0);

					$this->SQLRecordSetRowCount = $Counter;
				} else {
					$this->SQLRecordSetRowCount = 0;
				}
			} else {
				$this->SQLRecordSetRowCount = $Rows;
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

		$this->SQLRecordSet = @odbc_exec($this->SQLConnection, $this->SQLQueryString);
		if ($this->SQLRecordSet) {
			$this->SQLRecordSetFieldCount = 0;
			$this->SQLRecordSetRowCount = 0;
			return 1;
		} else {
			if (stripos($php_errormsg, 'duplicate') === false) {
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
		@odbc_free_result($this->SQLRecordSet);
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

		$Output = @odbc_field_name($this->SQLRecordSet, $FieldNumber + 1);
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Returns the data type of the column at the specified offset in the
	 * current result set
	 *
	 * @return string  the column's data type
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldType
	 */
	public function FieldType($FileName, $FileLine, $FieldNumber) {
		$php_errormsg = '';

		$Output = @odbc_field_type($this->SQLRecordSet, $FieldNumber + 1);
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
	}

	/**
	 * Returns the size of the column at the specified offset in the
	 * current result set
	 *
	 * @return string  the column's size
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldLength
	 */
	public function FieldLength($FileName, $FileLine, $FieldNumber) {
		$php_errormsg = '';

		$Output = @odbc_field_len($this->SQLRecordSet, $FieldNumber + 1);
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}
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
	 * @return array  an associative array containing the record's data
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordAsAssocArray
	 */
	public function RecordAsAssocArray($FileName, $FileLine, $SkipSafeMarkup = array()) {
		if (empty($this->SQLRecordSet)) {
			return null;
		}

		$php_errormsg = '';

		if (!@odbc_fetch_into($this->SQLRecordSet, $Row)) {
			if ($php_errormsg == ''
				|| stripos($php_errormsg, 'No tuples available'))
			{
				return null;
			}
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}

		$Output = array();
		foreach ($Row as $Key => $Val) {
			$FieldName = $this->FieldName('FieldName() had error when RAAA() '
					. "was called by $FileName", "$FileLine", $Key);
			$Output[$FieldName] = $Val;
		}

		if (!empty($Output)) {
			return $this->processRow($Output, $SkipSafeMarkup);
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

		if (!@odbc_fetch_into($this->SQLRecordSet, $Row)) {
			if ($php_errormsg == ''
				|| stripos($php_errormsg, 'No tuples available'))
			{
				return null;
			}
			$this->KillQuery($FileName, $FileLine, $php_errormsg);
		}

		if (!empty($Row)) {
			return $this->processRow($Row, $SkipSafeMarkup);
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
		$this->SQLQueryString = "SELECT $Field FROM $Table WHERE $Where";
		$this->RunQuery(__FILE__,__LINE__);
		if ($this->SQLRecordSetRowCount != 1) {
			$this->KillQuery($FileName, $FileLine,
				'The terms specified returned either no Insert ID or too '
				. "many Insert ID's.");
		}
		list($Val) = $this->RecordAsEnumArray(__FILE__, __LINE__);
		return $Val;
	}

	/**
	 * Moves the internal pointer to the specified row in a result set
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GoToRecord
	 */
	public function GoToRecord($FileName, $FileLine, $Row = 0) {
		@odbc_fetch_row($this->SQLRecordSet, $Row);
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
		static $search, $replace;

		if (!isset($search)) {
			if (preg_match('/mysql|postgres|pgsql/i', $this->SQLDSN)) {
				$search = array("'", '\\');
				$replace = array("''", '\\\\');
			} else {
				$search = array("'");
				$replace = array("''");
			}
		}

		if ($Value === null) {
			return 'NULL';
		} else {
			return "'" . str_replace($search, $replace, $Value) . "'";
		}
	}
}
