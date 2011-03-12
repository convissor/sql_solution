<?php

/**
 * A set of PHP classes to simplify integrating databases with web pages
 *
 * Requires PHP 5 or later.
 *
 * This file contains the SQLSolution_General and SQLSolution_ErrorHandler
 * classes.  All other files contain one class.
 *
 * For more information on using this program, read the manual on line
 * via the link below.
 *
 * If you're reading this code, please have mercy.  It was written in the
 * early days of PHP 4, so uses older ways of doing things.  Plus it is one
 * of the first PHP programs I wrote.
 *
 * SQL Solution is a trademark of The Analysis and Solutions Company.
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */

/**
 * Methods and properties common to all SQLSolution DBMS varieties
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_General extends SQLSolution_ErrorHandler {

	//  NOTE: most properties and methods are public for historical reasons.

	//  DO NOT FILL IN THESE PROPERTIES HERE !

	/**
	 * The database server connection resource
	 * @var mixed
	 */
	public $SQLConnection;

	/**
	 * The query string to execute
	 * @var string
	 */
	public $SQLQueryString = '';

	/**
	 * The alternate query string to execute for transform output
	 * @var string
	 */
	public $SQLAlternateQueryString = '';

	/**
	 * The query string for gathering the vertical (y axis) labels
	 * @var string
	 */
	public $SQLVerticalQueryString = '';

	/**
	 * The query string for gathering the horizontal (x axis) labels
	 * @var string
	 */
	public $SQLHorizontalQueryString = '';

	/**
	 * The query string for gathering the credits
	 * @var string
	 */
	public $SQLCreditQueryString = '';

	/**
	 * The query result set resource
	 * @var mixed
	 */
	public $SQLRecordSet;

	/**
	 * The number of rows in a result set
	 * @var integer
	 */
	public $SQLRecordSetRowCount = 0;

	/**
	 * The number of columns in a result set
	 * @var integer
	 */
	public $SQLRecordSetFieldCount = 0;

	/**
	 * The XHTML tag that has been opened, if any
	 * @var string
	 */
	public $SQLTagStarted = '';

	/**
	 * Should output be passed through htmlspecialchars()?
	 * @var string
	 */
	public $SQLEscapeHTML = '';

	/**
	 * Should output be passed through our Safe Markup parser?
	 * @var string
	 */
	public $SQLSafeMarkup = '';

	/**
	 * The name of our DBMS class that's being used
	 * @var string
	 */
	public $SQLClassName = '';

	/**
	 * The data store for RecordIntoThis() and CopyObjectContentsIntoSQL()
	 * @var array
	 */
	private $record = array();


	/**
	 * Retrieves the data saved by RecordIntoThis() or CopyObjectContentsIntoSQL()
	 *
	 * Automatically called by PHP when code requests a property.
	 *
	 * @return mixed  the desired value
	 * @uses SQLSolution_General::$record  as the data store
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->record)) {
			return $this->record[$name];
		} else {
			return null;
		}
	}


	/*
	 * Q U E R Y     S E C T I O N
	 *
	 * Continued from DBMS specific classes.
	 */

	/**
	 * Executes $this->SQLQueryString and checks if the result contains the
	 * desired number of rows
	 *
	 * Creates a database connection if one doesn't exist yet.
	 *
	 * @return mixed  1 if the number of rows matches, null if not
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RunQuery_RowsNeeded
	 */
	public function RunQuery_RowsNeeded($FileName, $FileLine, $RowsNeeded = 1) {
		$this->RunQuery($FileName, $FileLine);

		if ($this->SQLRecordSetRowCount == $RowsNeeded) {
			return 1;
		}
	}


	/*
	 * R E C O R D      D A T A      S E C T I O N
	 */

	/**
	 * Places the next record's data into variables within this object
	 *
	 * @return mixed  1 on success, 0 if result has no records, null if end
	 *                of result set has been passed
	 *
	 * @uses SQLSolution_General::$record  as the data store
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordIntoThis
	 */
	public function RecordIntoThis($FileName, $FileLine) {
		if (empty($this->SQLRecordSetRowCount)) {
			return 0;
		}

		$Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
				. "error when RIT() was called by $FileName", $FileLine);
		if ($Record) {
			$this->record = array_merge($this->record, $Record);
			return 1;
		} else {
			for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
				$FieldName = $this->FieldName('FieldName() had error when '
						. "RAT() was called by $FileName",
						$FileLine, $FieldCounter);
				$this->record[$FieldName] = '';
			}
		}
	}


	/*
	 * F I E L D      D E F I N I T I O N S      S E C T I O N
	 *
	 * Continued from DBMS specific classes.
	 */

	/**
	 * Returns an enumerated array containing the size of each column in the
	 * current record set
	 *
	 * @return array
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldLengthEnumArray
	 */
	public function FieldLengthEnumArray($FileName, $FileLine) {
		$Output = array();
		for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
			$Output[] = $this->FieldLength('FieldLength() had error when '
					. "FLEA() was called by $FileName", $FileLine, $Counter);
		}
		return $Output;
	}

	/**
	 * Returns an associative array containing the size of each column in
	 * the record set
	 *
	 * @return array
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldLengthAssocArray
	 */
	public function FieldLengthAssocArray($FileName, $FileLine) {
		$Output = array();
		for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
			$Name = $this->FieldName('FieldName() had error when '
					. "FLEA() was called by $FileName", $FileLine, $Counter);
			$Output[$Name] = $this->FieldLength('FieldLength() had error when '
					. "FLEA() was called by $FileName", $FileLine, $Counter);
		}
		return $Output;
	}

	/**
	 * Returns an enumerated array containing name of each column in
	 * the record set
	 *
	 * @return array
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#FieldNameEnumArray
	 */
	public function FieldNameEnumArray($FileName, $FileLine) {
		$Output = array();
		for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
			$Output[] = $this->FieldName('FieldName() had error when '
					. "FNEA() was called by $FileName", $FileLine, $Counter);
		}
		return $Output;
	}


	/*
	 * U T I L I T I E S      S E C T I O N
	 */


	/**
	 * Runs htmlspecialchars() and Safe Markup on the row's data as needed
	 *
	 * This method is used by RecordAsAssocArray() and RecordAsEnumArray().
	 * Don't call it directly.
	 *
	 * @param array $Row  the record to be processed
	 * @param array $SkipSafeMarkup  an array of field numbers (starting at 0)
	 *                               to not parse safe markup on
	 * @param int $Counter  an optional property to be incremented for each row
	 * @return array  the sanitized data
	 */
	protected function processRow($Row, $SkipSafeMarkup, &$Counter = 0) {
		foreach ($Row as $Key => $Val) {
			if ($this->SQLEscapeHTML != 'N') {
				$Val = htmlspecialchars($Val);
			}
			if ($this->SQLSafeMarkup == 'Y') {
				if (!in_array($Key, $SkipSafeMarkup)) {
					$Val = $this->ParseSafeMarkup($Val);
				}
			}
			$Row[$Key] = $Val;
		}
		$Counter++;
		return $Row;
	}

	/**
	 * Transforms Safe Markup into HTML
	 * @return string
	 */
	public function ParseSafeMarkup($Val) {
		// Use once-only (? >) where possible to improve efficiency.
		// Use S (spend more time analyzing) where appropriate.

		// Plain URI's
		$Val = preg_replace('@(?<!::)(http://|https://|ftp://|gopher://|news:|mailto:)((?>[\w/!#$%&\'()*+,.:;=?\@~-]+))@iS', '<a href="\\1\\2">\\1\\2</a>', $Val);
		// Ancored URI's
		$Val = preg_replace('@::a::(http://|https://|ftp://|gopher://|news:|mailto:)([\w/!#$%&\'()*+,.:;=?\@~-]+)([\w/!#$%&\'()*+:;=?\@~-])::a::(.*)::/a::@iU', '<a href="\\1\\2\\3">\\4</a>', $Val);
		// Paired Elements
		$Val = preg_replace('@::(/?)(p|ul|ol|li|dl|dt|dd|b|i|code|sup|pre|tt|em|blockquote)::@', '<\\1\\2>', $Val);
		// Empty Elements
		$Val = preg_replace('/::(br|hr)::/', '<\\1 />', $Val);
		// Character Entity References
		$Val = preg_replace('/(?<!::a)::((?>[a-z]{2,6})|(?>[a-z]{2,4})(?>[0-9]{1,2}))::/iS', '&\\1;', $Val);
		// Numeric Character References
		$Val = preg_replace('/(?<!::a)::((?>[0-9]{2,4}))::/S', '&#\\1;', $Val);
		return $Val;
	}

	/**
	 * Produces a Unix timestamp from a database timestamp string
	 *
	 * <kbd>20371231235959</kbd> is max unix_timestamp in MySQL.
	 *
	 * @return int  the Unix timestamp in seconds since the epoch
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#TimestampToUnix
	 */
	public function TimestampToUnix($FileName, $FileLine, $Time) {
		if (!preg_match('/^(19[7-9][0-9]|20[0-2][0-9]|203[0-7])(0[0-9]|1[0-2])'
				. '([0-2][0-9]|3[0-1])([0-1][0-9]|2[0-3])([0-5][0-9])'
				. '([0-5][0-9])$/', $Time, $Atom)
			|| !checkdate($Atom[2], $Atom[3], $Atom[1]))
		{
			$this->KillQuery($FileName, $FileLine, "$Time is an invalid "
					. 'timestamp. Perhaps the date exceeds Unix timestamp '
					. 'or input was not formatted properly.');
		} else {
			return @mktime($Atom[4], $Atom[5], $Atom[6], $Atom[2], $Atom[3],
					$Atom[1]);
		}
	}

	/**
	 * Produces a Unix timestamp from a database datetime string
	 *
	 * <kbd>2037-12-31 23:59:59</kbd> is max unix_timestamp in MySQL.
	 *
	 * @return int  the Unix timestamp in seconds since the epoch
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#DatetimeToUnix
	 */
	public function DatetimeToUnix($FileName, $FileLine, $Time) {
		if (!preg_match('/^(19[7-9][0-9]|20[0-2][0-9]|203[0-7])-(0[0-9]|1[0-2])-'
				. '([0-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9]):'
				. '([0-5][0-9])$/', $Time, $Atom)
			|| !checkdate($Atom[2],$Atom[3],$Atom[1]))
		{
			$this->KillQuery($FileName, $FileLine, "$Time is an invalid "
					. 'timestamp. Perhaps the date exceeds Unix timestamp '
					. 'or input was not formatted properly.');
		} else {
			return @mktime($Atom[4], $Atom[5], $Atom[6], $Atom[2], $Atom[3],
					$Atom[1]);
		}
	}

	/**
	 * Keeps track of a repeating processes
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#OverflowProtectionInSQL
	 */
	public function OverflowProtectionInSQL($FileName, $FileLine, $Break = 5) {
		if (!is_int($Break)) {
			$this->KillQuery($FileName, $FileLine, 'Break argument for '
					. 'Overflow Protection must be integer.');
		}

		static $OverflowCounter = 1;

		if ($OverflowCounter++ > $Break) {
			// Write an event into the error log.
			$this->KillQuery($FileName, $FileLine, 'Overflow Protection '
					. 'was triggered.');
		}
	}

	/**
	 * Copies the contents of another object into the present object
	 *
	 * @return void
	 *
	 * @uses SQLSolution_General::$record  as the data store
	 * @link http://www.SqlSolution.info/sql-man.htm#CopyObjectContentsIntoSQL
	 */
	public function CopyObjectContentsIntoSQL($FileName, $FileLine, $From) {
		global $$From;

		if (!is_object($$From)) {
			$this->KillQuery($FileName, $FileLine, 'The object you called, '
					. "$From, doesn't seem to be set.");
		}

		$this->record = array_merge($this->record, get_object_vars($$From));
	}


	/*
	 * R E S U L T      D I S P L A Y      S E C T I O N
	 */


	/**
	 * Displays contents of the next record as an HTML table
	 *
	 * @return mixed  1 on success, 0 if result has no records, null if end
	 *                of result set has been passed
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordAsTable
	 */
	public function RecordAsTable($FileName, $FileLine, $Opt = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
			$Wrap = ' nowrap';
		} else {
			$Wrap = '';
		}

		echo '<table';

		if (isset($Opt['border'])) {
			echo ' border="' . $Opt['border'] . '"';
		} else {
			echo ' border="1"';
		}

		if (isset($Opt['cellpadding'])) {
			echo ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			echo ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			echo ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			echo ' width="' . $Opt['width'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		echo $Class;

		if (isset($Opt['summary'])) {
			echo ' summary="' . $Opt['summary'] . '"';
		}

		echo ">\n";

		$this->SQLTagStarted = 'table';

		if (isset($Opt['caption'])) {
			echo ' <caption';
			if (isset($Opt['captionalign'])) {
				echo ' align="' . $Opt['captionalign'] . '"';
			}
			echo "$Class>" . $Opt['caption'] . "</caption>\n";
		}

		if (!$this->SQLRecordSetRowCount) {
			echo " <tr$Class><td$Class>No Such Record Exists</td></tr>\n";
			echo "</table>\n";
			$this->SQLTagStarted = '';
			return 0;
		}

		$Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
				. "error when RAT() was called by $FileName", $FileLine);
		if ($Record) {
			if (!isset($Opt['nohead'])) {
				echo " <tr$Class><th scope=\"col\"$Class>Field</th>";
				echo "<th scope=\"col\"$Class>Value</th></tr>\n";
			}
			foreach ($Record as $Key => $Val) {
				echo " <tr valign=\"top\"$Class>";
				echo "<td align=\"right\"$Wrap$Class><b$Class>$Key:</b></td>";
				echo "<td$Wrap$Class>";
				echo ($Val != '') ? ($Val) : ('&nbsp;') . "</td></tr>\n";
			}
			echo "</table>\n";
			$this->SQLTagStarted = '';
			return 1;
		}
	}

	/**
	 * Displays an entire Record Set as an unordered or ordered list
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordSetAsList
	 */
	public function RecordSetAsList($FileName, $FileLine, $Opt = '', $Col = '') {
		echo $this->GetRecordSetAsList($FileName, $FileLine, $Opt, $Col);
	}

	/**
	 * Returns an entire Record Set as an unordered or ordered list
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetRecordSetAsList
	 */
	public function GetRecordSetAsList($FileName, $FileLine, $Opt = '', $Col = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		if (!isset($Opt['delimiter'])) {
			$Opt['delimiter'] = ', ';
		}

		if (isset($Opt['list']) && $Opt['list'] == 'ol') {
			$out = '<ol';
			$this->SQLTagStarted = 'ol';
		} else {
			$out = '<ul';
			$this->SQLTagStarted = 'ul';
		}

		if (isset($Opt['type'])) {
			$out .= ' type="' . $Opt['type'] . '"';
		}

		if (isset($Opt['start'])) {
			$out .= ' start="' . $Opt['start'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		$out .= $Class;

		$out .= ">\n";

		// If there are no records in a record set...
		if (!$this->SQLRecordSetRowCount) {
			// print a message saying there are no records.
			$out .= " <li$Class>There are no matching records.</li>\n";

		} else {
			$this->GoToRecord('GoToRecord() had error when RSAL() was '
					. "called by $FileName", $FileLine);
			$Output = '';

			$FieldNames = $this->FieldNameEnumArray('FieldNameEnumArray() had '
					. "error when RSAL() was called by $FileName", $FileLine);

			if (!is_array($Col)) {
				$Col = array();
			}

			while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
				   . "had error when RSAL() was called by $FileName",
				   $FileLine))
			{
				$Output = array();
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++)
				{
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
							&& isset($Col[$FieldNames[$FieldCounter]]['linkurl']))
						{
							$Output[] = '<a href="'
								. $Col[$FieldNames[$FieldCounter]]['linkurl']
								. $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']]
								. "\"$Class>"
								. $Record[$FieldNames[$FieldCounter]]
								. '</a>';

						} else {
							$Output[] = $Record[$FieldNames[$FieldCounter]];
						}
					}
				}

				$out .= " <li$Class>" . implode($Opt['delimiter'], $Output)
						. "</li>\n";
			}
		}

		$out .= "</$this->SQLTagStarted>\n";
		$this->SQLTagStarted = '';
		return $out;
	}

	/**
	 * Displays an entire Record Set as an HTML table
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordSetAsTable
	 */
	public function RecordSetAsTable($FileName, $FileLine, $Opt = '', $Col = '') {
		echo $this->GetRecordSetAsTable($FileName, $FileLine, $Opt, $Col);
	}

	/**
	 * Returns an entire Record Set as an HTML table
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetRecordSetAsTable
	 */
	public function GetRecordSetAsTable($FileName, $FileLine, $Opt = '', $Col = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
			$Wrap = ' nowrap';
		} else {
			$Wrap = '';
		}

		$out = '<table';

		if (isset($Opt['border'])) {
			$out .= ' border="' . $Opt['border'] . '"';
		} else {
			$out .= ' border="1"';
		}

		if (isset($Opt['cellpadding'])) {
			$out .= ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			$out .= ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			$out .= ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			$out .= ' width="' . $Opt['width'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		$out .= $Class;

		if (isset($Opt['summary'])) {
			$out .= ' summary="' . $Opt['summary'] . '"';
		}

		$out .= ">\n";

		$this->SQLTagStarted = 'table';

		if (isset($Opt['caption'])) {
			$out .= ' <caption';
			if (isset($Opt['captionalign'])) {
				$out .= ' align="' . $Opt['captionalign'] . '"';
			}
			$out .= "$Class>" . $Opt['caption'] . "</caption>\n";
		}

		// If there are no records in a record set...
		if (!$this->SQLRecordSetRowCount) {
			// print a message saying there are no records.
			$out .= " <tr$Class><td$Class>There are no matching records.";
			$out .= "</td></tr>\n";

		} else {
			// else, there are some records, so let's display them.

			$this->GoToRecord('GoToRecord() had error when RSATbl() was '
					. "called by $FileName", $FileLine);

			if (!is_array($Col)) {
				$Col = array();
			}

			// Grab field names and lay out column headers:
			$VisibleFields = 0;
			if (!isset($Opt['nohead'])) {
				$out .= " <tr valign=\"top\"$Class>";
				$this->SQLTagStarted = 'tr';
				for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
					$FieldNames[] = $this->FieldName('FieldName() had error '
							. "when RSATbl() was called by $FileName",
							$FileLine,  $FieldCounter);
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						$VisibleFields++;
						$out .= "<th scope=\"col\"$Class>";
						$out .= "$FieldNames[$FieldCounter]</th>";
					}
				}
				$out .= "</tr>\n";
			} else {
				for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
					$FieldNames[] = $this->FieldName('FieldName() had error '
							. "when RSATbl() was called by $FileName",
							$FileLine,  $FieldCounter);
				}
			}

			#~:~# Go through each Record in RecordSet...
			while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
				   . "had error when RSATbl() was called by $FileName",
				   $FileLine))
			{
				$out .= " <tr valign=\"top\"$Class>";
				#~:~#
				#~:~# For each field in the row...
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++)
				{
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
							&& isset($Col[$FieldNames[$FieldCounter]]['linkurl']))
						{
							$out .= "<td$Wrap$Class><a href=\"";
							$out .= $Col[$FieldNames[$FieldCounter]]['linkurl'];
							$out .= $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
							$out .= "\"$Class>";
							$out .= $Record["$FieldNames[$FieldCounter]"];
							$out .= "</a></td>";
						} else {
							$out .= "<td$Wrap$Class>";
							if (isset($Record[$FieldNames[$FieldCounter]])
								&& $Record[$FieldNames[$FieldCounter]] != '')
							{
								$out .= $Record[$FieldNames[$FieldCounter]];
							} else {
								$out .= '&nbsp;';
							}
							$out .= '</td>';
						}
					}
				}
				#~:~#
				$out .= "</tr>\n";
				#~:~#
			}

			// If CreditString has something in it,
			// layout credits in bottom row of HTML table.
			if ($this->SQLCreditQueryString) {
				$CreditSQL = new $this->SQLClassName;

				$CreditSQL->SQLQueryString = $this->SQLCreditQueryString;
				$CreditSQL->RunQuery('RunQuery() had error when RSATbl() was '
						. "called by $FileName", $FileLine);

				$out .= "<tr$Class><td colspan=\"$VisibleFields\"$Class>";
				$out .= "Credits:\n";
				$CreditSQL->SQLTagStarted = 'td';
				$CreditSQL->RecordSetAsList('RecordSetAsList() had error when '
						. "RSATbl() was called by $FileName", $FileLine, $Opt);
				$out .= "</td></tr>\n";

				$this->SQLCreditQueryString = '';
			}
		}

		$out .= "</table>\n";
		$this->SQLTagStarted = '';
		return $out;
	}

	/**
	 * Turns your query results into XML output
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordSetAsXML
	 */
	public function RecordSetAsXML($FileName, $FileLine, $Opt = '', $Col = '') {
		echo $this->GetRecordSetAsXML($FileName, $FileLine, $Opt, $Col);
	}

	/**
	 * Returns your query results as XML
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetRecordSetAsXML
	 */
	public function GetRecordSetAsXML($FileName, $FileLine, $Opt = '', $Col = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		if (!isset($Opt['settag'])) {
			$Opt['settag'] = 'recordset';
		}

		if (!isset($Opt['recordtag'])) {
			$Opt['recordtag'] = 'record';
		}

		$out = '';
		switch (isset($Opt['prefix']) . ':' . isset($Opt['namespace'])) {
			case '1:':
			case '1:0':
				$Opt['prefix'] = $Opt['prefix'] . ':';
				$out .= '<' . $Opt['prefix'] . $Opt['settag'] . ">\n";
				break;
			case ':1':
			case '0:1':
				$out .= "<{$Opt['settag']} xmlns=\"{$Opt['namespace']}\">\n";
				$Opt['prefix'] = '';
				break;
			case '1:1':
				$out .= "<{$Opt['settag']} xmlns:{$Opt['prefix']}=\"";
				$out .= $Opt['namespace'] . "\">\n";
				$Opt['prefix'] = $Opt['prefix'] . ':';
				break;
			default:
				$out .= '<' . $Opt['settag'] . ">\n";
				$Opt['prefix'] = '';
		}

		$this->SQLTagStarted = $Opt['prefix'] . $Opt['settag'];

		if (!$this->SQLRecordSetRowCount) {
			$out .= '<' . $Opt['prefix'] . $Opt['recordtag'];
			$out .= '>There are no matching records.</';
			$out .= $Opt['prefix'] . $Opt['recordtag'] . ">\n";
		} else {
			$this->GoToRecord('GoToRecord() had error when RSAX() was called '
					. "by $FileName", $FileLine);
			$FieldNames = $this->FieldNameEnumArray('FieldNameEnumArray() had '
					. "error when RSAX() was called by $FileName", $FileLine);

			if (!is_array($Col)) {
				$Col = array();
			}

			// Go through each Record in RecordSet...
			while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
				   . "had error when RSAX() was called by $FileName",
				   $FileLine))
			{
				$out .= ' <' . $Opt['prefix'] . $Opt['recordtag'] . '>';
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++) {
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
							&& isset($Col[$FieldNames[$FieldCounter]]['linkurl']))
						{
							$out .= '<' . $Opt['prefix'];
							$out .= "$FieldNames[$FieldCounter]><";
							$out .= $Opt['prefix'] . 'a href="';
							$out .= $Col[$FieldNames[$FieldCounter]]['linkurl'];
							$out .= $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
							$out .= "\">" . $Record["$FieldNames[$FieldCounter]"];
							$out .= "</{$Opt['prefix']}a></{$Opt['prefix']}";
							$out .= "$FieldNames[$FieldCounter]>";
						} else {
							$out .= '<' . $Opt['prefix'];
							$out .= "$FieldNames[$FieldCounter]>";
							if (isset($Record[$FieldNames[$FieldCounter]])
								&& $Record[$FieldNames[$FieldCounter]] != '')
							{
								$out .= $Record[$FieldNames[$FieldCounter]];
							} else {
								$out .= '&nbsp;';
							}
							$out .= '</' . $Opt['prefix'];
							$out .= "$FieldNames[$FieldCounter]>";
						}
					}
				}
				$out .= '</' . $Opt['prefix'] . $Opt['recordtag'] . ">\n";
			}
		}

		$out .= '</' . $Opt['prefix'] . $Opt['settag'] . ">\n\n";
		return $out;
	}

	/**
	 * Makes a standard normalized Record Set look like a spreadsheet
	 * in an HTML table
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordSetAsTransform
	 */
	public function RecordSetAsTransform($FileName, $FileLine, $Opt = '') {
		echo $this->GetRecordSetAsTransform($FileName, $FileLine, $Opt);
	}

	/**
	 * Returns a standard normalized Record Set look like a spreadsheet
	 * in an HTML table
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetRecordSetAsTransform
	 */
	public function GetRecordSetAsTransform($FileName, $FileLine, $Opt = '') {
		if (!isset($this->SQLVerticalQueryString)
			|| !isset($this->SQLHorizontalQueryString))
		{
			$this->KillQuery($FileName, $FileLine, 'Horizontal and/or '
				. 'vertical query strings are not set. Please set them '
				. 'and try again.');
		}

		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		if (!isset($Opt['flip'])) {
			$Opt['flip'] = 'Y';
		}

		if (!isset($Opt['verticallabel'])) {
			$Opt['verticallabel'] = '';
		}

		if (!isset($Opt['horizontallabel'])) {
			$Opt['horizontallabel'] = '';
		}

		if (!isset($this->SQLAlternateQueryString)) {
			$Opt['flip'] = 'N';
		}

		if ($this->SQLRecordSetRowCount) {
			$this->GoToRecord('GoToRecord() had error when RSATran() was '
					. "called by $FileName", $FileLine);
		}
		$Counter = 0;
		$ActualVerticalLabel = '';

		// List and count labels for default vertical axis.
		$VerticalSQL = new $this->SQLClassName;
		$VerticalSQL->SQLQueryString = $this->SQLVerticalQueryString;
		$VerticalSQL->RunQuery('RunQuery() had error when RSATran() was '
				. "called by $FileName", $FileLine);

		// List and count labels for default horizontal axis.
		$HorizontalSQL = new $this->SQLClassName;
		$HorizontalSQL->SQLQueryString = $this->SQLHorizontalQueryString;
		$HorizontalSQL->RunQuery('RunQuery() had error when RSATran() was '
				. "called by $FileName", $FileLine);

		if (2 > ($HorizontalSQL->SQLRecordSetRowCount
				+ $VerticalSQL->SQLRecordSetRowCount)) {
			$this->KillQuery($FileName, $FileLine, 'Problem with Transform '
					. 'Queries: horizontal and/or vertical query results '
					. 'contain no rows. Fix your queries and try again.');
		}

		/*
		 * The axis with the most labels goes into the rows, so table is
		 * easier to read.  If we need to flip things around, transpose the
		 * appropriate variables.  But only do this if $Opt['flip'] = Y
		 */
		if ($HorizontalSQL->SQLRecordSetRowCount > $VerticalSQL->SQLRecordSetRowCount
			&& $Opt['flip'] == 'Y')
		{
			$this->SQLQueryString = $this->SQLAlternateQueryString;
			$HorizontalSQL->SQLRecordSet = $VerticalSQL->SQLRecordSet;

			$Transposer = $VerticalSQL->SQLRecordSetRowCount;
			$VerticalSQL->SQLRecordSetRowCount
					= $HorizontalSQL->SQLRecordSetRowCount;
			$HorizontalSQL->SQLRecordSetRowCount = $Transposer;

			$Transposer = $Opt['verticallabel'];
			$Opt['verticallabel'] = $Opt['horizontallabel'];
			$Opt['horizontallabel'] = $Transposer;
		}

		// Run the main query.
		$this->RunQuery('RunQuery() had error when RSATran() was called '
				. "by $FileName", $FileLine);

		// Test to see if query results line up correcly.
		if ($HorizontalSQL->SQLRecordSetRowCount
				* $VerticalSQL->SQLRecordSetRowCount
				!= $this->SQLRecordSetRowCount)
		{
			$this->KillQuery($FileName, $FileLine, 'Problem with Transform '
					. 'Queries: the number of records from the main query '
					. 'does not have same number of records as the '
					. 'horizontal query * the vertical query. Fix your '
					. 'queries and try again.');
		}

		$out = '<table';

		if (isset($Opt['border'])) {
			$out .= ' border="' . $Opt['border'] . '"';
		} else {
			$out .= ' border="1"';
		}

		if (isset($Opt['cellpadding'])) {
			$out .= ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			$out .= ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			$out .= ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			$out .= ' width="' . $Opt['width'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		$out .= $Class;

		$out .= ' summary="';
		if (isset($Opt['summary'])) {
			$out .= $Opt['summary'] . '. ';
		}
		if (isset($Opt['title'])) {
			$out .= 'Top cell spans whole table, contains Title. ';
		}
		if ($this->SQLCreditQueryString) {
			$out .= 'Bottom cell spans whole table, listing credits. ';
		}
		$out .= 'Rows contain ' . $Opt['verticallabel'] . '. Columns contain '
				. $Opt['horizontallabel'] . ".\">\n";

		$this->SQLTagStarted = 'table';

		if (isset($Opt['caption'])) {
			$out .= ' <caption';
			if (isset($Opt['captionalign'])) {
				$out .= ' align="' . $Opt['captionalign'] . '"';
			}
			$out .= "$Class>" . $Opt['caption'] . "</caption>\n";
		}

		// Add line breaks as needed.
		$Opt['verticallabel'] = preg_replace('/(.)(&[a-zA-Z]{2,4};)*/',
				'\\1\\2<br />', $Opt['verticallabel']);
		$Opt['verticallabel'] = preg_replace('/\\\2</',
				'<', $Opt['verticallabel']);
		$Opt['verticallabel'] = preg_replace('/(.)(&)/',
				'\\1<br />\\2', $Opt['verticallabel']);

		#..# HTML table header layout.
		if (isset($Opt['title'])) {
			$out .= " <tr$Class><td colspan=\"";
			$out .= ($HorizontalSQL->SQLRecordSetRowCount + 2);
			$out .= "\"$Class><h2$Class>" . $Opt['title'] . "</h2></td>\n";
		}
		$out .= " <tr$Class><td colspan=\"2\"";
		if (isset($Opt['background'])) {
			$out .= ' background="' . $Opt['background'] . '"';
		}
		$out .= ' rowspan="2" alt="Blank cell for formatting purposes."';
		$out .= "$Class>&nbsp;</td><th colspan=\"";
		$out .= "$HorizontalSQL->SQLRecordSetRowCount\" align=\"left\"";
		$out .= "scope=\"colgroup\"$Class>" . $Opt['horizontallabel'];
		$out .= "</th></tr>\n <tr$Class>";
		#..#
		#..#  HTML column header layout.
		$this->SQLTagStarted = 'tr';
		while ($Record = $HorizontalSQL->RecordAsEnumArray('RecordAsEnumArray() '
			   . "had error when RSATran() was called by $FileName",
			   $FileLine))
		{
			$out .= "<th scope=\"col\"$Class>" . $Record[0] . '</th>';
		}
		#..#
		$out .= "</tr>\n";

		#'.'#  Print HTML table rows.
		for ($Vlocation = 0; $Vlocation < $VerticalSQL->SQLRecordSetRowCount; $Vlocation++) {
			#'.'#  Get the next record.
			$this->SQLTagStarted = 'table';
			$Record = $this->RecordAsEnumArray('RecordAsEnumArray() had error '
					. "when RSATran() was called by $FileName", $FileLine);
			#'.'#
			#'.'#  Is this the first HTML table row?
			if ($Vlocation == 0) {
				#'.'#  Yes, so print out the side header plus the
				#'.'#  HTML table row title for the first record.
				$out .= " <tr$Class><td rowspan=\"";
				$out .= "$VerticalSQL->SQLRecordSetRowCount\" align=\"center\" ";
				$out .= "valign=\"top\" scope=\"rowgroup\"$Class><b$Class>";
				$out .= $Opt['verticallabel'] . "</b></td>\n      ";
				$out .= "<td nowrap scope=\"row\"$Class><b$Class>";
				$out .= $Record[0] . '</b></td>';
			} else {
				#'.'#  No, so just print out the HTML table row title
				$out .= " <tr$Class><td nowrap scope=\"row\"$Class><b$Class>";
				$out .= $Record[0] . '</b></td>';
			}

			#'.'#  Print out just the quantity for the first HTML table column.
			#'.'#  If field not blank...    print data...  else print space
			$out .= "<td align=\"right\"$Class>";
			if ($Record[1] != '') {
				$out .= $Record[1];
			} else {
				$out .= '&nbsp;';
			}
			$out .= '</td>';

			#'.'#
			#'.'#  Print out quantities for the remaining HTML table columns.
			$this->SQLTagStarted = 'tr';
			for ($Hlocation = 1; $Hlocation < $HorizontalSQL->SQLRecordSetRowCount; $Hlocation++) {
				$Record = $this->RecordAsEnumArray('RecordAsEnumArray() had '
					. "error when RSATran() was called by $FileName",
					$FileLine);
				#'.'#  If field not blank...    print data...  else print space
				$out .= "<td align=\"right\"$Class>";
				if ($Record[1] != '') {
					$out .= $Record[1];
				} else {
					$out .= '&nbsp;';
				}
				$out .= '</td>';
			}
			#'.'#
			$out .= "</tr>\n";
		}

		unset($Record);
		unset($VerticalSQL);

		$CreditWidth = $HorizontalSQL->SQLRecordSetRowCount + 2;
		unset($HorizontalSQL);

		// If CreditString has something in it, layout credits in bottom row
		// of HTML table.
		if ($this->SQLCreditQueryString) {
			$CreditSQL = new $this->SQLClassName;

			$CreditSQL->SQLQueryString = $this->SQLCreditQueryString;
			$CreditSQL->RunQuery('RunQuery() had error when RSATran() was '
					. "called by $FileName", $FileLine);

			$out .= " <tr$Class><td colspan=\"$CreditWidth\"$Class>Credits:\n";
			$CreditSQL->SQLTagStarted = 'td';
			$CreditSQL->RecordSetAsList('RecordSetAsList() had error when '
					. "RSATran() was called by $FileName", $FileLine, $Opt);
			$out .= " </td></tr>\n";

			$this->SQLCreditQueryString = '';
		}

		$out .= "</table>\n";
		$this->SQLTagStarted = '';
		$this->SQLQueryString = '';
		$this->SQLAlternateQueryString = '';
		return $out;
	}


	/*
	 * F O R M      G E N E R A T I O N      S E C T I O N
	 */


	/**
	 * Creates list boxes for use in forms
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#OptionListGenerator
	 */
	public function OptionListGenerator($FileName, $FileLine, $Opt = '') {
		echo $this->GetOptionListGenerator($FileName, $FileLine, $Opt);
	}

	/**
	 * Returns a list boxes for use in forms
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetOptionListGenerator
	 */
	public function GetOptionListGenerator($FileName, $FileLine, $Opt = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				switch ($Key) {
					case 'default':
					case 'where':
					case 'orderby':
					case 'groupby':
					case 'add':
						break;

					default:
						$Opt[$Key] = htmlspecialchars($Val);
				}
			}
		} else {
			$Opt = array();
		}

		// Validate the arguments
		if (!isset($Opt['default'])) {
			$Opt['default'] = '';
		}

		if (!isset($Opt['name']) OR $Opt['name'] == '') {
			$this->KillQuery("OptionListGenerator() had error when called by
					$FileName", $FileLine, "'name' argument was empty.");
		}

		if (!isset($Opt['keyfield']) OR $Opt['keyfield'] == '') {
			$this->KillQuery("OptionListGenerator() had error when called by
					$FileName", $FileLine, "'keyfield' argument was empty.");
		}

		if (!isset($Opt['visiblefield']) OR $Opt['visiblefield'] == '') {
			$this->KillQuery("OptionListGenerator() had error when called by
					$FileName", $FileLine, "'visiblefield' argument was empty.");
		}

		if (!isset($Opt['where']) OR $Opt['where'] == '') {
			$this->KillQuery("OptionListGenerator() had error when called by
					$FileName", $FileLine, "'where' argument was empty.");
		}

		if (!isset($Opt['orderby']) OR $Opt['orderby'] == '') {
			$this->KillQuery("OptionListGenerator() had error when called by
					$FileName", $FileLine, "'orderby' argument was empty.");
		}

		$this->SQLQueryString = "SELECT {$Opt['keyfield']}, "
				 . "{$Opt['visiblefield']} "
				 . "FROM {$Opt['table']} WHERE {$Opt['where']} ";

		if (isset($Opt['groupby']) && $Opt['groupby'] == '') {
			$this->SQLQueryString .= "GROUP BY {$Opt['groupby']} ";
		}

		$this->SQLQueryString .= "ORDER BY {$Opt['orderby']}";

		$this->RunQuery('RunQuery() had error when OLG() was called by'
				. $FileName, $FileLine);

		// Start the list box
		$out = "\n\n<select";

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		$out .= $Class;

		if (isset($Opt['id'])) {
			$out .= ' id="' . $Opt['id'] . '"';
		}

		if (isset($Opt['size'])) {
			$out .= ' size="' . $Opt['size'] . '"';
		}

		if (isset($Opt['multiple']) && $Opt['multiple'] == 'Y') {
			$out .= ' multiple name="' . $Opt['name'] . "[]\">\n";
			if (!is_array($Opt['default'])) {
				$Opt['default'] = array($Opt['default']);
			} else {
				reset($Opt['default']);
			}
		} else {
			$out .= ' name="' . $Opt['name'] . "\">\n";
			if (is_array($Opt['default'])) {
				reset($Opt['default']);
				$Opt['default'] = array(current($Opt['default']));
			} else {
				$Opt['default'] = array($Opt['default']);
			}
		}

		if (isset($Opt['add'])) {
			foreach ($Opt['add'] as $Value => $Visible) {
				$out .= ' <option value="' . htmlspecialchars($Value) . '"';
				if (in_array($Value, $Opt['default'])) {
					$out .= ' selected="selected"';
				}
				$out .= "$Class>" . htmlspecialchars($Visible) . "</option>\n";
			}
		}

		$this->SQLTagStarted = 'select';

		// Now, get down to business...
		if (!$this->SQLRecordSetRowCount) {
			$out .= " <option value=\"\"$Class>No Matching Records</option>\n";
		} else {
			while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
				   . "had error when OLG() was called by $FileName",
				   $FileLine))
			{
				$out .= ' <option value="' . $Record[$Opt['keyfield']] . '"';
				if (in_array($Record[$Opt['keyfield']], $Opt['default'])) {
					$out .= ' selected="selected"';
				}
				$out .= "$Class>{$Record[$Opt['visiblefield']]}</option>\n";
			}

		}

		$out .= "</select>\n\n";
		return $out;
	}

	/**
	 * Creates lists of check boxes and radio buttons for use in forms
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#InputListGenerator
	 */
	public function InputListGenerator($FileName, $FileLine, $Opt = '') {
		echo $this->GetInputListGenerator($FileName, $FileLine, $Opt);
	}

	/**
	 * Returns lists of check boxes and radio buttons for use in forms
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetInputListGenerator
	 */
	public function GetInputListGenerator($FileName, $FileLine, $Opt = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				switch ($Key) {
					case 'where':
					case 'default':
					case 'add':
					case 'orderby':
					case 'groupby':
						break;

					default:
						$Opt[$Key] = htmlspecialchars($Val);
				}
			}
		} else {
			$Opt = array();
		}

		if (!isset($Opt['default'])) {
			$Opt['default'] = '';
		}

		if (!isset($Opt['name']) OR $Opt['name'] == '') {
			$this->KillQuery('OptionListGenerator() had error when called by '
					. $FileName, $FileLine, "'name' argument empty/not set.");
		}

		if (!isset($Opt['keyfield']) OR $Opt['keyfield'] == '') {
			$this->KillQuery('OptionListGenerator() had error when called by '
					. $FileName, $FileLine, "'keyfield' argument "
					. 'empty/not set.');
		}

		if (!isset($Opt['visiblefield']) OR $Opt['visiblefield'] == '') {
			$this->KillQuery('OptionListGenerator() had error when called by '
					. $FileName, $FileLine, "'visiblefield' argument "
					. 'empty/not set.');
		}

		if (!isset($Opt['where']) OR $Opt['where'] == '') {
			$this->KillQuery('OptionListGenerator() had error when called by '
					. $FileName, $FileLine, "'where' argument empty/not set.");
		}

		if (!isset($Opt['orderby']) OR $Opt['orderby'] == '') {
			$this->KillQuery('OptionListGenerator() had error when called by '
					. $FileName, $FileLine, "'orderby' argument "
					. 'empty/not set.');
		}

		if (!isset($Opt['type'])) {
			$Opt['type'] = 'checkbox';
		}

		if (empty($Opt['columns'])) {
			$Opt['columns'] = 2;
		} else {
			settype($Opt['columns'], 'integer');
			if (!$Opt['columns']) {
				$Opt['columns'] = 2;
			}
		}

		if (!isset($Opt['all'])) {
			$Opt['all'] = '';
		}

		$this->SQLQueryString = "SELECT {$Opt['keyfield']}, "
				 . "{$Opt['visiblefield']} "
				 . "FROM {$Opt['table']} WHERE {$Opt['where']} ";

		if (isset($Opt['groupby']) && $Opt['groupby'] == '') {
			$this->SQLQueryString .= "GROUP BY {$Opt['groupby']} ";
		}

		$this->SQLQueryString .= "ORDER BY {$Opt['orderby']}";

		$this->RunQuery('RunQuery() had error when ILG() was called by '
				. $FileName, $FileLine);
		// debug tool -> //    $out .= htmlspecialchars($this->SQLQueryString);

		// Start the table
		$out = '<table';

		if (isset($Opt['border'])) {
			$out .= ' border="' . $Opt['border'] . '"';
		} else {
			$out .= ' border="2"';
		}

		if (isset($Opt['cellpadding'])) {
			$out .= ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			$out .= ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			$out .= ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			$out .= ' width="' . $Opt['width'] . '"';
		} else {
			$out .= ' width="100%"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		$out .= $Class;

		if (isset($Opt['summary'])) {
			$out .= ' summary="' . $Opt['summary'] . '"';
		}

		$out .= ">\n";

		if ($Opt['type'] == 'checkbox') {
			$Bracket = '[]';
			if (!is_array($Opt['default'])) {
				$Opt['default'] = array($Opt['default']);
			} else {
				reset($Opt['default']);
			}
		} else {
			// This is a radio button list.
			$Bracket = '';
			$Opt['all'] = '';

			if (is_array($Opt['default'])) {
				reset($Opt['default']);
				$Opt['default'] = array(current($Opt['default']));
			} else {
				$Opt['default'] = array($Opt['default']);
			}
		}

		$out .= " <tr valign=\"top\"$Class>\n";

		if (empty($Opt['add'])) {
			$Opt['add'] = array();
		}

		$Adds = count($Opt['add']);

		$Items = $this->SQLRecordSetRowCount + $Adds;
		if (empty($Items)) {
			$Break = 0;
			$ColumnWidth = 0;
		} else {
			if ($Opt['columns'] > $Items) {
				$Opt['columns'] = $Items;
			}
			$Break = ceil($Items / $Opt['columns']);
			$ColumnWidth = floor(100 / $Opt['columns']);
		}

		if (!$Items) {
			$out .= '  <td><input type="' . $Opt['type'] . '" name="';
			$out .= $Opt['name'] . "$Bracket\" value=\"\" />";
			$out .= "No Matching Records</td>\n";
		} else {
			for ($ItemCounter = 1;  $ItemCounter <= $Items;) {
				$out .= "  <td nowrap width=\"$ColumnWidth%\"$Class>\n            ";
				$this->SQLTagStarted = 'td';

				for ($RowCounter = 1;  $RowCounter <= $Break;) {
					if ($ItemCounter > $Adds) {
						$Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
								. 'had error when ILG() was called by '
								. $FileName, $FileLine);
					} else {
						list($Value,$Visible) = each($Opt['add']);
						$Record[$Opt['keyfield']] = htmlspecialchars($Value);
						$Record[$Opt['visiblefield']]
								= htmlspecialchars($Visible);
					}

					$out .= '<input type="' . $Opt['type'] . '" name="';
					$out .= $Opt['name'] . "$Bracket\" value=\"";
					$out .= $Record[$Opt['keyfield']] . '"';
					if ($Opt['all'] == 'Y' || in_array(
							$Record[$Opt['keyfield']], $Opt['default'])) {
						$out .= ' checked="checked"';
					}
					$out .= "$Class />";

					if (isset($Opt['linkurl'])) {
						$out .= '<a href="' . $Opt['linkurl'];
						$out .= $Record[$Opt['keyfield']] . "\"$Class>";
						$out .= $Record[$Opt['visiblefield']] . "</a>\n    <br />";
					} else {
						$out .= $Record[$Opt['visiblefield']] . "\n    <br />";
					}

					if ($ItemCounter == $Items) {
						$out .= "\n  </td>\n";
						break 2;
					}

					$ItemCounter++;
					$RowCounter++;
				}

				$out .= "\n  </td>\n";
				$RowCounter = 1;
			}
		}

		$out .= "</tr>\n</table>\n\n";
		$this->SQLTagStarted = '';
		return $out;
	}

	/**
	 * Displays contents of the next record as input elements for an HTML form
	 *
	 * @return mixed  1 on success, 0 if result has no records, null if end
	 *                of result set has been passed
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordAsInput
	 */
	public function RecordAsInput($FileName, $FileLine, $Opt = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				$Opt[$Key] = htmlspecialchars($Val);
			}
		} else {
			$Opt = array();
		}

		echo '<table';

		if (isset($Opt['border'])) {
			echo ' border="' . $Opt['border'] . '"';
		} else {
			echo ' border="1"';
		}

		if (isset($Opt['cellpadding'])) {
			echo ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			echo ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			echo ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			echo ' width="' . $Opt['width'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		echo $Class;

		if (isset($Opt['summary'])) {
			echo ' summary="' . $Opt['summary'] . '"';
		}

		echo ">\n";

		$this->SQLTagStarted = 'table';

		if (isset($Opt['caption'])) {
			echo ' <caption';
			if (isset($Opt['captionalign'])) {
				echo ' align="' . $Opt['captionalign'] . '"';
			}
			echo "$Class>" . $Opt['caption'] . "</caption>\n";
		}

		if (!$this->SQLRecordSetRowCount) {
			echo " <tr$Class><td$Class>No Such Record Exists</td></tr>\n";
			echo "</table>\n";
			return 0;
		}

		$Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
				. "error when RAI() was called by $FileName", $FileLine);
		if ($Record) {
			$Counter = 0;
			if (!isset($Opt['nohead'])) {
				echo " <tr$Class><th scope=\"col\"$Class>Field</th>";
				echo "<th scope=\"col\" abbr=\"Input\"$Class>Data Input</th>";
				echo "</tr>\n";
			}
			foreach ($Record as $Key => $Val) {
				$Length = $this->FieldLength('FieldLength() had error when RAI'
						. "() was called by $FileName", $FileLine, $Counter);

				echo " <tr valign=\"top\"$Class>\n  <td align=\"right\"";
				echo "$Class><b$Class>$Key:</b></td>\n  <td$Class>";

				if ($Length > 59) {
					if ($Length < 240) {
						$Rows = floor($Length / 60);
					} else {
						$Rows = 4;
					}
					echo "<textarea wrap name=\"$Key\" cols=\"60\" ";
					echo "rows=\"$Rows\" maxlength=\"$Length\"$Class>";
					echo "$Val</textarea>";

				} else {
					echo "<input type=\"text\" name=\"$Key\" value=\"$Val\" ";
					echo "size=\"$Length\" maxlength=\"$Length\"$Class />";
				}

				echo "</td>\n </tr>\n";
				$Counter++;
			}

			echo "</table>\n";
			$this->SQLTagStarted = '';
			return 1;
		}
	}

	/**
	 * Displays an entire Record Set as an HTML table with input fields
	 * for use on a form
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#RecordSetAsInput
	 */
	public function RecordSetAsInput($FileName, $FileLine, $Opt = '', $Col = '') {
		echo $this->GetRecordSetAsInput($FileName, $FileLine, $Opt, $Col);
	}

	/**
	 * Returns an entire Record Set as an HTML table with input fields
	 * for use on a form
	 *
	 * @return string
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#GetRecordSetAsInput
	 */
	public function GetRecordSetAsInput($FileName, $FileLine, $Opt = '', $Col = '') {
		if (is_array($Opt)) {
			foreach ($Opt as $Key => $Val) {
				if ($Key != 'default') {
					$Opt[$Key] = htmlspecialchars($Val);
				}
			}
		} else {
			$Opt = array();
		}

		if (!isset($Opt['keyfield']) OR !$Opt['keyfield']) {
			$this->KillQuery('RecordSetAsInput() had error when called by '
					. $FileName, $FileLine, 'keyfield argument was empty.');
		}

		if (!isset($Opt['default'])) {
			$Opt['default'] = '';
		}

		if (empty($Opt['name'])) {
			$Opt['name'] = 'Input';
		}

		if (empty($Opt['inputheader'])) {
			$Opt['inputheader'] = 'Input';
		}

		if (!isset($Opt['size'])) {
			$Opt['size'] = '3';
		}

		if (empty($Opt['maxlength'])) {
			$Opt['maxlength'] = '3';
		}

		if (empty($Opt['all'])) {
			$Opt['all'] = 'N';
		}

		if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
			$Wrap = ' nowrap';
		} else {
			$Wrap = '';
		}

		if (!isset($Opt['type'])) {
			$Opt['type'] = '';
		}

		switch ($Opt['type']) {
			case 'text':
				if (is_array($Opt['default'])) {
					reset($Opt['default']);
				} else {
					$Opt['default'] = array();
				}
				break;
			case 'radio':
				if (is_array($Opt['default'])) {
					reset($Opt['default']);
					$Opt['default'] = array(current($Opt['default']));
				} else {
					$Opt['default'] = array($Opt['default']);
				}
				break;
			default:
				if (is_array($Opt['default'])) {
					reset($Opt['default']);
				} else {
					$Opt['default'] = array($Opt['default']);
				}
		}

		// Start the table
		$out = '<table';

		if (isset($Opt['border'])) {
			$out .= ' border="' . $Opt['border'] . '"';
		} else {
			$out .= ' border="1"';
		}

		if (isset($Opt['cellpadding'])) {
			$out .= ' cellpadding="' . $Opt['cellpadding'] . '"';
		}

		if (isset($Opt['cellspacing'])) {
			$out .= ' cellspacing="' . $Opt['cellspacing'] . '"';
		}

		if (isset($Opt['align'])) {
			$out .= ' align="' . $Opt['align'] . '"';
		}

		if (isset($Opt['width'])) {
			$out .= ' width="' . $Opt['width'] . '"';
		}

		$Class = '';
		if (isset($Opt['class'])) {
			$Class .= ' class="' . $Opt['class'] . '"';
		}
		if (isset($Opt['id'])) {
			$Class .= ' id="' . $Opt['id'] . '"';
		}
		$out .= $Class;

		if (isset($Opt['summary'])) {
			$out .= ' summary="' . $Opt['summary'] . '"';
		}

		$out .= ">\n";

		$this->SQLTagStarted = 'table';

		if (isset($Opt['caption'])) {
			$out .= ' <caption';
			if (isset($Opt['captionalign'])) {
				$out .= ' align="' . $Opt['captionalign'] . '"';
			}
			$out .= "$Class>" . $Opt['caption'] . "</caption>\n";
		}

		// Now, get down to business
		if (!$this->SQLRecordSetRowCount) {
			$out .= " <tr$Class><td$Class>There are no matching records.";
			$out .= "</td></tr>\n";
		} else {
			$this->GoToRecord('GoToRecord() had error when RSAI() was called '
					. "by $FileName", $FileLine);

			if (!is_array($Col)) {
				$Col = array();
			}

			// Lay out column headers
			if (!isset($Opt['nohead'])) {
				$out .= " <tr valign=\"top\"$Class><th scope=\"col\"$Class>";
				$out .= $Opt['inputheader'] . "</th>";
				$this->SQLTagStarted = 'tr';
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++) {
					$FieldNames[] = $this->FieldName('FieldName() had error '
							. "when RSAI() was called by $FileName",
							$FileLine, $FieldCounter);
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						$out .= "<th scope=\"col\"$Class>";
						$out .= "$FieldNames[$FieldCounter]</th>";
					}
				}
				$out .= "</tr>\n";
			} else {
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++)
				{
					$FieldNames[] = $this->FieldName('FieldName() had error '
							. "when RSATbl() was called by $FileName",
							$FileLine, $FieldCounter);
				}
			}

			#~:~# Go through each Record in RecordSet
			while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
				   . "had error when RSAI() was called by $FileName",
				   $FileLine))
			{
				$out .= " <tr valign=\"top\"$Class>";

				#~:~# Display the form input field.
				$out .= "<td align=\"center\"$Wrap$Class><input name=\"";
				$out .= $Opt['name'];

				switch ($Opt['type']) {
					case 'text':
						$out .= '[' . $Record[$Opt['keyfield']] . ']"';
						$out .= 'type="text" value="';
						if (key($Opt['default']) == $Record[$Opt['keyfield']]) {
							$out .= substr(current($Opt['default']),
									0, $Opt['maxlength']) ;
							next($Opt['default']);
						}
						$out .= '" size="' . $Opt['size'] . '" maxlength="';
						$out .= $Opt['maxlength'] . '"';
						break;
					case 'radio':
						$out .= '" type="radio" value="';
						$out .= $Record[$Opt['keyfield']] . '"';
						if ($Opt['all'] == 'Y' || in_array(
								$Record[$Opt['keyfield']], $Opt['default'])) {
							$out .= ' checked="checked"';
						}
						break;
					default:
						$out .= '[]" type="checkbox" value="';
						$out .= $Record[$Opt['keyfield']] . '"';
						if ($Opt['all'] == 'Y' || in_array(
								$Record[$Opt['keyfield']], $Opt['default'])) {
							$out .= ' checked="checked"';
						}
				}

				$out .= "$Class /></td>";

				#~:~# For each field in the RecordSet...
				for ($FieldCounter = 0;
						$FieldCounter < $this->SQLRecordSetFieldCount;
						$FieldCounter++)
				{
					if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
						if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
							&& isset($Col[$FieldNames[$FieldCounter]]['linkurl']))
						{
							$out .= "<td scope=\"row\"$Wrap$Class><a href=\"";
							$out .= $Col[$FieldNames[$FieldCounter]]['linkurl'];
							$out .= $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
							$out .= "\"$Class>";
							$out .= $Record["$FieldNames[$FieldCounter]"];
							$out .= "</a></td>";
						} else {
							$out .= "<td$Wrap$Class>";
							if (isset($Record[$FieldNames[$FieldCounter]])
								&& $Record[$FieldNames[$FieldCounter]] != '')
							{
								$out .= $Record[$FieldNames[$FieldCounter]];
							} else {
								$out .= '&nbsp;';
							}
							$out .= '</td>';
						}
					}
				}
				#~:~#
				$out .= "</tr>\n";
				#~:~#
			}
		}

		$out .= "</table>\n";
		$this->SQLTagStarted = '';
		return $out;
	}
}

