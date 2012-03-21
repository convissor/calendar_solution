<?php

/**
 * Calendar Solution's cache interface
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Methods that each cache class needs to define
 *
 * Users planning to create their own cache classes should follow the
 * procedures implemented in CalendarSolution_Cache_Memcache.
 *
 * @see CalendarSolution_Cache_Memcache
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
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
	 * If $expiration_time has not been set, this method sets it to
	 * <kbd>00:00:01</kbd> tomorrow.
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
