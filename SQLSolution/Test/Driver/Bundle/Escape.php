<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RecordAsEnumArray methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_Escape extends PHPUnit_Extensions_OutputTestCase {
	/**
	 * The SQL Solution class being tested
	 * @var object
	 */
	protected $sql;

	protected $expected = array('\' " [ ] \\');

	/**
	 * PHPUnit's method for setting needed properties, etc, before each test
	 */
	protected function setUp() {
		$this->sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS']('N', 'N');

		sqlsolution_unlink_sqlite($this->sql);

		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution (first CHAR(9))';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->SQLQueryString = "INSERT INTO sqlsolution (first) VALUES "
			. "(" . $this->sql->Escape(__FILE__, __LINE__, $this->expected[0]) . ")";
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


	public function testEscape() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$row_db = $this->sql->RecordAsEnumArray(__FILE__, __LINE__);
		$this->assertEquals($this->expected, $row_db);
	}

	public function testEscapeNoConnectionAndNull() {
		$this->sql->Disconnect(__FILE__, __LINE__);

		// Should connect on it's own if needed.
		$actual = $this->sql->Escape(__FILE__, __LINE__, null);
		$this->assertEquals('NULL', $actual);
	}
}
