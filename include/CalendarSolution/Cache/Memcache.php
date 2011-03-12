<?php

/**
 * Calendar Solution's memcache code
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Calendar Solution's memcache methods
 *
 * NOTE: the cache is flushed when administrators edit events.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Cache_Memcache implements CalendarSolution_Cache {
	/**
	 * The cache connection
	 * @var Memcache
	 */
	protected $cache;

	/**
	 * The Unix timestamp of when the cache should expire.
	 *
	 * Should be set to <kbd>00:00:01</kbd> tomorrow.  This is because many
	 * views use the current date as the starting point, so when tomorrow
	 * rolls around, today's data is no longer needed.
	 *
	 * NOTE: the cache is also flushed when administrators edit events.
	 *
	 * @var int
	 */
	protected $expiration_time;

	/**
	 * Calls memcache's addServer() for each server listed in
	 * $GLOBALS['cache_servers']
	 *
	 * @throws CalendarSolution_Exception  if the memcache extension is not
	 *         installed or if adding one of the cache servers fails
	 *
	 * @uses $GLOBALS['cache_servers']  to know where the cache servers are
	 */
	public function __construct() {
		if (!class_exists('Memcache')) {
			throw new CalendarSolution_Exception('memcache extension is not installed');
		}

 		$this->cache = new Memcache;
		foreach ($GLOBALS['cache_servers'] as $i => $s) {
			if (!is_array($s) || count($s) < 2) {
				throw new CalendarSolution_Exception('Improper cache server array');
			}
			$s = array_values($s);
			$weight = empty($s[2]) ? 1 : $s[2];
			if (!@$this->cache->addServer($s[0], $s[1], false, $weight)) {
				throw new CalendarSolution_Exception("Adding cache server $i failed");
			}
		}
	}

	/**
	 * Flush all data from the cache
	 * @return bool
	 */
	public function flush() {
		return @$this->cache->flush();
	}

	/**
	 * Obtains data from the cache
	 *
	 * @param string $key  the data element's name
	 *
	 * @return mixed  the data on success, false on failure
	 */
	public function get($key) {
		return @$this->cache->get($key);
	}

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
	 * @uses CalendarSolution_Cache_Memcache::$expiration_time  to know
	 *       when the data should exipre
	 */
	public function set($key, $value) {
		if (!$this->expiration_time) {
			$date = new DateTime;
			// Using modify() because add() introduced in PHP 5.3.
			$date->modify('+1 day');
			$date->setTime(0, 0, 1);
			$this->expiration_time = $date->format('U');
		}

		return @$this->cache->set($key, $value, null, $this->expiration_time);
	}
}
