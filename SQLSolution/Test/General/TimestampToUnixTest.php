<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's TimestampToUnix method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_TimestampToUnixTest extends SQLSolution_Test_General {
	public function testTimestampToUnix() {
		ini_set('date.timezone', 'UTC');

		$return = $this->sql->TimestampToUnix(__FILE__, __LINE__, '19700101000000');
		$this->assertEquals(0, $return);

		$return = $this->sql->TimestampToUnix(__FILE__, __LINE__, '20371231235959');
		$this->assertEquals(2145916799, $return);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testTimestampToUnixUnder() {
		ini_set('date.timezone', 'UTC');
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->TimestampToUnix(__FILE__, __LINE__, '19691231235959');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testTimestampToUnixOver() {
		ini_set('date.timezone', 'UTC');
		$this->expectOutputString(SQLSOLUTION_TEST_ERROR_OUTPUT);
		$this->sql->TimestampToUnix(__FILE__, __LINE__, '20380101000000');
	}
}
