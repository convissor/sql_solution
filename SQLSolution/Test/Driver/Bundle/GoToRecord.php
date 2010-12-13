<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's GoToRecord methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_GoToRecord extends PHPUnit_Extensions_OutputTestCase {
	/**
	 * The SQL Solution class being tested
	 * @var object
	 */
	protected $sql;

	protected $expected = array(
		array('first' => 11, 'second' => 111),
		array('first' => 22, 'second' => 222),
		array('first' => 33, 'second' => 333),
	);

	/**
	 * PHPUnit's method for setting needed properties, etc, before each test
	 */
	protected function setUp() {
		$this->sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'];

		sqlsolution_unlink_sqlite($this->sql);

		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution (first INTEGER, second INTEGER)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (11, 111)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (22, 222)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (33, 333)';
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


	public function testGoToRecord0() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->RecordAsAssocArray(__FILE__, __LINE__);

		$row_id = 0;
		$this->sql->GoToRecord(__FILE__, __LINE__, $row_id);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($this->expected[$row_id], $row_db, "Mismatch in row $row_id");
	}

	public function testGoToRecordNoRows() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	public function testGoToRecord1And2() {
		if (preg_match('/SQLite3/', get_class($this->sql))) {
			$this->markTestSkipped('Can only go to row 0');
		}

		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$row_id = 1;
		$this->sql->GoToRecord(__FILE__, __LINE__, $row_id);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($this->expected[$row_id], $row_db, "Mismatch in row $row_id");

		$row_id = 2;
		$this->sql->GoToRecord(__FILE__, __LINE__, $row_id);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($this->expected[$row_id], $row_db, "Mismatch in row $row_id");
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testGoToRecord1Unsupported() {
		if (!preg_match('/SQLite3/', get_class($this->sql))) {
			$this->markTestSkipped('Supports GoToRecord');
		}

		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->GoToRecord(__FILE__, __LINE__, 1);
	}
}
