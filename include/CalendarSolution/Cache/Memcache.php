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

	public function flush() {
		return @$this->cache->flush();
	}

	public function get($key) {
		return @$this->cache->get($key);
	}

	public function set($key, $value) {
		return @$this->cache->set($key, $value, null, CALENDAR_SOLUTION_CACHE_EXPIRE);
	}
}
