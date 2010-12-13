<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's constructor methods
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_Driver_Bundle_Constructor extends PHPUnit_Framework_TestCase {
	public function testConstructorDefault() {
		$sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS'];
		$this->assertEquals('Y', $sql->SQLEscapeHTML);
		$this->assertEquals('N', $sql->SQLSafeMarkup);
	}

	public function testConstructorYesYes() {
		$sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS']('Y', 'Y');
		$this->assertEquals('Y', $sql->SQLEscapeHTML);
		$this->assertEquals('Y', $sql->SQLSafeMarkup);
	}

	public function testConstructorYesNo() {
		$sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS']('Y', 'N');
		$this->assertEquals('Y', $sql->SQLEscapeHTML);
		$this->assertEquals('N', $sql->SQLSafeMarkup);
	}

	public function testConstructorNoYes() {
		$sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS']('N', 'Y');
		$this->assertEquals('N', $sql->SQLEscapeHTML);
		$this->assertEquals('Y', $sql->SQLSafeMarkup);
	}

	public function testConstructorNoNo() {
		$sql = new $GLOBALS['SQLSOLUTION_TEST_USER_CLASS']('N', 'N');
		$this->assertEquals('N', $sql->SQLEscapeHTML);
		$this->assertEquals('N', $sql->SQLSafeMarkup);
	}
}
