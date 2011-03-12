<?php

/**
 * SQL Solution's SQLite3 specific code
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's SQLite3 specific methods
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @since Class available since release 7.0
 */
class SQLSolution_SQLite3Specifics extends SQLSolution_Customizations {

	/*
	 * C O N N E C T I O N      S E C T I O N
	 */

	/**
	 * Establishes a connection to the database server
	 *
	 * @return void
	 *
	 * @uses SQLSolution_SQLite3User  for the authentication information
	 * @link http://www.SqlSolution.info/sql-man.htm#Connect
	 */
	public function Connect($FileName, $FileLine) {
		ini_set('track_errors', 1);

		try {
			$this->SQLConnection = new SQLite3(
				$this->SQLDbName,
				$this->SQLFlags,
				$this->SQLEncryptionKey
			);
		} catch (Exception $e) {
			$this->KillQuery($FileName, $FileLine, $e->getMessage());
		}
	}

	/**
	 * This extension doesn't have persistent connections; calls are
	 * forwarded to Connect()
	 *
	 * @return void
	 * @link http://www.SqlSolution.info/sql-man.htm#PersistentConnect
	 */
	public function PersistentConnect($FileName, $FileLine) {
		$this->Connect($FileName, $FileLine);
	}

	/**
	 * This extension doesn't have this feature; calls are
	 * forwarded to Connect()
	 *
	 * @return void
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
			@$this->SQLConnection->close();
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
		return is_a($this->SQLConnection, 'SQLite3');
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

		$this->SQLRecordSet = null;
		$this->SQLRecordSetFieldCount = 0;
		$this->SQLRecordSetRowCount = 0;

		$this->SQLRecordSet = @$this->SQLConnection->query($this->SQLQueryString);
		if ($this->SQLRecordSet) {
			if (is_object($this->SQLRecordSet) && $this->SQLRecordSet->numColumns()) {
				$this->SQLRecordSetFieldCount = $this->SQLRecordSet->numColumns();
				while ($this->SQLRecordSet->fetchArray(SQLITE3_NUM)) {
					$this->SQLRecordSetRowCount++;
				}
				$this->SQLRecordSet->reset();
			}
		} else {
			$this->KillQuery($FileName, $FileLine,
					@$this->SQLConnection->lastErrorMsg());
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

		$this->SQLRecordSet = null;
		$this->SQLRecordSetFieldCount = 0;
		$this->SQLRecordSetRowCount = 0;

		$this->SQLRecordSet = @$this->SQLConnection->query($this->SQLQueryString);
		if ($this->SQLRecordSet) {
			if (is_object($this->SQLRecordSet) && $this->SQLRecordSet->numColumns()) {
				$this->SQLRecordSetFieldCount = $this->SQLRecordSet->numColumns();
				while ($this->SQLRecordSet->fetchArray(SQLITE3_NUM)) {
					$this->SQLRecordSetRowCount++;
				}
				$this->SQLRecordSet->reset();
			}
			return 1;
		} else {
			switch (@$this->SQLConnection->lastErrorCode()) {
				case 19:
					// Couldn't insert/update record due to duplicate key.
					break;

				default:
					// Some other database error.  Trap it.
					$this->KillQuery($FileName, $FileLine,
							@$this->SQLConnection->lastErrorMsg());
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
		if (is_object($this->SQLRecordSet)) {
			@$this->SQLRecordSet->finalize();
		}
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
		if (!is_object($this->SQLRecordSet)) {
			$this->KillQuery($FileName, $FileLine, 'SQLRecordSet is not set.');
		}

		// Avoid segfault (http://bugs.php.net/bug.php?id=53464).
		if ($FieldNumber >= $this->SQLRecordSetFieldCount) {
			$this->KillQuery($FileName, $FileLine, 'Invalid field number');
		}

		$Output = @$this->SQLRecordSet->columnName($FieldNumber);
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine,
					$this->SQLConnection->lastErrorMsg());
		}
	}

	/**
	 * This extension does not have this capability
	 *
	 * Disabling this because PHP's extension works on the data contained in a
	 * given row, not column definitions.
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldType
	 */
	public function FieldType($FileName, $FileLine, $FieldNumber) {
		$this->KillQuery($FileName, $FileLine,
				'SQLite3 does not provide FieldLength.');
	}

	/**
	 * This extension does not have this capability
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldLength
	 */
	public function FieldLength($FileName, $FileLine, $FieldNumber) {
		$this->KillQuery($FileName, $FileLine,
				'SQLite3 does not provide FieldLength.');
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
		if (!is_object($this->SQLRecordSet) || !$this->SQLRecordSetRowCount) {
			return null;
		}

		$php_errormsg = '';

		$Row = @$this->SQLRecordSet->fetchArray(SQLITE3_ASSOC);
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
		if (!is_object($this->SQLRecordSet) || !$this->SQLRecordSetRowCount) {
			return null;
		}

		$php_errormsg = '';

		$Row = @$this->SQLRecordSet->fetchArray(SQLITE3_NUM);
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
		if (!$this->CheckConnection()) {
			$this->KillQuery($FileName, $FileLine, 'Not connected.');
		}

		$Output = $this->SQLConnection->lastInsertRowID();
		if ($Output) {
			return $Output;
		} else {
			$this->KillQuery($FileName, $FileLine, 'No auto_increment id. '
					. 'This query does not generate one or this table does '
					. 'not have one.');
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
		if (!is_object($this->SQLRecordSet)) {
			return;
		}
		if ($Row) {
			$this->KillQuery($FileName, $FileLine,
					'SQLite3 only supports going to $Row 0.');
		} else {
			@$this->SQLRecordSet->reset();
		}
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
			return "'" . SQLite3::escapeString($Value) . "'";
		}
	}
}
