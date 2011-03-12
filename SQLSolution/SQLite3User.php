<?php

/**
 * SQL Solution's SQLite3 connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's SQLite3 connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_SQLite3User extends SQLSolution_SQLite3Specifics {
	/**
	 * Local path and name of the database file
	 * @var string
	 * @link http://php.net/sqlite3.construct
	 */
	public $SQLDbName = '';

	/**
	 * Optional flags used to determine how to open the SQLite database
	 *
	 * Warning: Do not set this value here.  Set it via the constructor
	 * when instantiating this object.
	 *
	 * @var string
	 */
	public $SQLFlags;

	/**
	 * Optional key for encrypting and decrypting an SQLite database
	 * @var string
	 * @link http://php.net/sqlite3.construct
	 */
	public $SQLEncryptionKey;


	/**
	 * Automatically sets basic properties when instantiating a new object
	 *
	 * @param int $Flags  bitwise integers represented by the following
	 *                    constants SQLITE3_OPEN_READONLY,
	 *                    SQLITE3_OPEN_READWRITE, SQLITE3_OPEN_CREATE.
	 *                    Default: SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE.
	 *                    See http://php.net/sqlite3.construct for more info.
	 */
	public function __construct($Escape = 'Y', $Safe = 'N', $Flags = null) {
		if ($Flags === null) {
			$Flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
		}
		$this->SQLFlags = $Flags;

		$this->SQLClassName = get_class($this);
		$this->SQLEscapeHTML = $Escape;
		$this->SQLSafeMarkup = $Safe;
	}
}
