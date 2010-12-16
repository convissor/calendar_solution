<?php

/**
 * Sets the include directory, desired database system and autoloader
 *
 * @package CalendarSolution_Test
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * Uses dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../../calendar_solution_setup.php';

/**
 * Load the Calendar Solution class file now because it includes our DateTime
 * class wich won't be found by autoloader
 */
require dirname(__FILE__) . '/../../CalendarSolution.php';
