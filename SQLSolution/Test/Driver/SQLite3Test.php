<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's sqlite3 driver
 *
 * USAGE:  From the "Test" directory, execute the following command:
 * phpunit Driver_SQLite3Test
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_SQLite3Test extends SQLSolution_Test_Driver_Bundle {
	public static function suite() {
		if (!defined('SQLITE3_OPEN_READWRITE')) {
			die("Skip: This version of PHP does not support SQLite3.\n");
		}
		parent::checkSkipDbms(__CLASS__, 'SQLDbName');
		return parent::suite();
	}
}
