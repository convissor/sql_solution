<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RecordIntoThis method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_RecordIntoThisTest extends SQLSolution_Test_General {
	public function testRecordIntoThis() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->RecordIntoThis(__FILE__, __LINE__);
		$this->assertEquals('11', $this->sql->first, 'Row 1, first');
		$this->assertEquals('111', $this->sql->second, 'Row 1, second');

		$this->sql->RecordIntoThis(__FILE__, __LINE__);
		$this->assertEquals('22', $this->sql->first, 'Row 2, first');
		$this->assertEquals('222', $this->sql->second, 'Row 2, second');

		$return = $this->sql->RecordIntoThis(__FILE__, __LINE__);
		$this->assertEquals('33', $this->sql->first, 'Row 3, first');
		$this->assertEquals('333', $this->sql->second, 'Row 3, second');
		$this->assertEquals(1, $return, 'Row 3, return');

		$return = $this->sql->RecordIntoThis(__FILE__, __LINE__);
		$this->assertEquals('', $this->sql->first, 'End, first');
		$this->assertEquals('', $this->sql->second, 'End, second');
		$this->assertEquals(null, $return, 'End, return');
	}

	public function testRecordIntoThisNoRecords() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->RecordIntoThis(__FILE__, __LINE__);
		$this->assertEquals(0, $return, 'return');
	}
}
