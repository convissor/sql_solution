<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's pgsql driver
 *
 * USAGE:  From the "Test" directory, execute the following command:
 * phpunit Driver_PostgreSQLTest
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_PostgreSQLTest extends SQLSolution_Test_Driver_Bundle {
	public static function suite() {
		if (!extension_loaded('pgsql')) {
			die("Skip: this extension is not loaded.\n");
		}
		$GLOBALS['SQLSOLUTION_TEST_USER_CLASS'] = 'SQLSolution_PostgreSQLUser';
		parent::checkSkipDbms(__CLASS__, 'SQLDbName');
		return parent::suite();
	}
}