/**
 * The error handler class
 *
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_ErrorHandler {

	/**
	 * Gracefully handles errors. Called automatically when a problem arises.
	 *
	 * @return void
	 *
	 * @link http://www.SqlSolution.info/sql-man.htm#KillQuery
	 */
	public function KillQuery($FileName, $FileLine, $Message) {
		/*
		 * Close any tags which were started in order to ensure
		 * error pages contain "well-formed" XML syntax.
		 */
		switch ($this->SQLTagStarted) {
			case 'td':
			case 'th':
				echo "</$this->SQLTagStarted>";
			case 'tr':
				echo '</tr>';
			case 'table':
				echo "</table>\n";
				break;
			case '':
				break;

			default:
				echo "</$this->SQLTagStarted>\n";
		}

		echo "\n<h3>A Database Problem Occurred.\n";
		echo '<br />Please make a note of the present time and what you were ';
		echo "doing.\n<br />Then contact the System Administrator.</h3>\n";

		// debug tool -> // echo "\n<p>File: " . htmlspecialchars($FileName) . "\n<br />Line: $FileLine\n</p>\n\n<p>Error:<br />\n" . htmlspecialchars($Message) . "\n</p>\n\n<p>Most Recent Query String:<br />\n" . htmlspecialchars($this->SQLQueryString) . "\n</p>\n\n";

		echo "</body></html>\n\n";

		// for phpunit tests -> // user_error($Message);
		exit;
	}
}
