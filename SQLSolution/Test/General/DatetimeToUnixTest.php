<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's DatetimeToUnix method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_DatetimeToUnixTest extends SQLSolution_Test_General {
	public function testDatetimeToUnix() {
		ini_set('date.timezone', 'UTC');

		$return = $this->sql->DatetimeToUnix(__FILE__, __LINE__, '1970-01-01 00:00:00');
		$this->assertEquals(0, $return);

		$return = $this->sql->DatetimeToUnix(__FILE__, __LINE__, '2037-12-31 23:59:59');
		$this->assertEquals(2145916799, $return);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testDatetimeToUnixUnder() {
		ini_set('date.timezone', 'UTC');
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->DatetimeToUnix(__FILE__, __LINE__, '1969-12-31 23:59:59');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testDatetimeToUnixOver() {
		ini_set('date.timezone', 'UTC');
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->DatetimeToUnix(__FILE__, __LINE__, '2038-01-01 00:00:00');
	}
}
