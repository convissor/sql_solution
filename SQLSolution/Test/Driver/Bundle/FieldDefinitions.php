<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's field definition properties
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_FieldDefinitions extends PHPUnit_Extensions_OutputTestCase {
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
		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution (first, second) VALUES (1, 2)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution';
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

	protected function getExpectedLength() {
		switch (get_class($this->sql)) {
			case 'SQLSolution_MySQLUser':
			case 'SQLSolution_MySQLiUser':
				return 11;
			case 'SQLSolution_ODBCUser':
				return 10;
			case 'SQLSolution_PostgreSQLUser':
				return 4;
		}
	}

	protected function getExpectedType() {
		switch (get_class($this->sql)) {
			case 'SQLSolution_MySQLUser':
				return 'int';
			case 'SQLSolution_MySQLiUser':
				return MYSQLI_TYPE_LONG;
			case 'SQLSolution_ODBCUser':
				return 'integer';
			case 'SQLSolution_PostgreSQLUser':
				return 'int4';
		}
	}


	public function testFieldName() {
		$this->assertEquals('first', $this->sql->FieldName(__FILE__, __LINE__, 0));
	}

	public function testFieldLength() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field length');
		}
		$length = $this->getExpectedLength();
		$this->assertEquals($length, $this->sql->FieldLength(__FILE__, __LINE__, 0));
	}

	public function testFieldType() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field type');
		}
		$type = $this->getExpectedType();
		$this->assertEquals($type, $this->sql->FieldType(__FILE__, __LINE__, 0));
	}


	public function testFieldNameNoResults() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->assertEquals('first', $this->sql->FieldName(__FILE__, __LINE__, 0));
	}

	public function testFieldLengthNoResults() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field length');
		}
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$length = $this->getExpectedLength();
		$this->assertEquals($length, $this->sql->FieldLength(__FILE__, __LINE__, 0));
	}

	public function testFieldTypeNoResults() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field type');
		}
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$type = $this->getExpectedType();
		$this->assertEquals($type, $this->sql->FieldType(__FILE__, __LINE__, 0));
	}


	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldNameNoRecordSet() {
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldName(__FILE__, __LINE__, 0);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldLengthNoRecordSet() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field length');
		}
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldLength(__FILE__, __LINE__, 0);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldTypeNoRecordSet() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field type');
		}
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldType(__FILE__, __LINE__, 0);
	}


	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldNameBadRecordSet() {
		$this->sql->SQLRecordSet = 'ha ha';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldName(__FILE__, __LINE__, 0);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldLengthBadRecordSet() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field length');
		}
		$this->sql->SQLRecordSet = 'ha ha';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldLength(__FILE__, __LINE__, 0);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldTypeBadRecordSet() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field type');
		}
		$this->sql->SQLRecordSet = 'ha ha';
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldType(__FILE__, __LINE__, 0);
	}


	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldNameBadIndex() {
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldName(__FILE__, __LINE__, 3);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldLengthBadIndex() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field length');
		}
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldLength(__FILE__, __LINE__, 3);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldTypeBadIndex() {
		if (preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Does not support field type');
		}
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldType(__FILE__, __LINE__, 3);
	}


	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldLengthUnavailable() {
		if (!preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Supports field length');
		}
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldLength(__FILE__, __LINE__, 0);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFieldTypeUnavailable() {
		if (!preg_match('/SQLite/', get_class($this->sql))) {
			$this->markTestSkipped('Supports field type');
		}
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->FieldType(__FILE__, __LINE__, 0);
	}
}
