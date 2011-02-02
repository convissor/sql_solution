<?php /** @package SQLSolution_Test */

/**
 * Lists the tests for SQL Solution's general methods
 *
 * Usage:  phpunit AllGeneralTests
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_AllGeneralTests {
	/**
	 * PHPUnit's function for setting tests to be run
	 *
	 * @return PHPUnit_Framework_TestSuite  the tests to be run
	 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('SQL Solution Driver Bundle');

		$suite->addTestSuite('SQLSolution_Test_General_CopyObjectContentsIntoSQLTest');
		$suite->addTestSuite('SQLSolution_Test_General_DatetimeToUnixTest');
		$suite->addTestSuite('SQLSolution_Test_General_FieldLengthAssocArrayTest');
		$suite->addTestSuite('SQLSolution_Test_General_FieldLengthEnumArrayTest');
		$suite->addTestSuite('SQLSolution_Test_General_FieldNameEnumArrayTest');
		$suite->addTestSuite('SQLSolution_Test_General_ParseSafeMarkupTest');
		$suite->addTestSuite('SQLSolution_Test_General_RecordIntoThisTest');
		$suite->addTestSuite('SQLSolution_Test_General_RunQueryRowsNeededTest');
		$suite->addTestSuite('SQLSolution_Test_General_TimestampToUnixTest');

        return $suite;
	}
}
