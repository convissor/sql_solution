<?php

/**
 * SQL Solution's MySQL connection information
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see SQLSolution/General.php
 */

/**
 * SQL Solution's MySQL connection information
 *
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_MySQLUser extends SQLSolution_MySQLSpecifics {
	/**
	 * The host name or IP address (and port, if desired) of the database server
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
	 * Should calling connect with the same host/username/password
	 * combination cause a new link to be created?
	 *
	 * If false, the existing link is returned.  If true, a new connection
	 * is formed.
	 *
	 * @var boolean
	 */
	public $SQLNewLink = false;

	/**
	 * MySQL configuration options
	 *
	 * This is optional.  Use 0 for none or any combination of the following
	 * bitwised constants: MYSQL_CLIENT_COMPRESS, MYSQL_CLIENT_IGNORE_SPACE
	 * or MYSQL_CLIENT_INTERACTIVE.
	 *
	 * @link http://php.net/ref.mysql
	 * @var integer
	 */
	public $SQLClientFlags = 0;


	/**
	 * Automatically sets basic properties when instantiating a new object
	 */
	public function __construct($Escape = 'Y', $Safe = 'N') {
		$this->SQLClassName = get_class($this);
		$this->SQLEscapeHTML = $Escape;
		$this->SQLSafeMarkup = $Safe;
	}
}
