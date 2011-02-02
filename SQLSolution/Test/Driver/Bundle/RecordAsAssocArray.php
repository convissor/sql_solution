<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RecordAsAssocArray methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_RecordAsAssocArray extends PHPUnit_Framework_TestCase {
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

		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
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


	public function testRecordAsAssocArray() {
		foreach ($this->expected as $row_id => $row_expected) {
			$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
			$this->assertEquals($row_expected, $row_db, "Mismatch in row $row_id");
		}
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Subsequent calls should return null');
	}

	public function testRecordAsAssocArrayNoRows() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null when no rows returned');
	}

	public function testRecordAsAssocArrayDefinitionAndManipulationQueries() {
		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution_liz (lemon INTEGER)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null for definition queries');

		$this->sql->SQLQueryString = 'INSERT INTO sqlsolution_liz (lemon) VALUES (1)';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null for manipulation queries');

		$this->sql->SQLQueryString = 'UPDATE sqlsolution_liz SET lemon = 2';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null for manipulation queries');

		$this->sql->SQLQueryString = 'DELETE FROM sqlsolution_liz WHERE lemon = 2';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null for manipulation queries');

		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'DROP TABLE sqlsolution_liz';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertNull($row_db, 'Should be null for definition queries');
	}

	public function testRecordAsAssocArrayEscaping() {
		$raw = array('first' => '::i::italic safe markup::/i:: <br>');
		$hsc = array('first' => '::i::italic safe markup::/i:: &lt;br&gt;');
		$smu = array('first' => '<i>italic safe markup</i> <br>');
		$hsc_smu = array('first' => '<i>italic safe markup</i> &lt;br&gt;');

		$this->sql->SQLQueryString = 'CREATE TABLE sqlsolution_words (first CHAR(34))';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->SQLQueryString = "INSERT INTO sqlsolution_words (first) VALUES ('" . $raw['first'] . "')";
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->SQLEscapeHTML = 'Y';
		$this->sql->SQLSafeMarkup = 'Y';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($hsc_smu, $row_db, 'Mismatch in row Y:Y');

		$this->sql->SQLEscapeHTML = 'Y';
		$this->sql->SQLSafeMarkup = 'N';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($hsc, $row_db, 'Mismatch in row Y:N');

		$this->sql->SQLEscapeHTML = 'N';
		$this->sql->SQLSafeMarkup = 'Y';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($smu, $row_db, 'Mismatch in row N:Y');

		$this->sql->SQLEscapeHTML = 'N';
		$this->sql->SQLSafeMarkup = 'N';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->assertEquals($raw, $row_db, 'Mismatch in row N:N');

		$this->sql->SQLEscapeHTML = 'N';
		$this->sql->SQLSafeMarkup = 'Y';
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row_db = $this->sql->RecordAsAssocArray(__FILE__, __LINE__, array('first'));
		$this->assertEquals($raw, $row_db, 'Mismatch in row N:Y SkipSafeMarkup');

		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->sql->SQLQueryString = 'DROP TABLE sqlsolution_words';
		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	public function testRecordAsAssocArrayNoRecordSet() {
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);
		$this->assertNull($this->sql->RecordAsAssocArray(__FILE__, __LINE__));
	}
}
