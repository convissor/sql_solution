<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RunQuery methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_RunQuery extends PHPUnit_Extensions_OutputTestCase {
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

		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution (first INTEGER, second INTEGER)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (1, 1)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (2, 2)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (3, 3)';
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

	public function testRunQuery() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(2, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(3, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');
	}

	public function testRunQueryNoRows() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(2, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');
	}

	public function testRunQueryVarious() {
		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution_liz (lemon INTEGER)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(0, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');

		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution_liz (lemon) VALUES (1)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(0, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');

		$this->sql->SQLQueryString = 'UPDATE sqlsolution_liz SET lemon = 2';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(0, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');

		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_liz';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(1, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(1, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');

		$this->sql->SQLQueryString = 'DELETE FROM sqlsolution_liz WHERE lemon = 2';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(0, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');

		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'DROP TABLE sqlsolution_liz';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->assertEquals(0, $this->sql->SQLRecordSetFieldCount, 'Field count mismatch');
		$this->assertEquals(0, $this->sql->SQLRecordSetRowCount, 'Row count mismatch');
	}

	/**
	 * Ensure bad connection forces reconnection
	 */
	public function testRunQueryBadConnection() {
		$this->sql->SQLConnection = 'FUBAR';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * Ensure bad handle forces reconnection
	 */
	public function testRunQueryBadHandle() {
		if (!property_exists($this->sql, 'SQLDbHandle')) {
			$this->markTestSkipped('This driver does not have handles.');
		}
		$this->sql->SQLDbHandle = 'FUBAR';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryEmptyString() {
		$this->sql->SQLQueryString = '';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryUnsetString() {
		unset($this->sql->SQLQueryString);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryUnknownTable() {
		$this->sql->SQLQueryString = 'SELECT * FROM foo';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRunQueryBadQuery() {
		$this->sql->SQLQueryString = 'FUBAR';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->RunQuery(__FILE__, __LINE__);
	}
}
