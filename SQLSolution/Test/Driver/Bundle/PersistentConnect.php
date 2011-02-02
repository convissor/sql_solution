<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's PersistentConnect methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_PersistentConnect extends PHPUnit_Extensions_OutputTestCase {
	/**
	 * The SQL Solution class being tested
	 * @var object
	 */
	protected $sql;

	/**
	 * PHPUnit's method for setting needed properties, etc, before each test
	 */
	protected function setUp() {
		$this->sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'];
		sqlsolution_unlink_sqlite($this->sql);
	}

	/**
	 * PHPUnit's method for unsetting needed properties, etc, after each test
	 */
	protected function tearDown() {
		$this->sql->Disconnect(__FILE__, __LINE__);
		sqlsolution_unlink_sqlite($this->sql);
		$this->sql = null;
	}


	public function testPersistentConnect() {
		$this->sql->PersistentConnect(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPersistentConnectFailure() {
		switch (get_class($this->sql)) {
			case 'SQLSolution_MySQLUser':
			case 'SQLSolution_ODBCUser':
				$this->sql->SQLPassword = '/dev/foo';
				break;
			default:
				$this->sql->SQLDbName = '/dev/foo';
		}
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->PersistentConnect(__FILE__, __LINE__);
	}
}
