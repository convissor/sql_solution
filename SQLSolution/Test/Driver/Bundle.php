<?php /** @package SQLSolution_Test */

/**
 * Lists the tests each of the SQL Solution's drivers must pass.  Also provides 
 * helper methods for the driver tests.
 *
 * Don't call this directly.  Call the individual driver tests directly.
 * See ../README.txt for more information.
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
abstract class SQLSolution_Test_Driver_Bundle {
	/**
	 * Dies if the current DBMS lacks connecion settings
	 *
	 * @param string $test_class  the SQLSolution_Test_Driver_<DBMS>Test class name
	 * @param string $property  the property name to check
	 *
	 * @return void
	 */
	public static function checkSkipDbms($test_class, $property) {
		$user_class = self::getUserClassFromTestClass($test_class);
		$sql = new $user_class;
		if (empty($sql->{$property})) {
			die("Skip: $user_class::\$$property is empty\n");
		}
	}

	/**
	 * Converts test class names to user class names
	 *
	 * @param string $test_class  the SQLSolution_Test_Driver_<DBMS>Test class name
	 * @return string  the SQLSolution_<DBMS>User class name
	 */
	public static function getUserClassFromTestClass($test_class) {
		return preg_replace('/.*_([a-z0-9]+)Test$/i',
				'SQLSolution_\1User', $test_class);
	}

	/**
	 * PHPUnit's function for setting tests to be run
	 *
	 * We also have it set $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'].
	 *
	 * @return PHPUnit_Framework_TestSuite  the tests to be run
	 */
	public static function suite() {
		/**
		 * @global string  the user class name for use by the driver tests
		 */
		$GLOBALS['SQLSOLUTION_TEST_USER_CLASS'] =
				self::getUserClassFromTestClass(get_called_class());

		$suite = new PHPUnit_Framework_TestSuite('SQL Solution Driver Bundle');

		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_Constructor');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_CheckConnection');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_CheckHandle');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_Connect');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_PersistentConnect');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_ObtainHandle');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_Disconnect');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_RunQuery');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_RunQueryNoDuplicates');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_ReleaseRecordSet');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_FieldDefinitions');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_RecordAsAssocArray');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_RecordAsEnumArray');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_InsertID');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_GoToRecord');
		$suite->addTestSuite('SQLSolution_Test_Driver_Bundle_Escape');

        return $suite;
	}
}
