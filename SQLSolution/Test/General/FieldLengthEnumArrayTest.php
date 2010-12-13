<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's FieldLengthEnumArray method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_FieldLengthEnumArrayTest extends SQLSolution_Test_General {
	public function testFieldLengthEnumArray() {
		$this->sql->SQLQueryString = 'SELECT first FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldLengthEnumArray(__FILE__, __LINE__);
		$this->assertEquals(11, $return[0]);
	}

	public function testFieldLengthEnumArrayNoRecords() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldLengthEnumArray(__FILE__, __LINE__);
		$this->assertEquals(11, $return[0]);
	}
}
