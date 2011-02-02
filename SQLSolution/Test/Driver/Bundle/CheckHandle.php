<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's Check Handle methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_CheckHandle extends PHPUnit_Framework_TestCase {
	/**
	 * The SQL Solution class being tested
	 * @var object
	 */
	protected $sql;

	/**
	 * PHPUnit's method for setting needed properties, etc, before each test
	 */
	protected function setUp() {
		$this->sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'];
	}

	/**
	 * PHPUnit's method for unsetting needed properties, etc, after each test
	 */
	protected function tearDown() {
		$this->sql->Disconnect(__FILE__, __LINE__);
		$this->sql = null;
	}


	public function testCheckHandle() {
		if (!property_exists($this->sql, 'SQLDbHandle')) {
			$this->markTestSkipped('This driver does not have handles.');
		}
		$this->assertFalse($this->sql->CheckHandle(), 'Handle found but should not exist.');
		$this->sql->ObtainHandle(__FILE__, __LINE__);
		$this->assertTrue($this->sql->CheckHandle(), 'Handle missing but should exist.');
	}
}
