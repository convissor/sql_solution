<?php

/**
 * SQL Solution's SQLite connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's SQLite connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_SQLiteUser extends SQLSolution_SQLiteSpecifics {
	/**
	 * Local path and name of the database file
	 * @var string
	 */
	public $SQLDbName = '';

	/**
	 * Octal chmod permissions for the database file
	 * @var string
	 */
	public $SQLPermissions = '0600';


	/**
	 * Automatically sets basic properties when instantiating a new object
	 */
	public function __construct($Escape = 'Y', $Safe = 'N') {
		$this->SQLClassName = get_class($this);
		$this->SQLEscapeHTML = $Escape;
		$this->SQLSafeMarkup = $Safe;
	}
}
