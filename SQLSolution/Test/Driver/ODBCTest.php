<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's odbc driver
 *
 * USAGE:  From the "Test" directory, execute the following command:
 * phpunit Driver_ODBCUser
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_ODBCTest extends SQLSolution_Test_Driver_Bundle {
	public static function suite() {
		$GLOBALS['SQLSOLUTION_TEST_USER_CLASS'] = 'SQLSolution_ODBCUser';
		parent::checkSkipDbms(__CLASS__, 'SQLDSN');
		return parent::suite();
	}
}
