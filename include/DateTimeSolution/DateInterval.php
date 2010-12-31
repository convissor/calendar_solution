<?php

/**
 * DateTime Solution's DateInterval class for use if PHP < 5.3
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides DateInterval functionality for versions of PHP before 5.3
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateTimeSolution_DateInterval {
	/**
	 * Indicates which level of support the DateTime Solution is providing
	 * @var string
	 */
	public $datetime_solution_level = '52';

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
		if (!preg_match('/^P((\d+)Y)?((\d+)M)?((\d+)D)?(T((\d+)H)?((\d+)M)?((\d+)S)?)?$/', $interval_spec, $match)) {
			throw new Exception("Invalid interval_spec $interval_spec");
		}
		$this->y = empty($match[2]) ? 0 : (int) $match[2];
		$this->m = empty($match[4]) ? 0 : (int) $match[4];
		$this->d = empty($match[6]) ? 0 : (int) $match[6];
		$this->h = empty($match[9]) ? 0 : (int) $match[9];
		$this->i = empty($match[11]) ? 0 : (int) $match[11];
		$this->s = empty($match[13]) ? 0 : (int) $match[13];
	}

	/**
	 * Formats the interval
	 *
	 * @param string $format  supports any combination of
	 *                        "%y", "%m", "%d", "%a", "%h", "%i", "%s", "%R", "%r"
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

		$search = array('%y', '%m', '%d', '%a', '%h', '%i', '%s', '%R', '%r');
		$replace = array($this->y, $this->m, $this->d, $this->days, $this->h, $this->i, $this->s, $this->R, $this->r);
		return str_replace($search, $replace, $format);
	}
}
