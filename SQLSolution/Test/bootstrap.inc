<?php

/**
 * Settings for all SQL Solution PHPUnit tests
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */

/**
 * Require the auto loader
 *
 * Use dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require_once dirname(__FILE__) . '/../../autoload.php';

/**
 * The generic output SQL Solution produces when errors arise
 */
define('SQLSOLUTION_TEST_ERROR_OUTPUT', '
<h3>A Database Problem Occurred.
<br />Please make a note of the present time and what you were doing.
<br />Then contact the System Administrator.</h3>
</body></html>

');

/**
 * Removes SQLite files
 *
 * @param object $sql  the SQL Solution object being tested
 * @return void
 */
function sqlsolution_unlink_sqlite($sql) {
	if (preg_match('/SQLite/', get_class($sql))) {
		if (file_exists($sql->SQLDbName)) {
			@unlink($sql->SQLDbName);
		}
	}
}
