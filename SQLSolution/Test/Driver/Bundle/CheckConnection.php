<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's Check Connect methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_CheckConnection extends PHPUnit_Framework_TestCase {
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


	public function testCheckConnection() {
		$this->assertFalse($this->sql->CheckConnection(), 'Connection found but should not exist.');
		$this->sql->Connect(__FILE__, __LINE__);
		$this->assertTrue($this->sql->CheckConnection(), 'Connection missing but should exist.');
	}
}
