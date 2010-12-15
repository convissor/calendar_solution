<?php

/**
 * Sets the include directory, desired database system and autoloader
 *
 * @package CalendarSolution
 */

/**
 * Indicates location of the include files
 *
 * Uses dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 *
 * @ignore
 */
define('TAASC_DIR_INCLUDE', dirname(__FILE__) . '/../../include');

/**
 * Obtain the Calendar Solution's autoload function
 *
 * If you already have an autoloader, you can integrate the CalendarSolution*
 * and SQLSolution* classes into it and then get rid of this require statement.
 * See taasc_autoload.php and INSTALL.TXT for more information.
 */
require TAASC_DIR_INCLUDE . '/taasc_autoload.php';

/**
 * @global string $GLOBALS['dbms']  the database engine to use: "mysql",
 *                                  "mysqli", "pgsql", "sqlite", "sqlite3"
 */
$GLOBALS['dbms'] = '';
