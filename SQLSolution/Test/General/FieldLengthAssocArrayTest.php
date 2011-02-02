<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's FieldLengthAssocArray method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_FieldLengthAssocArrayTest extends SQLSolution_Test_General {
	public function testFieldLengthAssocArray() {
		$this->sql->SQLQueryString = 'SELECT first FROM sqlsolution';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldLengthAssocArray(__FILE__, __LINE__);
		$this->assertEquals(11, $return['first']);
	}

	public function testFieldLengthAssocArrayNoRecords() {
		$this->sql->SQLQueryString = 'SELECT * FROM sqlsolution WHERE first = 987654321';
		$this->sql->RunQuery(__FILE__, __LINE__);

		$return = $this->sql->FieldLengthAssocArray(__FILE__, __LINE__);
		$this->assertEquals(11, $return['first']);
	}
}
