<?php

/**
 * Calendar Solution's means to edit a Frequent Event via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to edit a Frequent Event via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_FrequentEvent_Form extends CalendarSolution_FrequentEvent {
	/**
	 * Errors found by is_valid()
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The names of fields on the form
	 * @var array
	 */
	protected $fields = array(
		'frequent_event_id',
		'frequent_event',
		'frequent_event_uri',
	);

	/**
	 * The names of fields on the form that are bitwise in the database
	 * @var array
	 */
	protected $fields_bitwise = array();


	/**
	 * Deletes the record specified by $this->data['frequent_event_id']
	 * @return void
	 */
	public function delete() {
		$this->flush_cache();

		$this->sql->SQLQueryString = 'DELETE FROM cs_frequent_event
			WHERE frequent_event_id = '
			. $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_id']);

		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * Inserts the posted data into the database
	 * @return void
	 */
	public function insert() {
		$this->flush_cache();

		$this->sql->SQLQueryString = 'INSERT INTO cs_frequent_event (
				frequent_event,
				frequent_event_uri
			) VALUES ('
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_uri'])
			. ')';

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}
	}

	/**
	 * Ensures the validity of the information in $this->data
	 *
	 * @param bool $check_frequent_event_id  test the value of frequent_event_id?
	 *
	 * @return bool
	 *
	 * @throws CalendarSolution_Exception on fields containing predetermined
	 *         data being manipulated
	 */
	public function is_valid($check_frequent_event_id = true) {
		$this->errors = array();

		if ($check_frequent_event_id) {
			if (!preg_match('/^\d{1,10}$/', $this->data['frequent_event_id'])) {
				throw new CalendarSolution_Exception('frequent_event_id is invalid');
			}
		}

		if (strlen($this->data['frequent_event']) > 60) {
			$this->errors[] = 'Name is too long. We trimmed it';
			$this->data['frequent_event'] = trim(substr($this->data['frequent_event'], 0, 60));
		}
		$Temp = $this->data['frequent_event'];
		$this->data['frequent_event'] = preg_replace('/::\/?\w+::/', '', $this->data['frequent_event']);
		if ($this->data['frequent_event'] != $Temp) {
			$this->errors[] = 'Safe Markup is not allowed in the Name field';
		} elseif ($this->data['frequent_event'] == '') {
			$this->errors[] = 'Name is blank';
		}

		if ($this->data['frequent_event_uri']
			&& !preg_match('@(http://|https://|ftp://|gopher://|news:|mailto:)([\w/!#$%&\'()*+,.:;=?\@~-]+)([\w/!#$%&\'()*+:;=?\@~-])@i', $this->data['frequent_event_uri']))
		{
			$this->errors[] = 'Frequent Event URL is malformed';
		}
		if (strlen($this->data['frequent_event_uri']) > 250) {
			$this->errors[] = 'Frequent Event URL is too long. We trimmed it';
			$this->data['frequent_event_uri'] = trim(substr($this->data['frequent_event_uri'], 0, 250));
		}

		return empty($this->errors);
	}

	/**
	 * Produces the HTML form for editing an event
	 *
	 * @param int $frequent_event_id  the id number of the item to get
	 *                                (defaults to $_REQUEST['frequent_event_id'])
	 *
	 * @return string  the complete HTML of the desired event
	 *
	 * @throws CalendarSolution_Exception if $this->data is not populated
	 */
	public function get_rendering() {
		if (empty($this->data)) {
			throw new CalendarSolution_Exception('$data has not been populated');
		}

		if ($this->data['set_from'] == 'post') {
			$this->escape_data_for_html();
		}

		$out = '<form class="cs_form" method="post">' . "\n";
		$out .= ' <table summary="Frequent Event Entry Form. Left ';
		$out .= "column has field names. Right column has data entry fields.\">\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>T<u>i</u>tle</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* <em>Required.</em>\n";
		$out .= '    <br /><input accesskey="i" type="text" name="frequent_event" size="60" maxlength="60" value="' . $this->data['frequent_event'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Frequent Event URL\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Hyperlink for this one event.\n";
		$out .= "    <br />* 250 characters or less.\n";
		$out .= '    <br /><input type="text" name="frequent_event_uri" size="60" maxlength="250" value="' . $this->data['frequent_event_uri'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Submit\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if ($this->data['frequent_event_id']) {
			$out .= "    Update:\n";
			$out .= '    <input type="submit" name="submit" value="Update" />' . "\n";
		}

		if ($this->data['frequent_event_id']) {
			$out .= "    Copy this as new item:\n";
		} else {
			$out .= "    New:\n";
		}
		$out .= '    <input type="submit" name="submit" value="Add" />' . "\n";

		if ($this->data['frequent_event_id']) {
			$out .= "    Delete:\n";
			$out .= '    <input type="submit" name="submit" value="Delete" />' . "\n";
		}

		$out .= '    <input type="hidden" name="frequent_event_id" value="';
		$out .=      $this->data['frequent_event_id'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		$out .= ' </table>';
		$out .= "\n</form>\n\n";

		$out .= $this->get_credit();

		return $out;
	}

	/**
	 * Updates the record with the posted data
	 * @return void
	 */
	public function update() {
		$this->flush_cache();

		$this->sql->SQLQueryString = 'UPDATE cs_frequent_event SET
			frequent_event = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event']) . ',
			frequent_event_uri = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_uri']) . '
			WHERE frequent_event_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_id']);

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}
	}
}
