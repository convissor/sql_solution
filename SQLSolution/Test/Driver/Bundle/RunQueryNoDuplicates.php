<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RunQuery methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_RunQueryNoDuplicates extends PHPUnit_Extensions_OutputTestCase {
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

		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution (
			first INT NOT NULL,
			PRIMARY KEY (first))';
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * PHPUnit's method for unsetting needed properties, etc, after each test
	 */
	protected function tearDown() {
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'DROP TABLE sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->Disconnect(__FILE__, __LINE__);
		sqlsolution_unlink_sqlite($this->sql);
		$this->sql = null;
	}


	public function testRunQueryNoDuplicates() {
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first) VALUES (1)';
		$this->assertEquals(1, $this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__));
		$this->assertNull($this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__));
	}

	public function testRunQueryNoDuplicatesUpdate() {
		$this->sql->SQLQueryString = 'UPDATE sqlsolution SET first = 2';
		$this->assertEquals(1, $this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__));
	}

	/**
	 * Ensure bad connection forces reconnection
	 */
	public function testRunQueryNoDuplicatesBadConnection() {
		$this->sql->SQLConnection = 'FUBAR';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
	}

	/**
	 * Ensure bad handle forces reconnection
	 */
	public function testRunQueryNoDuplicatesBadHandle() {
		if (!property_exists($this->sql, 'SQLDbHandle')) {
			$this->markTestSkipped('This driver does not have handles.');
		}
		$this->sql->SQLDbHandle = 'FUBAR';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryNoDuplicatesEmptyString() {
		$this->sql->SQLQueryString = '';
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryNoDuplicatesUnsetString() {
		unset($this->sql->SQLQueryString);
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryNoDuplicatesUnknownTable() {
		$this->sql->SQLQueryString = 'SELECT * FROM foo';
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryNoDuplicatesBadQuery() {
		$this->sql->SQLQueryString = 'FUBAR';
		$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
	}
}
