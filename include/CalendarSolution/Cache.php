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
	 * Cache values expire at 00:00:01 tomorrow.
	 *
	 * Note: the cache is also flushed when administrators edit events.
	 *
	 * @param string $key  the data element's name
	 * @param mixed $value  the data to be stored
	 *
	 * @return bool
	 *
	 * @uses CalendarSolution_Cache::$expiration_time  to know when the data
	 *       should exipre
	 */
	public function set($key, $value);
}
