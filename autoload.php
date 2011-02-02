<?php

/**
 * An example of how to set up an autoloader
 *
 * NOTE: Use your own function that's set up for your environment instead.
 *
 * @package SQLSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2001-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */

if (!defined('TAASC_DIR_INCLUDE')) {
	/**
	 * Set the include path to the current directory
	 *
	 * Using dirname(__FILE__) because __DIR__ introduced in PHP 5.3.
	 */
	define('TAASC_DIR_INCLUDE', dirname(__FILE__));
}

/**
 * A sample autoload function
 *
 * Uses the PEAR naming convention of "_" in class names becoming "/".
 *
 * Checks the current directory and subdirectories thereof first,
 * then tries via the include_path.
 *
 * @return void
 */
function taasc_autoload_example($class) {
	$class = str_replace('_', '/', $class);

	if (file_exists(TAASC_DIR_INCLUDE . '/' . $class . '.php')) {
		// Local file, get it.
		require TAASC_DIR_INCLUDE . '/' . $class . '.php';
	} else {
		// File doesn't exist locally.  Use include path.
		require $class . '.php';
	}
}

spl_autoload_register('taasc_autoload_example');
