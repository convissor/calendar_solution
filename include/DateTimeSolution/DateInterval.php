<?php

/**
 * DateTime Solution's DateInterval class for use if PHP < 5.4
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides DateInterval functionality for versions of PHP before 5.4
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateTimeSolution_DateInterval {
	/**
	 * The total number of days
	 *
	 * This is false if the value is not known
	 * @var int|bool
	 */
	public $days = false;

	/**
	 * The interval specification provided in the constructor
	 * @var string
	 */
	protected $interval_spec;

	/**#@+
	 * Date components
	 * @var int
	 */
	public $y;
	public $m;
	public $d;
	public $h;
	public $i;
	public $s;
	/**#@-*/

	/**
	 * Boolean representation of whether end came before start
	 * @var int
	 */
	public $invert = 0;

	/**#@+
	 * String representations of $invert
	 * @var string
	 */
	private $R = '+';
	private $r = '';
	/**#@-*/


	/**
	 * Creates a DateIntervalSolution object
	 *
	 * @param string $interval_spec  example: "P0Y2M1DT10H5M20S"
	 *
	 * @throws Exception  on invalid interval_spec
	 * @link http://php.net/dateinterval.construct
	 */
	public function __construct($interval_spec) {
		$this->interval_spec = $interval_spec;
		if (!preg_match('/^P((\d+)Y)?((\d+)M)?((\d+)(D|W))?(T((\d+)H)?((\d+)M)?((\d+)S)?)?$/', $interval_spec, $match)) {
			throw new Exception("Invalid interval_spec $interval_spec");
		}
		$this->y = empty($match[2]) ? 0 : (int) $match[2];
		$this->m = empty($match[4]) ? 0 : (int) $match[4];
		if (empty($match[6])) {
			$this->d = 0;
		} else {
			if ($match[7] == 'W') {
				$this->d = $match[6] * 7;
			} else {
				$this->d = (int) $match[6];
			}
		}
		$this->h = empty($match[9]) ? 0 : (int) $match[9];
		$this->i = empty($match[11]) ? 0 : (int) $match[11];
		$this->s = empty($match[13]) ? 0 : (int) $match[13];
	}

	/**
	 * Formats the interval
	 *
	 * @param string $format  supports any combination of "%y", "%Y", "%m",
	 *                        "%M", "%d", "%D", "%a", "%h", "%H", "%i", "%I",
	 *                        "%s", "%S", "%r", "%R"
	 * @return string
	 */
	public function format($format) {
		if ($this->invert) {
			$this->R = '-';
			$this->r = '-';
		} else {
			$this->R = '+';
			$this->r = '';
		}

		$days = $this->days === false ? '(unknown)' : $this->days;

		$search = array('%y', '%m', '%d', '%a', '%h', '%i', '%s', '%R', '%r');
		$replace = array($this->y, $this->m, $this->d, $days, $this->h, $this->i, $this->s, $this->R, $this->r);
		$format = str_replace($search, $replace, $format);

		return preg_replace_callback('/%[YMDHIS]/',
				array($this, 'replace_upper_case_formats'), $format);
	}

	/**
	 * Indicates which level of support the DateTime Solution is providing
	 *
	 * Can't use a property because of PHP bug 52738
	 *
	 * @return string
	 */
	public function get_datetime_solution_level() {
		return 'userland';
	}

	/**
	 * Handles upper case formats for format()
	 * @param array $matches  the data from preg_replace_callback()
	 * @return string  the formatted number
	 */
	private function replace_upper_case_formats($matches) {
		$property = substr(strtolower($matches[0]), -1);
		return sprintf('%02d', $this->{$property});
	}
}
