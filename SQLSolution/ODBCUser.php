<?php

/**
 * SQL Solution's ODBC connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's ODBC connection information
 *
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_ODBCUser extends SQLSolution_ODBCSpecifics {
	/**
	 * Must be a system DSN.
	 * @var string
	 */
	public $SQLDSN = '';

	/**
	 * The user name for logging in to the database
	 * @var string
	 */
	public $SQLUser = '';

	/**
	 * The password for logging in to the database
	 * @var string
	 */
	public $SQLPassword = '';

	/**
	 * The type of cursor to be used with this connection.
	 *
	 * This is optional.  Use 0 for none or one of the following constants:
	 * SQL_CUR_USE_IF_NEEDED, SQL_CUR_USE_ODBC, SQL_CUR_USE_DRIVER
	 * or SQL_CUR_DEFAULT
	 *
	 * @var integer
	 */
	public $SQLCursor = 0;


	/**
	 * Automatically sets basic properties when instantiating a new object
	 */
	public function __construct($Escape = 'Y', $Safe = 'N') {
		$this->SQLClassName = get_class($this);
		$this->SQLEscapeHTML = $Escape;
		$this->SQLSafeMarkup = $Safe;
	}
}
