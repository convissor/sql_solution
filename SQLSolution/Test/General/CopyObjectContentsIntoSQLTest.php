<?php /** @package SQLSolution_Test */

/**
 * Tests the SQL Solution's CopyObjectContentsIntoSQL method
 *
 * @package SQLSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_Test_General_CopyObjectContentsIntoSQLTest extends SQLSolution_Test_General {
	public function testCopyObjectContentsIntoSQL() {
		global $obj;
		$obj = new foo;
		$this->sql->CopyObjectContentsIntoSQL(__FILE__, __LINE__, 'obj');
		$this->assertEquals('wiki', $this->sql->bar);
	}
}

/**
 * A temporary class that the test will copy from
 *
 * @package SQLSolution_Test
 */
class foo {
	/**
	 * @var string
	 */
	public $bar = 'wiki';
}
