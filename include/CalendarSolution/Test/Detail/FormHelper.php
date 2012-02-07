<?php

/**
 * A helper class
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Extend the class to be tested, providing access to protected elements
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_Detail_FormHelper extends CalendarSolution_Detail_Form {
	public function __call($method, $args) {
		return call_user_func_array(array($this, $method), $args);
	}
	public function __get($property) {
		return $this->$property;
	}
	public function get_data_element($key) {
		return $this->data[$key];
	}
}
