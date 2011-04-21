<?php

/**
 * Calendar Solution's means to edit a specific event via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to edit a specific event via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Detail_Form extends CalendarSolution_Detail {
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
		'calendar_id',
		'calendar_uri',
		'category_id',
		'changed',
		'date_start',
		'detail',
		'feature_on_page_id',
		'frequency',
		'frequent_event_id',
		'frequent_event_uri',
		'is_own_event',
		'list_link_goes_to_id',
		'location_start',
		'note',
		'span',
		'status_id',
		'summary',
		'time_end',
		'time_start',
		'title',
		'week_of_month',
	);

	/**
	 * The names of fields on the form that are bitwise in the database
	 * @var array
	 */
	protected $fields_bitwise = array(
		'feature_on_page_id',
	);


	/**
	 * Sets the CSRF token name and calls the main constructor
	 *
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CALENDAR_SOLUTION_DBMS  to know which database extension to use
	 * @uses CalendarSolution::__construct()  for the main instantiation tasks
	 * @uses CalendarSolution::$csrf_token_name  to hold the token's name
	 */
	public function __construct($dbms = CALENDAR_SOLUTION_DBMS) {
		parent::__construct($dbms);
		$this->csrf_token_name = 'csrf_token_' . __CLASS__;
	}

	/**
	 * Deletes the record specified by $this->data['calendar_id']
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function delete() {
		$this->validate_csrf_token();
		$this->flush_cache();

		$this->sql->SQLQueryString = 'DELETE FROM cs_calendar
			WHERE calendar_id = '
			. $this->sql->Escape(__FILE__, __LINE__, $this->data['calendar_id']);

		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/Form.css';
	}

	/**
	 * Calculates the start dates for events
	 *
	 * Uses $this->data['date_start'], $this->data['frequency'],
	 * $this->data['week_of_month'] and $this->data['span']
	 *
	 * @return array  an array of dates in YYYY-MM-DD format
	 */
	protected function get_date_starts() {
		if (!$this->data['frequency']) {
			/*
			 * It's a one day event.  Dead simple.  Get it over with.
			 */
			return array($this->data['date_start']);
		}

		if ($this->data['frequency'] == 'Monthly'
			&& $this->data['week_of_month'] != 'day')
		{
			/*
			 * These monthly types are hairy.  Do this separately.
			 */

			$date = new DateTimeSolution($this->data['date_start']);
			$interval = new DateIntervalSolution('P1M');

			$date_starts = array($this->data['date_start']);

			if ($this->data['week_of_month'] == 'end') {
				// Last day of the month.
				$date->modify('first day of this month');
				for ($i = 1; $i < $this->data['span']; $i++) {
					$date->add($interval);
					$date_starts[] = $date->format('Y-m-t');
				}
			} else {
				$day = $date->format('l');
				$week = $this->data['week_of_month'];
				for ($i = 1; $i < $this->data['span']; $i++) {
					$date->modify('first day of this month');
					$date->add($interval);
					$date->modify("$week $day of this month");
					$date_starts[] = $date->format('Y-m-d');
				}
			}

			return $date_starts;
		}

		/*
		 * The rest of these follow basic addition.
		 */
		switch ($this->data['frequency']) {
			case 'Monthly':
				// If down here, doing same day each month.
				$interval_spec = 'P1M';
				break;
			case 'Bi-weekly':
				$interval_spec = 'P14D';
				break;
			case 'Weekly':
				$interval_spec = 'P7D';
				break;
			case 'Daily':
				$interval_spec = 'P1D';
		}

		$date_starts = array($this->data['date_start']);

		$date = new DateTimeSolution($this->data['date_start']);
		$interval = new DateIntervalSolution($interval_spec);

		for ($i = 1; $i < $this->data['span']; $i++) {
			$date->add($interval);
			$date_starts[] = $date->format('Y-m-d');
		}

		return $date_starts;
	}

	/**
	 * Inserts the posted data into the database
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function insert() {
		$this->validate_csrf_token();
		$this->flush_cache();

		$feature_bitwise = $this->get_bitwise_from_array($this->data['feature_on_page_id']);

		$sql_top = 'INSERT INTO cs_calendar (
				calendar_uri,
				category_id,
				changed,
				detail,
				feature_on_page_id,
				frequent_event_id,
				is_own_event,
				list_link_goes_to_id,
				location_start,
				note,
				status_id,
				summary,
				time_end,
				time_start,
				title,
				date_start
			) VALUES ('
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['calendar_uri']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['category_id']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['changed']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['detail']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $feature_bitwise) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_id']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['is_own_event']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['list_link_goes_to_id']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['location_start']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['note']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['status_id']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['summary']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['time_end']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['time_start']) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['title']) . ', ';

		$date_starts = $this->get_date_starts();
		foreach ($date_starts as $date_start) {
			$this->sql->SQLQueryString = $sql_top
				. $this->sql->Escape(__FILE__, __LINE__, $date_start) . ')';
			$this->sql->RunQuery(__FILE__, __LINE__);
		}
	}

	/**
	 * Ensures the validity of the information in $this->data
	 *
	 * @param bool  $check_calendar_id  test the value of calendar_id?
	 *
	 * @return bool
	 *
	 * @throws CalendarSolution_Exception on fields containing predetermined
	 *         data being manipulated
	 */
	public function is_valid($check_calendar_id = true) {
		$this->errors = array();

		if ($check_calendar_id) {
			if (!preg_match('/^\d{1,10}$/', $this->data['calendar_id'])) {
				throw new CalendarSolution_Exception('calendar_id is invalid');
			}
		}

		if (strlen($this->data['title']) > 40) {
			$this->errors[] = 'Title is too long. We trimmed it';
			$this->data['title'] = trim(substr($this->data['title'], 0, 40));
		}
		$Temp = $this->data['title'];
		$this->data['title'] = preg_replace('/::\/?\w+::/', '', $this->data['title']);
		if ($this->data['title'] != $Temp) {
			$this->errors[] = 'Safe Markup is not allowed in the Title field';
		} elseif ($this->data['title'] == '') {
			$this->errors[] = 'Title is blank';
		}

		if (strlen($this->data['summary']) > 250) {
			$this->errors[] = 'Summary is too long. We trimmed it';
			$this->data['summary'] = trim(substr($this->data['summary'], 0, 250));
		}

		if (strlen($this->data['location_start']) > 250) {
			$this->errors[] = 'Location is too long. We trimmed it';
			$this->data['location_start'] = trim(substr($this->data['location_start'], 0, 250));
		}

		if (strlen($this->data['note']) > 250) {
			$this->errors[] = 'Note is too long. We trimmed it';
			$this->data['note'] = trim(substr($this->data['note'], 0, 250));
		}

		if (strlen($this->data['detail']) > 5000) {
			$this->errors[] = 'Detail is too long. We trimmed it';
			$this->data['detail'] = trim(substr($this->data['detail'], 0, 5000));
		}

		if (!is_array($this->data['feature_on_page_id'])) {
			// Comes from list, so this is a real problem.
			throw new CalendarSolution_Exception('Featured Event is invalid');
		}

		if ($this->data['frequent_event_id']
			&& !preg_match('/^\d{1,5}$/', $this->data['frequent_event_id']))
		{
			// Comes from list, so this is a real problem.
			throw new CalendarSolution_Exception('Frequent Event is invalid');
		}

		if ($this->data['category_id']
			&& !preg_match('/^\d{1,10}$/', $this->data['category_id']))
		{
			// Comes from list, so this is a real problem.
			throw new CalendarSolution_Exception('Category is invalid');
		}

		if ($this->data['calendar_uri']
			&& !preg_match('@(http://|https://|ftp://|gopher://|news:|mailto:)([\w/!#$%&\'()*+,.:;=?\@~-]+)([\w/!#$%&\'()*+:;=?\@~-])@i', $this->data['calendar_uri']))
		{
			$this->errors[] = 'Specific URL is malformed';
		}
		if (strlen($this->data['calendar_uri']) > 250) {
			$this->errors[] = 'Specific URL is too long. We trimmed it';
			$this->data['calendar_uri'] = trim(substr($this->data['calendar_uri'], 0, 250));
		}

		if (!preg_match('/^\d{1,3}$/', $this->data['list_link_goes_to_id'])) {
			// Comes from list, so this is a real problem.
			throw new CalendarSolution_Exception('List Link Goes To is invalid');
		}

		if ($this->data['list_link_goes_to_id'] == self::LINK_TO_CALENDAR_URI) {
			if (!$this->data['calendar_uri']) {
				$this->errors[] = 'List Link Goes To is set to Specific URL but you did not provide a Specific URL';
			}
		} elseif ($this->data['list_link_goes_to_id'] == self::LINK_TO_FREQUENT_EVENT_URI) {
			if ($this->data['frequent_event_id']) {
				$this->sql->SQLQueryString = "SELECT frequent_event_uri
					FROM cs_frequent_event
					WHERE frequent_event_id = " .
						$this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_id']) . "
						AND frequent_event_uri IS NOT NULL
						AND frequent_event_uri <> ''";
				if (!$this->sql->RunQuery_RowsNeeded(__FILE__, __LINE__)) {
					$this->errors[] = 'List Link Goes To is set to Frequent Event URL but the selected Frequent Event does not have a URL';
				}
			}
		}

		if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $this->data['date_start'])) {
			$this->errors[] = 'Start Date is invalid';
		} else {
			try {
				$x = new DateTimeSolution($this->data['date_start']);
			} catch (Exception $e) {
				$this->errors[] = 'Start Date is invalid';
			}
		}

		if ($this->data['time_start'] == 'HH:MM') {
			$this->data['time_start'] = null;
		} elseif ($this->data['time_start']) {
			if (!preg_match('/^\d{1,2}:\d{2}$/', $this->data['time_start'])) {
				$this->errors[] = 'Start Time is invalid';
			} else {
				try {
					$x = new DateTimeSolution($this->data['time_start'] . ':00');
				} catch (Exception $e) {
					$this->errors[] = 'Start Time is invalid';
				}
			}
		}

		if ($this->data['time_end'] == 'HH:MM') {
			$this->data['time_end'] = null;
		} elseif ($this->data['time_end']) {
			if (!preg_match('/^\d{1,2}:\d{2}$/', $this->data['time_end'])) {
				$this->errors[] = 'End Time is invalid';
			} else {
				try {
					$x = new DateTimeSolution($this->data['time_end'] . ':00');
				} catch (Exception $e) {
					$this->errors[] = 'End Time is invalid';
				}
			}
		}

		if ($this->data['span']) {
			if (!preg_match('/^\d{1,2}$/', $this->data['span'])) {
				$this->errors[] = 'How Many Times has to be one or two digits long';
			}

			switch ($this->data['frequency']) {
				case 'Monthly':
					if (!preg_match('/^first|second|third|fourth|last|end|day$/', $this->data['week_of_month'])) {
						$this->errors[] = 'Week/Day of Month is invalid';
					}
					if ($this->data['week_of_month'] == 'day') {
						if (substr($this->data['date_start'], -2) > 28) {
							$this->errors[] = 'Start Date must be earlier than the 29th if Week/Day of Month is Same Day';
						}
					}
					break;
				case 'Bi-weekly':
				case 'Weekly':
				case 'Daily':
					// Nothing needs to be checked.
					break;
				default:
					// Comes from list, so this is a real problem.
					throw new CalendarSolution_Exception('Frequency is invalid');
			}
		}

		if (!preg_match('/^[YN]$/', $this->data['changed'])) {
			// Comes from radio button, so this is a real problem.
			throw new CalendarSolution_Exception('Changed or Added Recently is invalid');
		}

		if (!preg_match('/^[YN]$/', $this->data['is_own_event'])) {
			// Comes from radio button, so this is a real problem.
			throw new CalendarSolution_Exception('Is This Your Own Event is invalid');
		}

		if (!preg_match('/^\d{1,3}$/', $this->data['status_id'])) {
			// Comes from list, so this is a real problem.
			throw new CalendarSolution_Exception('Status is invalid');
		}

		return empty($this->errors);
	}

	/**
	 * Produces the HTML form for editing an event
	 *
	 * @param int $calendar_id  the id number of the item to get (defaults to
	 *                          $_REQUEST['calendar_id'])
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

		$out = '<p>Spacing inserted via the ENTER key are ignored when displaying web pages.</p>' . "\n";

		$out .= "<p>&quot;Safe Markup&quot; is allowed in several fields.";
		$out .= " Here is how it works:\n";
		$out .= "<br />* Open paragraphs with <kbd>::p::</kbd> and ";
		$out .= "create line breaks via <kbd>::br::</kbd>\n";
		$out .= "<br />* URL's can be entered two ways:";
		$out .= "<br />&nbsp;+ Anything starting with <kbd>http://</kbd>";
		$out .= " becomes a hyperlink.";
		$out .= "<br />&nbsp;+ To turn text into a hyperlink, put the URL and link";
		$out .= " text between <kbd>::a::</kbd>'s like this: ";
		$out .= "<kbd>::a::http://example.com/::a::Some Great Example::/a::</kbd>\n";
		$out .= "</p>\n";

		$out .= '<form class="cs_detail_form" method="post">' . "\n";
		$out .= ' <table summary="Event Entry Form. Left ';
		$out .= "column has field names. Right column has data entry fields.\">\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= '    <label for="frequent_event_id" accesskey="r">' . "\n";
		$out .= "     F<u>r</u>equent Event\n";
		$out .= "    </label>\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if (empty($this->data['frequent_event_id'])) {
			$this->data['frequent_event_id'] = '';
		}
		$Opt = array(
			'id'           => 'frequent_event_id',
			'table'        => 'cs_frequent_event',
			'visiblefield' => 'frequent_event',
			'keyfield'     => 'frequent_event_id',
			'name'         => 'frequent_event_id',
			'orderby'      => 'frequent_event',
			'where'        => '1 = 1',
			'multiple'     => 'N',
			'size'         => '0',
			'default'      => $this->data['frequent_event_id'],
			'add'          => array('' => 'NONE'),
		);
		$out .= $this->sql->GetOptionListGenerator(__FILE__, __LINE__, $Opt);
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>T<u>i</u>tle</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* <em>Required.</em>\n";
		$out .= "    <br />* Title of the event.\n";
		$out .= "    <br />* Can describe the whole event if you want.</small>\n";
		$out .= '    <br /><input accesskey="i" type="text" name="title" size="40" maxlength="40" value="' . $this->data['title'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		if (!$this->data['date_start']) {
			$this->data['date_start'] = 'YYYY-MM-DD';
		}
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>Start Date</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* <em>Required.</em>\n";
		$out .= "    <br />* Use <kbd>YYYY-MM-DD</kbd> format.\n";
		$out .= "    <br />* For recurring events, enter the date of the first event here, then fill in the &quot;Recurring&quot; info, below.</small>\n";
		$out .= '    <br /><input type="text" name="date_start" size="10" maxlength="10" value="' . $this->data['date_start'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>Start Time</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* Use 24 hour, <kbd>HH:MM</kbd> format.\n";
		$out .= "    <br />* Leave blank for none.</small>\n";
		$out .= '    <br /><input type="text" name="time_start" size="5" maxlength="5" value="';
		if ($this->data['time_start']) {
			$out .= $this->format_date($this->data['time_start'], self::DATE_FORMAT_TIME_24);
		} else {
			$out .= 'HH:MM';
		}
		$out .= "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>End Time</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* Use 24 hour, <kbd>HH:MM</kbd> format.\n";
		$out .= "    <br />* Leave blank for none.</small>\n";
		$out .= '    <br /><input type="text" name="time_end" size="5" maxlength="5" value="';
		if ($this->data['time_end']) {
			$out .= $this->format_date($this->data['time_end'], self::DATE_FORMAT_TIME_24);
		} else {
			$out .= 'HH:MM';
		}
		$out .= "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Recurring\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if ($this->data['calendar_id']) {
			$out .= '       Recurring event data can only be entered for new items.';
		} else {
			$freqs = array(
				'',
				'Monthly',
				'Bi-weekly',
				'Weekly',
				'Daily',
			);
			$out .= 'Frequency: <select name="frequency">' . "\n";
			foreach ($freqs as $value) {
				$out .= ' <option value="' . $value;
				if ($this->data['frequency'] == $value) {
					$out .= '" selected="selected';
				}
				$out .= '">' . $value . "</option>\n";
			}
			$out .= "</select>\n";

			$wks = array(
				'' => '',
				'first' => 'First',
				'second' => 'Second',
				'third' => 'Third',
				'fourth' => 'Fourth',
				'last' => 'Last',
				'end' => 'Last Day',
				'day' => 'Same Day Number',
			);
			$out .= '<br />Week/Day of Month (if monthly): <select name="week_of_month">' . "\n";
			foreach ($wks as $key => $value) {
				$out .= ' <option value="' . $key;
				if ($this->data['week_of_month'] == $key) {
					$out .= '" selected="selected';
				}
				$out .= '">' . $value . "</option>\n";
			}
			$out .= "</select>\n";

			$out .= '    <br />How Many Times: <input type="text" name="span"'
				 . '  size="2" maxlength="2" value="' . $this->data['span'] . '" />' . "\n";
			$out .= "       <br /><small>NOTE: Once submitted, each date becomes a separate event.</small>\n";
		}

		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Summary\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Brief explanation of what's happening.\n";
		$out .= "    <br />* 250 characters or less.\n";
		$out .= "    <br />* Safe markup is allowed.</small>\n";
		$out .= '    <br /><textarea name="summary" cols="50" rows="3">';
		$out .=      $this->data['summary'] . "</textarea>\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Detail\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Use for events that need to convey a lot of info.\n";
		$out .= "    <br />* 5,000 characters or less.\n";
		$out .= "    <br />* Please don't duplicate info from the summary.\n";
		$out .= "    <br />* Safe markup is allowed.</small>\n";
		$out .= '    <br /><textarea name="detail" cols="50" rows="10">';
		$out .=      $this->data['detail'] . "</textarea>\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= '    <label for="location_start" accesskey="l">' . "\n";
		$out .= "     <u>L</u>ocation\n";
		$out .= "    </label>\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= '    * Where the event is happening. Include both starting and ';
		$out .=      "ending points if appropriate.\n";
		$out .= "    <br />* 250 characters or less.\n";
		$out .= '    <br />* If your info is lengthy, put a summary here and the ';
		$out .=      "full data in the details field, above.\n";
		$out .= "    <br />* Safe markup is allowed.</small>\n";
		$out .= '    <br /><textarea name="location_start" id="location_start" cols="50" rows="3">';
		$out .=      $this->data['location_start'] . "</textarea>\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Note\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Brief comments, if any, that don't fit elsewhere.\n";
		$out .= "    <br />* 250 characters or less.\n";
		$out .= "    <br />* Safe markup is allowed.</small>\n";
		$out .= '    <br /><textarea name="note" cols="50" rows="3">';
		$out .=      $this->data['note'] . "</textarea>\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td>\n";
		$out .= "    Specific URL\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Hyperlink for this one event.\n";
		$out .= "    <br />* If the Frequent Event already has a URL, you don't need to put something here.\n";
		$out .= "    <br />* 250 characters or less.\n";
		$out .= '    <br /><textarea name="calendar_uri" cols="50" rows="3">';
		$out .=      $this->data['calendar_uri'] . "</textarea>\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    List Link Goes To\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * When viewing a list of multiple events (in list or calendar";
		$out .= "    format), where should clicking on the event go to?</small>\n";

		if (empty($this->data['list_link_goes_to_id'])) {
			$this->data['list_link_goes_to_id'] = 2;
		}
		$Opt = array(
			'type'         => 'radio',
			'table'        => 'cs_list_link_goes_to',
			'visiblefield' => 'list_link_goes_to',
			'keyfield'     => 'list_link_goes_to_id',
			'name'         => 'list_link_goes_to_id',
			'orderby'      => 'list_link_goes_to_id',
			'where'        => '1 = 1',
			'default'      => $this->data['list_link_goes_to_id'],
			'border'       => 0,
			'columns'      => 1,
		);
		ob_start();
		$this->sql->InputListGenerator(__FILE__, __LINE__, $Opt);
		$out .= ob_get_contents();
		ob_end_clean();

		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= '    <label for="category_id">' . "\n";
		$out .= "     Category\n";
		$out .= "    </label>\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if (empty($this->data['category_id'])) {
			$this->data['category_id'] = '';
		}
		$Opt = array(
			'id'           => 'category_id',
			'table'        => 'cs_category',
			'visiblefield' => 'category',
			'keyfield'     => 'category_id',
			'name'         => 'category_id',
			'orderby'      => 'category',
			'where'        => '1 = 1',
			'multiple'     => 'N',
			'size'         => '0',
			'default'      => $this->data['category_id'],
			'add'          => array('' => 'NONE'),
		);
		$out .= $this->sql->GetOptionListGenerator(__FILE__, __LINE__, $Opt);
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Featured Event\n";
		$out .= "   </td>\n";
		$out .= "   <td><small>\n";
		$out .= "    * Pages this item should be featured on, if any.</small>\n";

		$page_ids = $this->get_array_from_bitwise($this->data['feature_on_page_id']);
		$Opt = array(
			'type'         => 'checkbox',
			'table'        => 'cs_feature_on_page',
			'visiblefield' => 'feature_on_page',
			'keyfield'     => 'feature_on_page_id',
			'name'         => 'feature_on_page_id',
			'orderby'      => 'feature_on_page_id',
			'where'        => '1 = 1',
			'default'      => $page_ids,
			'border'       => 0,
			'columns'      => 1,
		);
		ob_start();
		$this->sql->InputListGenerator(__FILE__, __LINE__, $Opt);
		$out .= ob_get_contents();
		ob_end_clean();

		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		if ($this->data['changed'] == 'Y') {
			$yes = ' checked="checked"';
			$no = '';
		} else {
			$yes = '';
			$no = ' checked="checked"';
		}

		$out .= '  <tr>' . "\n";
		$out .= "   <td>Changed or Added Recently?</td>\n";
		$out .= "   <td>\n";
		$out .= '    <input type="radio" name="changed" value="Y"' . $yes . ' />Changed' . "\n";
		$out .= '    <input type="radio" name="changed" value="N"' . $no . ' />Not Changed' . "\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Status\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if (empty($this->data['status_id'])) {
			$this->data['status_id'] = 1;
		}
		$Opt = array(
			'type'         => 'radio',
			'table'        => 'cs_status',
			'visiblefield' => 'status',
			'keyfield'     => 'status_id',
			'name'         => 'status_id',
			'orderby'      => 'status_id',
			'where'        => '1 = 1',
			'default'      => $this->data['status_id'],
			'border'       => 0,
			'columns'      => 1,
		);
		ob_start();
		$this->sql->InputListGenerator(__FILE__, __LINE__, $Opt);
		$out .= ob_get_contents();
		ob_end_clean();

		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		if ($this->data['is_own_event'] == 'N') {
			$yes = '';
			$no = ' checked="checked"';
		} else {
			$yes = ' checked="checked"';
			$no = '';
		}

		$out .= '  <tr>' . "\n";
		$out .= "   <td>Is This Your Own Event?</td>\n";
		$out .= "   <td>\n";
		$out .= '    <input type="radio" name="is_own_event" value="Y"' . $yes . ' />Yes' . "\n";
		$out .= '    <input type="radio" name="is_own_event" value="N"' . $no . ' />No, this is produced by a different organization' . "\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Submit\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if ($this->data['calendar_id']) {
			$out .= "    Update:\n";
			$out .= '    <input type="submit" name="submit" value="Update" />' . "\n";
		}

		if ($this->data['calendar_id']) {
			$out .= "    Copy this as new item:\n";
		} else {
			$out .= "    New:\n";
		}
		$out .= '    <input type="submit" name="submit" value="Add" />' . "\n";

		if ($this->data['calendar_id']) {
			$out .= "    Delete:\n";
			$out .= '    <input type="submit" name="submit" value="Delete" />' . "\n";
		}

		$token_value = uniqid(rand(), true);
		$_SESSION[$this->csrf_token_name] = $token_value;
		$out .= '    <input type="hidden" name="' . $this->csrf_token_name
			. '" value="' . $token_value . "\" />\n";

		$out .= '    <input type="hidden" name="calendar_id" value="';
		$out .=      $this->data['calendar_id'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		$out .= ' </table>';
		$out .= "\n</form>\n\n";

		$out .= $this->get_credit();

		return $out;
	}

	/**
	 * Updates the record with the posted data
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function update() {
		$this->validate_csrf_token();
		$this->flush_cache();

		$feature_bitwise = $this->get_bitwise_from_array($this->data['feature_on_page_id']);

		$this->sql->SQLQueryString = 'UPDATE cs_calendar SET
			calendar_uri = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['calendar_uri']) . ',
			category_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['category_id']) . ',
			changed = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['changed']) . ',
			date_start = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['date_start']) . ',
			detail = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['detail']) . ',
			feature_on_page_id = ' . $this->sql->Escape(__FILE__, __LINE__, $feature_bitwise) . ',
			frequent_event_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['frequent_event_id']) . ',
			is_own_event = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['is_own_event']) . ',
			list_link_goes_to_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['list_link_goes_to_id']) . ',
			location_start = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['location_start']) . ',
			note = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['note']) . ',
			status_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['status_id']) . ',
			summary = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['summary']) . ',
			time_end = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['time_end']) . ',
			time_start = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['time_start']) . ',
			title = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['title']) . '
			WHERE calendar_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['calendar_id']);

		$this->sql->RunQuery(__FILE__, __LINE__);
	}
}
