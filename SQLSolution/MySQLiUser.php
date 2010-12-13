<?php

/**
 * SQL Solution's MySQLi connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's MySQLi connection information
 *
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_MySQLiUser extends SQLSolution_MySQLiSpecifics {
	/**
	 * The host name or IP address of the database server
	 * @var string
	 */
	public $SQLHost = '';

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
	 * The name of the database
	 * @var string
	 */
	public $SQLDbName = '';

	/**
	 * The port on which the database server is listening
	 * @var int
	 */
	public $SQLPort;

	/**
	 * The socket or pipe on which the database server is listening
	 * @var string
	 */
	public $SQLSocket = '';


	/**
	 * Automatically sets basic properties when instantiating a new object
	 */
	public function __construct($Escape = 'Y', $Safe = 'N') {
		$this->SQLClassName = get_class($this);
		$this->SQLEscapeHTML = $Escape;
		$this->SQLSafeMarkup = $Safe;
	}
}
