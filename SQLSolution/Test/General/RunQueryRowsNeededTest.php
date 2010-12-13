<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's RunQuery_RowsNeeded method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_RunQueryRowsNeededTest extends SQLSolution_Test_General {
	public function testRunQueryRowsNeeded() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->assertEquals(1, $this->sql->RunQuery_RowsNeeded(__FILE__, __LINE__, 3));
	}

	public function testRunQueryRowsNeededFalse() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution ORDER BY first';
		$this->assertEquals(null, $this->sql->RunQuery_RowsNeeded(__FILE__, __LINE__, 1));
	}

	public function testRunQueryRowsNeededNoRecords() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->assertEquals(null, $this->sql->RunQuery_RowsNeeded(__FILE__, __LINE__, 1));
	}
}
