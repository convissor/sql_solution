<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's Release Record Set methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_ReleaseRecordSet extends PHPUnit_Framework_TestCase {
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


	public function testReleaseRecordSet() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		// Should fail silently.
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
	}
}
