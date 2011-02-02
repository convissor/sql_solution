<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's FieldNameEnumArray method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_FieldNameEnumArrayTest extends SQLSolution_Test_General {
	public function testFieldNameEnumArray() {
		$this->sql->SQLQueryString = 'SELECT first FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldNameEnumArray(__FILE__, __LINE__);
		$this->assertEquals('first', $return[0]);
	}

	public function testFieldNameEnumArrayNoRecords() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldNameEnumArray(__FILE__, __LINE__);
		$this->assertEquals('first', $return[0]);
	}
}
