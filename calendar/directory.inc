<?php

/**
 * Contains the $IncludeDir and $dbms settings for scripts in this directory
 *
 * @package CalendarSolution
 */

/**
 * Use dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 *
 * @global string $GLOBALS['IncludeDir']  indicates where to find include files
 */
$GLOBALS['IncludeDir'] = dirname(__FILE__) . '/../include';

/**
 * @global string $GLOBALS['dbms']  the database engine to use: "mysql",
 *                                  "mysqli", "pgsql", "sqlite", "sqlite3"
 */
$GLOBALS['dbms'] = '';
