<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's InsertID methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_InsertID extends PHPUnit_Framework_TestCase {
	/**
	 * The SQL Solution class name or ODBC DSN name being tested
	 * @var string
	 */
	protected $driver;

	/**
	 * The SQL Solution object being tested
	 * @var object
	 */
	protected $sql;

	/**
	 * PHPUnit's method for setting needed properties, etc, before each test
	 */
	protected function setUp() {
		$this->sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'];

		$this->driver = get_class($this->sql);
		if ($this->driver == 'SQLSolution_ODBCUser') {
			$this->driver = $this->sql->SQLDSN;
		}

		sqlsolution_unlink_sqlite($this->sql);

		// If class or ODBC DSN indicates PostgreSQL, add sequence.
		if (preg_match('/postgres|pgsql/i', $this->driver)) {
			$this->sql->SQLQueryString = 'CREATE SEQUENCE sqlsolutionseq';
			$this->sql->RunQuery(__FILE__, __LINE__);
		}

		// If class or ODBC DSN indicates MySQL, add auto increment.
		if (preg_match('/mysql/i', $this->driver)) {
			$inc_def = 'INTEGER NOT NULL AUTO_INCREMENT';
		} elseif (preg_match('/postgres|pgsql/i', $this->driver)) {
			$inc_def = "integer NOT NULL DEFAULT nextval('sqlsolutionseq')";
		} else {
			$inc_def = 'INTEGER NOT NULL';
		}

		$this->sql->SQLQueryString = "CREATE TABLE sqlsolution (
				inc $inc_def,
				ins INTEGER,
				PRIMARY KEY (inc))";
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * PHPUnit's method for unsetting needed properties, etc, after each test
	 */
	protected function tearDown() {
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'DROP TABLE sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		// If class or ODBC DSN indicates PostgreSQL, drop sequence.
		if (preg_match('/postgres|pgsql/i', $this->driver)) {
			$this->sql->SQLQueryString = 'DROP SEQUENCE sqlsolutionseq';
			$this->sql->RunQuery(__FILE__, __LINE__);
		}

		$this->sql->Disconnect(__FILE__, __LINE__);
		sqlsolution_unlink_sqlite($this->sql);
		$this->sql = null;
	}

	public function testInsertID() {
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (ins) VALUES (11)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
		$this->assertEquals(1, $id);

		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (ins) VALUES (22)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 22', 'sqlsolutionseq');
		$this->assertEquals(2, $id);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInsertIDNoConnection() {
		if (preg_match('/ODBC/', get_class($this->sql))) {
			$this->markTestSkipped('ODBC runs query, so automatically reconnects.');
		}

		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (ins) VALUES (11)';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
		$this->assertEquals(1, $id);

		$this->sql->Disconnect(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInsertIDBadConnection() {
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
		$this->assertEquals(1, $id);

		$this->sql->SQLConnection = 'FUBAR';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInsertIDNonInsertQuery() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 11', 'sqlsolutionseq');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInsertIDIncorrectWhere() {
		if (!preg_match('/ODBC/', get_class($this->sql))) {
			$this->markTestSkipped('This test is only for ODBC');
		}

		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (ins) VALUES (11)';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$id = $this->sql->InsertID(__FILE__, __LINE__, 'sqlsolution', 'inc',
				'ins = 89798', 'sqlsolutionseq');
	}
}
