<?php

/**
 * Calendar Solution's cache interface
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Methods that each cache class needs to define
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
interface CalendarSolution_Cache {
	/**
	 * Flush all data from the cache
	 * @return bool
	 */
	public function flush();

	/**
	 * Obtains data from the cache
	 *
	 * @param string $key  the data element's name
	 *
	 * @return mixed  the data on success, false on failure
	 */
	public function get($key);

	/**
	 * Stores data in the cache
	 *
	 * @param string $key  the data element's name
	 * @param mixed $value  the data to be stored
	 *
	 * @return bool
	 *
	 * @uses CALENDAR_SOLUTION_CACHE_EXPIRE  to know how long to store data
	 */
	public function set($key, $value);
}
