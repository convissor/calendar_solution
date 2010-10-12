<?php

/**
 * Calendar Solution's DateInterval class, used only if the server's PHP
 * version is earlier than 5.3
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides DateInterval functionality for versions of PHP before 5.3
 *
 * Only support years, months and days
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateInterval {
    /**
     * The interval specification provided in the constructor
     * @var string
     */
    protected $interval_spec;

    /**#@+
     * Date components
     *
     * Make public to improve efficiency by reducing calls to format().
     * @var int
     */
    public $y = 0;
    public $m = 0;
    public $d = 0;
    public $h = 0;
    public $i = 0;
    public $s = 0;
    /**#@-*/

    /**
     * Boolean representation of whether end came before start
     *
     * Make public to improve efficiency by reducing calls to format().
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
     * Creates a DateInterval object
     *
     * @param string $interval_spec  example: "P0Y2M1DT10H5M20S"
     *
     * @link http://php.net/dateinterval.construct
     */
    public function __construct($interval_spec) {
        $this->interval_spec = $interval_spec;
        if (!preg_match('/^P(\d+Y)?(\d+M)?(\d+D)?T?(\d+H)?(\d+M)?(\d+S)?$/', $interval_spec, $parts)) {
            throw new Exception('invalid interval_spec');
        }
        $this->y = empty($parts[1]) ? 0 : (int) $parts[1];
        $this->m = empty($parts[2]) ? 0 : (int) $parts[2];
        $this->d = empty($parts[3]) ? 0 : (int) $parts[3];
        $this->h = empty($parts[4]) ? 0 : (int) $parts[4];
        $this->i = empty($parts[5]) ? 0 : (int) $parts[5];
        $this->s = empty($parts[6]) ? 0 : (int) $parts[6];
    }

    /**
     * Formats the interval
     *
     * @param string $format  supports any combination of
     *                        "%y", "%m", "%d", "%h", "%i", "%s", "%R", "%r"
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

        $search = array('%y', '%m', '%d', '%h', '%i', '%s', '%R', '%r');
        $replace = array($this->y, $this->m, $this->d, $this->h, $this->i, $this->s, $this->R, $this->r);
        return str_replace($search, $replace, $format);
    }
}
