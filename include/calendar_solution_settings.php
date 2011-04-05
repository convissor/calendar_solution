<?php

/**
 * The DBMS and cache settings plus autoload function for the Calendar Solution
 *
 * NOTE: If your system already has an autoloader, feel free to integrate the
 * concepts in this autoload function into yours and then comment out the
 * spl_autoload_register() call.  If you do so and also use our PHPUnit tests,
 * don't forget to edit the bootstrap.ini file to include your autoloader.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/sql/sql.htm
 */

/**
 * The database extension to use: "mysql", "mysqli", "pgsql", "sqlite", "sqlite3"
 */
define('CALENDAR_SOLUTION_DBMS', '');


/**
 * The HTTP_HOST string used if $_SERVER['HTTP_HOST'] is empty
 * @see CalendarSolution::$http_host
 */
define('CALENDAR_SOLUTION_HTTP_HOST', 'localhost');


/**
 * Cache server connection information, if any
 *
 * Caching will only be utilized if this array is populated.
 *
 * The format is an array of arrays.  Each sub-array must contain the server
 * and port.  A server's weight can also be specified in the third element.
 *
 * <pre>
 * array(
 *     array('localhost', 11211, 8),
 *     array('cache.example.net', 11211, 4),
 *     array('198.7.9.45', 11211, 4),
 * )
 * </pre>
 *
 * @link http://php.net/memcached.addservers
 */
$GLOBALS['cache_servers'] = array(
);

/**
 * The Calendar Solution Cache class to use: "CalendarSolution_Cache_Memcache"
 *
 * NOTE: Leave this alone unless you are creating your own cache class.
 */
define('CALENDAR_SOLUTION_CACHE_CLASS', 'CalendarSolution_Cache_Memcache');


if (!defined('TAASC_DIR_INCLUDE')) {
	/**
	 * Set the include path to the current directory
	 *
	 * Using dirname(__FILE__) because __DIR__ introduced in PHP 5.3.
	 */
	define('TAASC_DIR_INCLUDE', dirname(__FILE__));
}

/**
 * An autoload function for software from The Analysis and Solutions Company
 *
 * Uses the PEAR naming convention of "_" in class names becoming "/".
 *
 * Checks the current directory and subdirectories thereof first,
 * then tries via the include_path.
 *
 * NOTE: If your system already has an autoloader, integrate the concepts in
 * our autoload function into yours and then remove this one.
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
