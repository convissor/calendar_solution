<?php

/**
 * A set of PHP classes to simplify integrating databases with web pages.
 *
 * <p>Handles MySQL and and ODBC database management systems.</p>
 *
 * <p>Requires PHP 4 or later.</p>
 *
 * <p>Please make a donation to support our open source development.
 * Update notifications are sent to people who make donations that exceed
 * the small registration threshold.  See the link below.</p>
 *
 * <p>For more information on using this program, read the manual on line
 * via the link below.</p>
 *
 * <p>SQL Solution is a trademark of The Analysis and Solutions Company.</p>
 *
 * <pre>
 * ======================================================================
 * SIMPLE PUBLIC LICENSE                        VERSION 1.1   2003-01-21
 *
 * Copyright (c) The Analysis and Solutions Company
 * http://www.analysisandsolutions.com/
 *
 * 1.  Permission to use, copy, modify, and distribute this software and
 * its documentation, with or without modification, for any purpose and
 * without fee or royalty is hereby granted, provided that you include
 * the following on ALL copies of the software and documentation or
 * portions thereof, including modifications, that you make:
 *
 *     a.  The full text of this license in a location viewable to users
 *     of the redistributed or derivative work.
 *
 *     b.  Notice of any changes or modifications to the files,
 *     including the date changes were made.
 *
 * 2.  The name, servicemarks and trademarks of the copyright holders
 * may NOT be used in advertising or publicity pertaining to the
 * software without specific, written prior permission.
 *
 * 3.  Title to copyright in this software and any associated
 * documentation will at all times remain with copyright holders.
 *
 * 4.  THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND
 * COPYRIGHT HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY
 * OR FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.
 *
 * 5.  COPYRIGHT HOLDERS WILL NOT BE LIABLE FOR ANY DAMAGES, INCLUDING
 * BUT NOT LIMITED TO, DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL,
 * ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 * ======================================================================
 * </pre>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $ $Id: sql-common.inc,v 5.26 2009-12-18 16:52:46 danielc Exp $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */

/**
 *
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_General {

    //  DO NOT FILL IN THESE PROPERTIES HERE !

    /**
     *
     * @var  mixed
     */
    var $SQLConnection;

    /**
     *
     * @var  string
     */
    var $SQLQueryString = '';

    /**
     *
     * @var  string
     */
    var $SQLAlternateQueryString = '';

    /**
     *
     * @var  string
     */
    var $SQLVerticalQueryString = '';

    /**
     *
     * @var  string
     */
    var $SQLHorizontalQueryString = '';

    /**
     *
     * @var  string
     */
    var $SQLCreditQueryString = '';

    /**
     *
     * @var  mixed
     */
    var $SQLRecordSet;

    /**
     *
     * @var  integer
     */
    var $SQLRecordSetRowCount = 0;

    /**
     *
     * @var  integer
     */
    var $SQLRecordSetFieldCount = 0;

    /**
     *
     * @var  string
     */
    var $SQLTagStarted = '';

    /**
     *
     * @var  string
     */
    var $SQLEscapeHTML = '';

    /**
     *
     * @var  string
     */
    var $SQLSafeMarkup = '';

    /**
     *
     * @var  string
     */
    var $SQLClassName = '';


    /*
     * R E C O R D      D A T A      S E C T I O N
     *
     * Continued from DBMS specific classes.
     */

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordIntoThis
     */
    function RecordIntoThis($FileName, $FileLine) {
        if (empty($this->SQLRecordSetRowCount)) {
            return 0;
        }

        if ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
                . "error when RIT() was called by $FileName", $FileLine))
        {
            foreach ($Record as $Key => $Val) {
                $this->$Key = $Val;
            }
            return 1;
        } else {
            for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
                $FieldName = $this->FieldName('FieldName() had error when '
                        . "RAT() was called by $FileName",
                        $FileLine, $FieldCounter);
                $this->$FieldName = '';
            }
        }
    }


    /*
     * F I E L D      D E F I N I T I O N S      S E C T I O N
     *
     * Continued from DBMS specific classes.
     */

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldLengthEnumArray
     */
    function FieldLengthEnumArray($FileName, $FileLine) {
        for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
            $Output[] = $this->FieldLength('FieldLength() had error when '
                    . "FLEA() was called by $FileName", $FileLine, $Counter);
        }
        return $Output;
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldLengthAssocArray
     */
    function FieldLengthAssocArray($FileName, $FileLine) {
        for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
            $Output[] = $this->FieldLength('FieldLength() had error when '
                    . "FLEA() was called by $FileName", $FileLine, $Counter);
        }
        return $Output;
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldNameEnumArray
     */
    function FieldNameEnumArray($FileName, $FileLine) {
        for ($Counter = 0; $Counter < $this->SQLRecordSetFieldCount; $Counter++) {
            $Output[] = $this->FieldName('FieldName() had error when '
                    . "FNEA() was called by $FileName", $FileLine, $Counter);
        }
        return $Output;
    }


    /*
     * R E S U L T      D I S P L A Y      S E C T I O N
     */


    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordAsTable
     */
    function RecordAsTable($FileName, $FileLine, $Opt = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
          $Wrap = ' nowrap';
        } else {
          $Wrap = '';
        }

        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="1"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        if (isset($Opt['summary'])) {
            echo ' summary="' . $Opt['summary'] . '"';
        }

        echo ">\n";

        $this->SQLTagStarted = 'table';

        if (isset($Opt['caption'])) {
            echo ' <caption';
            if (isset($Opt['captionalign'])) {
                echo ' align="' . $Opt['captionalign'] . '"';
            }
            echo "$Class>" . $Opt['caption'] . "</caption>\n";
        }

        if (!$this->SQLRecordSetRowCount) {
            echo " <tr$Class><td$Class>No Such Record Exists</td></tr>\n";
            echo "</table>\n";
            $this->SQLTagStarted = '';
            return 0;
        }

        if ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
                . "error when RAT() was called by $FileName", $FileLine))
        {
            if (!isset($Opt['nohead'])) {
                echo " <tr$Class><th scope=\"col\"$Class>Field</th>";
                echo "<th scope=\"col\"$Class>Value</th></tr>\n";
            }
            foreach ($Record as $Key => $Val) {
                echo " <tr valign=\"top\"$Class>";
                echo "<td align=\"right\"$Wrap$Class><b$Class>$Key:</b></td>";
                echo "<td$Wrap$Class>";
                echo ($Val != '') ? ($Val) : ('&nbsp;') . "</td></tr>\n";
            }
            echo "</table>\n";
            $this->SQLTagStarted = '';
            return 1;
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordSetAsList
     */
    function RecordSetAsList($FileName, $FileLine, $Opt = '', $Col = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        if (!isset($Opt['delimiter'])) {
            $Opt['delimiter'] = ', ';
        }

        if (isset($Opt['list']) && $Opt['list'] == 'ol') {
            echo '<ol';
            $this->SQLTagStarted = 'ol';
        } else {
            echo '<ul';
            $this->SQLTagStarted = 'ul';
        }

        if (isset($Opt['type'])) {
            echo ' type="' . $Opt['type'] . '"';
        }

        if (isset($Opt['start'])) {
            echo ' start="' . $Opt['start'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        echo ">\n";

        // If there are no records in a record set...
        if (!$this->SQLRecordSetRowCount) {
            // print a message saying there are no records.
            echo " <li$Class>There are no matching records.</li>\n";

        } else {
            $this->GoToRecord('GoToRecord() had error when RSAL() was '
                    . "called by $FileName", $FileLine);
            $Output = '';

            $FieldNames = $this->FieldNameEnumArray('FieldNameEnumArray() had '
                    . "error when RSAL() was called by $FileName", $FileLine);

            if (!is_array($Col)) {
                $Col = array();
            }

            while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                   . "had error when RSAL() was called by $FileName",
                   $FileLine))
            {
                $Output = array();
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++) {
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
                                AND
                                isset($Col[$FieldNames[$FieldCounter]]['linkurl'])) {

                            $Output[] = '<a href="'
                                . $Col[$FieldNames[$FieldCounter]]['linkurl']
                                . $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']]
                                . "\"$Class>"
                                . $Record[$FieldNames[$FieldCounter]]
                                . '</a>';

                        } else {
                            $Output[] = $Record[$FieldNames[$FieldCounter]];
                        }
                    }
                }

                echo " <li$Class>" . implode($Opt['delimiter'], $Output)
                        . "</li>\n";
            }
        }

        echo "</$this->SQLTagStarted>\n";
        $this->SQLTagStarted = '';
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordSetAsTable
     */
    function RecordSetAsTable($FileName, $FileLine, $Opt = '', $Col = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
          $Wrap = ' nowrap';
        } else {
          $Wrap = '';
        }

        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="1"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        if (isset($Opt['summary'])) {
            echo ' summary="' . $Opt['summary'] . '"';
        }

        echo ">\n";

        $this->SQLTagStarted = 'table';

        if (isset($Opt['caption'])) {
            echo ' <caption';
            if (isset($Opt['captionalign'])) {
                echo ' align="' . $Opt['captionalign'] . '"';
            }
            echo "$Class>" . $Opt['caption'] . "</caption>\n";
        }

        // If there are no records in a record set...
        if (!$this->SQLRecordSetRowCount) {
            // print a message saying there are no records.
            echo " <tr$Class><td$Class>There are no matching records.";
            echo "</td></tr>\n";

        } else {
            // else, there are some records, so let's display them.

            $this->GoToRecord('GoToRecord() had error when RSATbl() was '
                    . "called by $FileName", $FileLine);

            if (!is_array($Col)) {
                $Col = array();
            }

            // Grab field names and lay out column headers:
            $VisibleFields = 0;
            if (!isset($Opt['nohead'])) {
                echo " <tr valign=\"top\"$Class>";
                $this->SQLTagStarted = 'tr';
                for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
                    $FieldNames[] = $this->FieldName('FieldName() had error '
                            . "when RSATbl() was called by $FileName",
                            $FileLine,  $FieldCounter);
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        $VisibleFields++;
                        echo "<th scope=\"col\"$Class>";
                        echo "$FieldNames[$FieldCounter]</th>";
                    }
                }
                echo "</tr>\n";
            } else {
                for ($FieldCounter = 0; $FieldCounter < $this->SQLRecordSetFieldCount; $FieldCounter++) {
                    $FieldNames[] = $this->FieldName('FieldName() had error '
                            . "when RSATbl() was called by $FileName",
                            $FileLine,  $FieldCounter);
                }
            }

            #~:~# Go through each Record in RecordSet...
            while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                   . "had error when RSATbl() was called by $FileName",
                   $FileLine))
            {
                echo " <tr valign=\"top\"$Class>";
                #~:~#
                #~:~# For each field in the row...
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++) {
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
                                AND
                                isset($Col[$FieldNames[$FieldCounter]]['linkurl'])) {
                            echo "<td$Wrap$Class><a href=\"";
                            echo $Col[$FieldNames[$FieldCounter]]['linkurl'];
                            echo $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
                            echo "\"$Class>";
                            echo $Record["$FieldNames[$FieldCounter]"];
                            echo "</a></td>";
                        } else {
                            echo "<td$Wrap$Class>";
                            if (isset($Record[$FieldNames[$FieldCounter]])
                                && $Record[$FieldNames[$FieldCounter]] != '')
                            {
                                echo $Record[$FieldNames[$FieldCounter]];
                            } else {
                                echo '&nbsp;';
                            }
                            echo '</td>';
                        }
                    }
                }
                #~:~#
                echo "</tr>\n";
                #~:~#
            }

            // If CreditString has something in it,
            // layout credits in bottom row of HTML table.
            if ($this->SQLCreditQueryString) {
                $CreditSQL = new $this->SQLClassName;

                $CreditSQL->SQLQueryString = $this->SQLCreditQueryString;
                $CreditSQL->RunQuery('RunQuery() had error when RSATbl() was '
                        . "called by $FileName", $FileLine);

                echo "<tr$Class><td colspan=\"$VisibleFields\"$Class>";
                echo "Credits:\n";
                $CreditSQL->SQLTagStarted = 'td';
                $CreditSQL->RecordSetAsList('RecordSetAsList() had error when '
                        . "RSATbl() was called by $FileName", $FileLine, $Opt);
                echo "</td></tr>\n";

                $this->SQLCreditQueryString = '';
            }
        }

        echo "</table>\n";
        $this->SQLTagStarted = '';
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordSetAsXML
     */
    function RecordSetAsXML($FileName, $FileLine, $Opt = '', $Col = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        if (!isset($Opt['settag'])) {
            $Opt['settag'] = 'recordset';
        }

        if (!isset($Opt['recordtag'])) {
            $Opt['recordtag'] = 'record';
        }

        switch (isset($Opt['prefix']) . ':' . isset($Opt['namespace'])) {
            case '1:':
            case '1:0':
                $Opt['prefix'] = $Opt['prefix'] . ':';
                echo '<' . $Opt['prefix'] . $Opt['settag'] . ">\n";
                break;
            case ':1':
            case '0:1':
                echo "<{$Opt['settag']} xmlns=\"{$Opt['namespace']}\">\n";
                $Opt['prefix'] = '';
                break;
            case '1:1':
                echo "<{$Opt['settag']} xmlns:{$Opt['prefix']}=\"";
                echo $Opt['namespace'] . "\">\n";
                $Opt['prefix'] = $Opt['prefix'] . ':';
                break;
            default:
                echo '<' . $Opt['settag'] . ">\n";
                $Opt['prefix'] = '';
        }

        $this->SQLTagStarted = $Opt['prefix'] . $Opt['settag'];

        if (!$this->SQLRecordSetRowCount) {
            echo '<' . $Opt['prefix'] . $Opt['recordtag'];
            echo '>There are no matching records.</';
            echo $Opt['prefix'] . $Opt['recordtag'] . ">\n";
        } else {
            $this->GoToRecord('GoToRecord() had error when RSAX() was called '
                    . "by $FileName", $FileLine);
            $FieldNames = $this->FieldNameEnumArray('FieldNameEnumArray() had '
                    . "error when RSAX() was called by $FileName", $FileLine);

            if (!is_array($Col)) {
                $Col = array();
            }

            // Go through each Record in RecordSet...
            while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                   . "had error when RSAX() was called by $FileName",
                   $FileLine))
            {
                echo ' <' . $Opt['prefix'] . $Opt['recordtag'] . '>';
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++) {
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
                                AND
                                isset($Col[$FieldNames[$FieldCounter]]['linkurl'])) {
                            echo '<' . $Opt['prefix'];
                            echo "$FieldNames[$FieldCounter]><";
                            echo $Opt['prefix'] . 'a href="';
                            echo $Col[$FieldNames[$FieldCounter]]['linkurl'];
                            echo $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
                            echo "\">" . $Record["$FieldNames[$FieldCounter]"];
                            echo "</{$Opt['prefix']}a></{$Opt['prefix']}";
                            echo "$FieldNames[$FieldCounter]>";
                        } else {
                            echo '<' . $Opt['prefix'];
                            echo "$FieldNames[$FieldCounter]>";
                            if (isset($Record[$FieldNames[$FieldCounter]]) AND
                                    $Record[$FieldNames[$FieldCounter]] != '') {
                                echo $Record[$FieldNames[$FieldCounter]];
                            } else {
                                echo '&nbsp;';
                            }
                            echo '</' . $Opt['prefix'];
                            echo "$FieldNames[$FieldCounter]>";
                        }
                    }
                }
                echo '</' . $Opt['prefix'] . $Opt['recordtag'] . ">\n";
            }
        }

        echo '</' . $Opt['prefix'] . $Opt['settag'] . ">\n\n";
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordSetAsTransform
     */
    function RecordSetAsTransform($FileName, $FileLine, $Opt = '') {
        if (!isset($this->SQLVerticalQueryString)
            || !isset($this->SQLHorizontalQueryString))
        {
            $this->KillQuery($FileName, $FileLine, 'Horizontal and/or '
                . 'vertical query strings are not set. Please set them '
                . 'and try again.');
        }

        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        if (!isset($Opt['flip'])) {
            $Opt['flip'] = 'Y';
        }

        if (!isset($Opt['verticallabel'])) {
            $Opt['verticallabel'] = '';
        }

        if (!isset($Opt['horizontallabel'])) {
            $Opt['horizontallabel'] = '';
        }

        if (!isset($this->SQLAlternateQueryString)) {
            $Opt['flip'] = 'N';
        }

        if ($this->SQLRecordSetRowCount) {
            $this->GoToRecord('GoToRecord() had error when RSATran() was '
                    . "called by $FileName", $FileLine);
        }
        $Counter = 0;
        $ActualVerticalLabel = '';

        // List and count labels for default vertical axis.
        $VerticalSQL = new $this->SQLClassName;
        $VerticalSQL->SQLQueryString = $this->SQLVerticalQueryString;
        $VerticalSQL->RunQuery('RunQuery() had error when RSATran() was '
                . "called by $FileName", $FileLine);

        // List and count labels for default horizontal axis.
        $HorizontalSQL = new $this->SQLClassName;
        $HorizontalSQL->SQLQueryString = $this->SQLHorizontalQueryString;
        $HorizontalSQL->RunQuery('RunQuery() had error when RSATran() was '
                . "called by $FileName", $FileLine);

        if (2 > ($HorizontalSQL->SQLRecordSetRowCount
                + $VerticalSQL->SQLRecordSetRowCount)) {
            $this->KillQuery($FileName, $FileLine, 'Problem with Transform '
                    . 'Queries: horizontal and/or vertical query results '
                    . 'contain no rows. Fix your queries and try again.');
        }

        /*
         * The axis with the most labels goes into the rows, so table is
         * easier to read.  If we need to flip things around, transpose the
         * appropriate variables.  But only do this if $Opt['flip'] = Y
         */
        if ($HorizontalSQL->SQLRecordSetRowCount > $VerticalSQL->SQLRecordSetRowCount
            && $Opt['flip'] == 'Y')
        {

            $this->SQLQueryString = $this->SQLAlternateQueryString;
            $HorizontalSQL->SQLRecordSet = $VerticalSQL->SQLRecordSet;

            $Transposer = $VerticalSQL->SQLRecordSetRowCount;
            $VerticalSQL->SQLRecordSetRowCount
                    = $HorizontalSQL->SQLRecordSetRowCount;
            $HorizontalSQL->SQLRecordSetRowCount = $Transposer;

            $Transposer = $Opt['verticallabel'];
            $Opt['verticallabel'] = $Opt['horizontallabel'];
            $Opt['horizontallabel'] = $Transposer;
        }

        // Run the main query.
        $this->RunQuery('RunQuery() had error when RSATran() was called '
                . "by $FileName", $FileLine);

        // Test to see if query results line up correcly.
        if ($HorizontalSQL->SQLRecordSetRowCount
                * $VerticalSQL->SQLRecordSetRowCount
                != $this->SQLRecordSetRowCount)
        {
            $this->KillQuery($FileName, $FileLine, 'Problem with Transform '
                    . 'Queries: the number of records from the main query '
                    . 'does not have same number of records as the '
                    . 'horizontal query * the vertical query. Fix your '
                    . 'queries and try again.');
        }

        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="1"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        echo ' summary="';
        if (isset($Opt['summary'])) {
            echo $Opt['summary'] . '. ';
        }
        if (isset($Opt['title'])) {
            echo 'Top cell spans whole table, contains Title. ';
        }
        if ($this->SQLCreditQueryString) {
            echo 'Bottom cell spans whole table, listing credits. ';
        }
        echo 'Rows contain ' . $Opt['verticallabel'] . '. Columns contain '
                . $Opt['horizontallabel'] . ".\">\n";

        $this->SQLTagStarted = 'table';

        if (isset($Opt['caption'])) {
            echo ' <caption';
            if (isset($Opt['captionalign'])) {
                echo ' align="' . $Opt['captionalign'] . '"';
            }
            echo "$Class>" . $Opt['caption'] . "</caption>\n";
        }

        // Add line breaks as needed.
        $Opt['verticallabel'] = preg_replace('/(.)(&[a-zA-Z]{2,4};)*/',
                '\\1\\2<br />', $Opt['verticallabel']);
        $Opt['verticallabel'] = preg_replace('/\\\2</',
                '<', $Opt['verticallabel']);
        $Opt['verticallabel'] = preg_replace('/(.)(&)/',
                '\\1<br />\\2', $Opt['verticallabel']);

        #..# HTML table header layout.
        if (isset($Opt['title'])) {
            echo " <tr$Class><td colspan=\"";
            echo ($HorizontalSQL->SQLRecordSetRowCount + 2);
            echo "\"$Class><h2$Class>" . $Opt['title'] . "</h2></td>\n";
        }
        echo " <tr$Class><td colspan=\"2\"";
        if (isset($Opt['background'])) {
            echo ' background="' . $Opt['background'] . '"';
        }
        echo ' rowspan="2" alt="Blank cell for formatting purposes."';
        echo "$Class>&nbsp;</td><th colspan=\"";
        echo "$HorizontalSQL->SQLRecordSetRowCount\" align=\"left\"";
        echo "scope=\"colgroup\"$Class>" . $Opt['horizontallabel'];
        echo "</th></tr>\n <tr$Class>";
        #..#
        #..#  HTML column header layout.
        $this->SQLTagStarted = 'tr';
        while ($Record = $HorizontalSQL->RecordAsEnumArray('RecordAsEnumArray() '
               . "had error when RSATran() was called by $FileName",
               $FileLine))
        {
            echo "<th scope=\"col\"$Class>" . $Record[0] . '</th>';
        }
        #..#
        echo "</tr>\n";

        #'.'#  Print HTML table rows.
        for ($Vlocation = 0; $Vlocation < $VerticalSQL->SQLRecordSetRowCount; $Vlocation++) {
            #'.'#  Get the next record.
            $this->SQLTagStarted = 'table';
            $Record = $this->RecordAsEnumArray('RecordAsEnumArray() had error '
                    . "when RSATran() was called by $FileName", $FileLine);
            #'.'#
            #'.'#  Is this the first HTML table row?
            if ($Vlocation == 0) {
                #'.'#  Yes, so print out the side header plus the 
                #'.'#  HTML table row title for the first record.
                echo " <tr$Class><td rowspan=\"";
                echo "$VerticalSQL->SQLRecordSetRowCount\" align=\"center\" ";
                echo "valign=\"top\" scope=\"rowgroup\"$Class><b$Class>";
                echo $Opt['verticallabel'] . "</b></td>\n      ";
                echo "<td nowrap scope=\"row\"$Class><b$Class>";
                echo $Record[0] . '</b></td>';
            } else {
                #'.'#  No, so just print out the HTML table row title
                echo " <tr$Class><td nowrap scope=\"row\"$Class><b$Class>";
                echo $Record[0] . '</b></td>';
            }

            #'.'#  Print out just the quantity for the first HTML table column.
            #'.'#  If field not blank...    print data...  else print space
            echo "<td align=\"right\"$Class>";
            if ($Record[1] != '') {
                echo $Record[1];
            } else {
                echo '&nbsp;';
            }
            echo '</td>';

            #'.'#
            #'.'#  Print out quantities for the remaining HTML table columns.
            $this->SQLTagStarted = 'tr';
            for ($Hlocation = 1; $Hlocation < $HorizontalSQL->SQLRecordSetRowCount; $Hlocation++) {
                $Record = $this->RecordAsEnumArray('RecordAsEnumArray() had '
                    . "error when RSATran() was called by $FileName",
                    $FileLine);
                #'.'#  If field not blank...    print data...  else print space
                echo "<td align=\"right\"$Class>";
                if ($Record[1] != '') {
                    echo $Record[1];
                } else {
                    echo '&nbsp;';
                }
                echo '</td>';
            }
            #'.'#
            echo "</tr>\n";
        }

        unset($Record);
        unset($VerticalSQL);

        $CreditWidth = $HorizontalSQL->SQLRecordSetRowCount + 2;
        unset($HorizontalSQL);

        // If CreditString has something in it, layout credits in bottom row
        // of HTML table.
        if ($this->SQLCreditQueryString) {
            $CreditSQL = new $this->SQLClassName;

            $CreditSQL->SQLQueryString = $this->SQLCreditQueryString;
            $CreditSQL->RunQuery('RunQuery() had error when RSATran() was '
                    . "called by $FileName", $FileLine);

            echo " <tr$Class><td colspan=\"$CreditWidth\"$Class>Credits:\n";
            $CreditSQL->SQLTagStarted = 'td';
            $CreditSQL->RecordSetAsList('RecordSetAsList() had error when '
                    . "RSATran() was called by $FileName", $FileLine, $Opt);
            echo " </td></tr>\n";

            $this->SQLCreditQueryString = '';
        }

        echo "</table>\n";
        $this->SQLTagStarted = '';
        $this->SQLQueryString = '';
        $this->SQLAlternateQueryString = '';
    }


    /*
     * F O R M      G E N E R A T I O N      S E C T I O N
     */


    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#OptionListGenerator
     */
    function OptionListGenerator($FileName, $FileLine, $Opt = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                switch ($Key) {
                    case 'default':
                    case 'where':
                    case 'orderby':
                    case 'groupby':
                    case 'add':
                        break;

                    default:
                        $Opt[$Key] = htmlspecialchars($Val);
                }
            }
        } else {
            $Opt = array();
        }

        // Validate the arguments
        if (!isset($Opt['default'])) {
            $Opt['default'] = '';
        }

        if (!isset($Opt['name']) OR $Opt['name'] == '') {
            $this->KillQuery("OptionListGenerator() had error when called by
                    $FileName", $FileLine, "'name' argument was empty.");
        }

        if (!isset($Opt['keyfield']) OR $Opt['keyfield'] == '') {
            $this->KillQuery("OptionListGenerator() had error when called by
                    $FileName", $FileLine, "'keyfield' argument was empty.");
        }

        if (!isset($Opt['visiblefield']) OR $Opt['visiblefield'] == '') {
            $this->KillQuery("OptionListGenerator() had error when called by
                    $FileName", $FileLine, "'visiblefield' argument was empty.");
        }

        if (!isset($Opt['where']) OR $Opt['where'] == '') {
            $this->KillQuery("OptionListGenerator() had error when called by
                    $FileName", $FileLine, "'where' argument was empty.");
        }

        if (!isset($Opt['orderby']) OR $Opt['orderby'] == '') {
            $this->KillQuery("OptionListGenerator() had error when called by
                    $FileName", $FileLine, "'orderby' argument was empty.");
        }

        $this->SQLQueryString = "SELECT {$Opt['keyfield']}, "
                 . "{$Opt['visiblefield']} "
                 . "FROM {$Opt['table']} WHERE {$Opt['where']} ";

        if (isset($Opt['groupby']) && $Opt['groupby'] == '') {
            $this->SQLQueryString .= "GROUP BY {$Opt['groupby']} ";
        }

        $this->SQLQueryString .= "ORDER BY {$Opt['orderby']}";

        $this->RunQuery('RunQuery() had error when OLG() was called by'
                . $FileName, $FileLine);

        // Start the list box
        echo "\n\n<select";

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        echo $Class;

        if (isset($Opt['id'])) {
            echo ' id="' . $Opt['id'] . '"';
        }

        if (isset($Opt['size'])) {
            echo ' size="' . $Opt['size'] . '"';
        }

        if (isset($Opt['multiple']) && $Opt['multiple'] == 'Y') {
            echo ' multiple name="' . $Opt['name'] . "[]\">\n";
            if (!is_array($Opt['default'])) {
                $Opt['default'] = array($Opt['default']);
            } else {
                reset($Opt['default']);
            }
        } else {
            echo ' name="' . $Opt['name'] . "\">\n";
            if (is_array($Opt['default'])) {
                reset($Opt['default']);
                $Opt['default'] = array(current($Opt['default']));
            } else {
                $Opt['default'] = array($Opt['default']);
            }
        }

        if (isset($Opt['add'])) {
            foreach ($Opt['add'] as $Value => $Visible) {
                echo ' <option value="' . htmlspecialchars($Value) . '"';
                if (in_array($Value, $Opt['default'])) {
                    echo ' selected="selected"';
                }
                echo "$Class>" . htmlspecialchars($Visible) . "</option>\n";
            }
        }

        $this->SQLTagStarted = 'select';

        // Now, get down to business...
        if (!$this->SQLRecordSetRowCount) {
            echo " <option value=\"\"$Class>No Matching Records</option>\n";
        } else {
            while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                   . "had error when OLG() was called by $FileName",
                   $FileLine))
            {
                echo ' <option value="' . $Record[$Opt['keyfield']] . '"';
                if (in_array($Record[$Opt['keyfield']], $Opt['default'])) {
                    echo ' selected="selected"';
                }
                echo "$Class>{$Record[$Opt['visiblefield']]}</option>\n";
            }

        }

        echo "</select>\n\n";
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#InputListGenerator
     */
    function InputListGenerator($FileName, $FileLine, $Opt = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                switch ($Key) {
                    case 'where':
                    case 'default':
                    case 'add':
                    case 'orderby':
                    case 'groupby':
                        break;

                    default:
                        $Opt[$Key] = htmlspecialchars($Val);
                }
            }
        } else {
            $Opt = array();
        }

        if (!isset($Opt['default'])) {
            $Opt['default'] = '';
        }

        if (!isset($Opt['name']) OR $Opt['name'] == '') {
            $this->KillQuery('OptionListGenerator() had error when called by '
                    . $FileName, $FileLine, "'name' argument empty/not set.");
        }

        if (!isset($Opt['keyfield']) OR $Opt['keyfield'] == '') {
            $this->KillQuery('OptionListGenerator() had error when called by '
                    . $FileName, $FileLine, "'keyfield' argument "
                    . 'empty/not set.');
        }

        if (!isset($Opt['visiblefield']) OR $Opt['visiblefield'] == '') {
            $this->KillQuery('OptionListGenerator() had error when called by '
                    . $FileName, $FileLine, "'visiblefield' argument "
                    . 'empty/not set.');
        }

        if (!isset($Opt['where']) OR $Opt['where'] == '') {
            $this->KillQuery('OptionListGenerator() had error when called by '
                    . $FileName, $FileLine, "'where' argument empty/not set.");
        }

        if (!isset($Opt['orderby']) OR $Opt['orderby'] == '') {
            $this->KillQuery('OptionListGenerator() had error when called by '
                    . $FileName, $FileLine, "'orderby' argument "
                    . 'empty/not set.');
        }

        if (!isset($Opt['type'])) {
            $Opt['type'] = 'checkbox';
        }

        if (empty($Opt['columns'])) {
            $Opt['columns'] = 2;
        } else {
            settype($Opt['columns'], 'integer');
            if (!$Opt['columns']) {
                $Opt['columns'] = 2;
            }
        }

        if (!isset($Opt['all'])) {
            $Opt['all'] = '';
        }

        $this->SQLQueryString = "SELECT {$Opt['keyfield']}, "
                 . "{$Opt['visiblefield']} "
                 . "FROM {$Opt['table']} WHERE {$Opt['where']} ";

        if (isset($Opt['groupby']) && $Opt['groupby'] == '') {
            $this->SQLQueryString .= "GROUP BY {$Opt['groupby']} ";
        }

        $this->SQLQueryString .= "ORDER BY {$Opt['orderby']}";

        $this->RunQuery('RunQuery() had error when ILG() was called by '
                . $FileName, $FileLine);
        // debug tool -> //    echo htmlspecialchars($this->SQLQueryString);

        // Start the table
        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="2"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        } else {
            echo ' width="100%"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        if (isset($Opt['summary'])) {
            echo ' summary="' . $Opt['summary'] . '"';
        }

        echo ">\n";

        if ($Opt['type'] == 'checkbox') {
            $Bracket = '[]';
            if (!is_array($Opt['default'])) {
                $Opt['default'] = array($Opt['default']);
            } else {
                reset($Opt['default']);
            }
        } else {
            // This is a radio button list.
            $Bracket = '';
            $Opt['all'] = '';

            if (is_array($Opt['default'])) {
                reset($Opt['default']);
                $Opt['default'] = array(current($Opt['default']));
            } else {
                $Opt['default'] = array($Opt['default']);
            }
        }

        echo " <tr valign=\"top\"$Class>\n";

        if (empty($Opt['add'])) {
            $Opt['add'] = array();
        }

        $Adds = count($Opt['add']);

        $Items = $this->SQLRecordSetRowCount + $Adds;
        if (empty($Items)) {
            $Break = 0;
            $ColumnWidth = 0;
        } else {
            if ($Opt['columns'] > $Items) {
                $Opt['columns'] = $Items;
            }
            $Break = ceil($Items / $Opt['columns']);
            $ColumnWidth = floor(100 / $Opt['columns']);
        }

        if (!$Items) {
            echo '  <td><input type="' . $Opt['type'] . '" name="';
            echo $Opt['name'] . "$Bracket\" value=\"\" />";
            echo "No Matching Records</td>\n";
        } else {
            for ($ItemCounter = 1;  $ItemCounter <= $Items;) {
                echo "  <td nowrap width=\"$ColumnWidth%\"$Class>\n            ";
                $this->SQLTagStarted = 'td';

                for ($RowCounter = 1;  $RowCounter <= $Break;) {
                    if ($ItemCounter > $Adds) {
                        $Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                                . 'had error when ILG() was called by '
                                . $FileName, $FileLine);
                    } else {
                        list($Value,$Visible) = each($Opt['add']);
                        $Record[$Opt['keyfield']] = htmlspecialchars($Value);
                        $Record[$Opt['visiblefield']]
                                = htmlspecialchars($Visible);
                    }

                    echo '<input type="' . $Opt['type'] . '" name="';
                    echo $Opt['name'] . "$Bracket\" value=\"";
                    echo $Record[$Opt['keyfield']] . '"';
                    if ($Opt['all'] == 'Y' || in_array(
                            $Record[$Opt['keyfield']], $Opt['default'])) {
                        echo ' checked="checked"';
                    }
                    echo "$Class />";

                    if (isset($Opt['linkurl'])) {
                        echo '<a href="' . $Opt['linkurl'];
                        echo $Record[$Opt['keyfield']] . "\"$Class>";
                        echo $Record[$Opt['visiblefield']] . "</a>\n    <br />";
                    } else {
                        echo $Record[$Opt['visiblefield']] . "\n    <br />";
                    }

                    if ($ItemCounter == $Items) {
                        echo "\n  </td>\n";
                        break 2;
                    }

                    $ItemCounter++;
                    $RowCounter++;
                }

                echo "\n  </td>\n";
                $RowCounter = 1;
            }
        }

        echo "</tr>\n</table>\n\n";
        $this->SQLTagStarted = '';
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordAsInput
     */
    function RecordAsInput($FileName, $FileLine, $Opt = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                $Opt[$Key] = htmlspecialchars($Val);
            }
        } else {
            $Opt = array();
        }

        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="1"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        if (isset($Opt['summary'])) {
            echo ' summary="' . $Opt['summary'] . '"';
        }

        echo ">\n";

        $this->SQLTagStarted = 'table';

        if (isset($Opt['caption'])) {
            echo ' <caption';
            if (isset($Opt['captionalign'])) {
                echo ' align="' . $Opt['captionalign'] . '"';
            }
            echo "$Class>" . $Opt['caption'] . "</caption>\n";
        }

        if (!$this->SQLRecordSetRowCount) {
            echo " <tr$Class><td$Class>No Such Record Exists</td></tr>\n";
            echo "</table>\n";
            return 0;
        }

        if ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() had '
                . "error when RAI() was called by $FileName", $FileLine))
        {
            $Counter = 0;
            if (!isset($Opt['nohead'])) {
                echo " <tr$Class><th scope=\"col\"$Class>Field</th>";
                echo "<th scope=\"col\" abbr=\"Input\"$Class>Data Input</th>";
                echo "</tr>\n";
            }
            foreach ($Record as $Key => $Val) {
                $Length = $this->FieldLength('FieldLength() had error when RAI'
                        . "() was called by $FileName", $FileLine, $Counter);

                echo " <tr valign=\"top\"$Class>\n  <td align=\"right\"";
                echo "$Class><b$Class>$Key:</b></td>\n  <td$Class>";

                if ($Length > 59) {
                    if ($Length < 240) {
                        $Rows = floor($Length / 60);
                    } else {
                        $Rows = 4;
                    }
                    echo "<textarea wrap name=\"$Key\" cols=\"60\" ";
                    echo "rows=\"$Rows\" maxlength=\"$Length\"$Class>";
                    echo "$Val</textarea>";

                } else {
                    echo "<input type=\"text\" name=\"$Key\" value=\"$Val\" ";
                    echo "size=\"$Length\" maxlength=\"$Length\"$Class />";
                }

                echo "</td>\n </tr>\n";
                $Counter++;
            }

            echo "</table>\n";
            $this->SQLTagStarted = '';
            return 1;
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordSetAsInput
     */
    function RecordSetAsInput($FileName, $FileLine, $Opt = '', $Col = '') {
        if (is_array($Opt)) {
            foreach ($Opt as $Key => $Val) {
                if ($Key != 'default') {
                    $Opt[$Key] = htmlspecialchars($Val);
                }
            }
        } else {
            $Opt = array();
        }

        if (!isset($Opt['keyfield']) OR !$Opt['keyfield']) {
            $this->KillQuery('RecordSetAsInput() had error when called by '
                    . $FileName, $FileLine, 'keyfield argument was empty.');
        }

        if (!isset($Opt['default'])) {
            $Opt['default'] = '';
        }

        if (empty($Opt['name'])) {
            $Opt['name'] = 'Input';
        }

        if (empty($Opt['inputheader'])) {
            $Opt['inputheader'] = 'Input';
        }

        if (!isset($Opt['size'])) {
            $Opt['size'] = '3';
        }

        if (empty($Opt['maxlength'])) {
            $Opt['maxlength'] = '3';
        }

        if (empty($Opt['all'])) {
            $Opt['all'] = 'N';
        }

        if (isset($Opt['wrap']) && $Opt['wrap'] == 'N') {
            $Wrap = ' nowrap';
        } else {
            $Wrap = '';
        }

        if (!isset($Opt['type'])) {
            $Opt['type'] = '';
        }

        switch ($Opt['type']) {
            case 'text':
                if (is_array($Opt['default'])) {
                    reset($Opt['default']);
                } else {
                    $Opt['default'] = array();
                }
                break;
            case 'radio':
                if (is_array($Opt['default'])) {
                    reset($Opt['default']);
                    $Opt['default'] = array(current($Opt['default']));
                } else {
                    $Opt['default'] = array($Opt['default']);
                }
                break;
            default:
                if (is_array($Opt['default'])) {
                    reset($Opt['default']);
                } else {
                    $Opt['default'] = array($Opt['default']);
                }
        }

        // Start the table
        echo '<table';

        if (isset($Opt['border'])) {
            echo ' border="' . $Opt['border'] . '"';
        } else {
            echo ' border="1"';
        }

        if (isset($Opt['cellpadding'])) {
            echo ' cellpadding="' . $Opt['cellpadding'] . '"';
        }

        if (isset($Opt['cellspacing'])) {
            echo ' cellspacing="' . $Opt['cellspacing'] . '"';
        }

        if (isset($Opt['align'])) {
            echo ' align="' . $Opt['align'] . '"';
        }

        if (isset($Opt['width'])) {
            echo ' width="' . $Opt['width'] . '"';
        }

        $Class = '';
        if (isset($Opt['class'])) {
            $Class .= ' class="' . $Opt['class'] . '"';
        }
        if (isset($Opt['id'])) {
            $Class .= ' id="' . $Opt['id'] . '"';
        }
        echo $Class;

        if (isset($Opt['summary'])) {
            echo ' summary="' . $Opt['summary'] . '"';
        }

        echo ">\n";

        $this->SQLTagStarted = 'table';

        if (isset($Opt['caption'])) {
            echo ' <caption';
            if (isset($Opt['captionalign'])) {
                echo ' align="' . $Opt['captionalign'] . '"';
            }
            echo "$Class>" . $Opt['caption'] . "</caption>\n";
        }

        // Now, get down to business
        if (!$this->SQLRecordSetRowCount) {
            echo " <tr$Class><td$Class>There are no matching records.";
            echo "</td></tr>\n";
        } else {
            $this->GoToRecord('GoToRecord() had error when RSAI() was called '
                    . "by $FileName", $FileLine);

            if (!is_array($Col)) {
                $Col = array();
            }

            // Lay out column headers
            if (!isset($Opt['nohead'])) {
                echo " <tr valign=\"top\"$Class><th scope=\"col\"$Class>";
                echo $Opt['inputheader'] . "</th>";
                $this->SQLTagStarted = 'tr';
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++) {
                    $FieldNames[] = $this->FieldName('FieldName() had error '
                            . "when RSAI() was called by $FileName",
                            $FileLine, $FieldCounter);
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        echo "<th scope=\"col\"$Class>";
                        echo "$FieldNames[$FieldCounter]</th>";
                    }
                }
                echo "</tr>\n";
            } else {
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++)
                {
                    $FieldNames[] = $this->FieldName('FieldName() had error '
                            . "when RSATbl() was called by $FileName",
                            $FileLine, $FieldCounter);
                }
            }

            #~:~# Go through each Record in RecordSet
            while ($Record = $this->RecordAsAssocArray('RecordAsAssocArray() '
                   . "had error when RSAI() was called by $FileName",
                   $FileLine))
            {
                echo " <tr valign=\"top\"$Class>";

                #~:~# Display the form input field.
                echo "<td align=\"center\"$Wrap$Class><input name=\"";
                echo $Opt['name'];

                switch ($Opt['type']) {
                    case 'text':
                        echo '[' . $Record[$Opt['keyfield']] . ']"';
                        echo 'type="text" value="';
                        if (key($Opt['default']) == $Record[$Opt['keyfield']]) {
                            echo substr(current($Opt['default']),
                                    0, $Opt['maxlength']) ;
                            next($Opt['default']);
                        }
                        echo '" size="' . $Opt['size'] . '" maxlength="';
                        echo $Opt['maxlength'] . '"';
                        break;
                    case 'radio':
                        echo '" type="radio" value="';
                        echo $Record[$Opt['keyfield']] . '"';
                        if ($Opt['all'] == 'Y' || in_array(
                                $Record[$Opt['keyfield']], $Opt['default'])) {
                            echo ' checked="checked"';
                        }
                        break;
                    default:
                        echo '[]" type="checkbox" value="';
                        echo $Record[$Opt['keyfield']] . '"';
                        if ($Opt['all'] == 'Y' || in_array(
                                $Record[$Opt['keyfield']], $Opt['default'])) {
                            echo ' checked="checked"';
                        }
                }

                echo "$Class /></td>";

                #~:~# For each field in the RecordSet...
                for ($FieldCounter = 0;
                        $FieldCounter < $this->SQLRecordSetFieldCount;
                        $FieldCounter++) {
                    if (!isset($Col[$FieldNames[$FieldCounter]]['hide'])) {
                        if (isset($Col[$FieldNames[$FieldCounter]]['keyfield'])
                                AND
                                isset($Col[$FieldNames[$FieldCounter]]['linkurl'])) {
                            echo "<td scope=\"row\"$Wrap$Class><a href=\"";
                            echo $Col[$FieldNames[$FieldCounter]]['linkurl'];
                            echo $Record[$Col[$FieldNames[$FieldCounter]]['keyfield']];
                            echo "\"$Class>";
                            echo $Record["$FieldNames[$FieldCounter]"];
                            echo "</a></td>";
                        } else {
                            echo "<td$Wrap$Class>";
                            if (isset($Record[$FieldNames[$FieldCounter]]) AND
                                    $Record[$FieldNames[$FieldCounter]] != '') {
                                echo $Record[$FieldNames[$FieldCounter]];
                            } else {
                                echo '&nbsp;';
                            }
                            echo '</td>';
                        }
                    }
                }
                #~:~#
                echo "</tr>\n";
                #~:~#
            }
        }

        echo "</table>\n";
        $this->SQLTagStarted = '';
    }


    /*
     * U T I L I T I E S      S E C T I O N
     */


    /**
     * Transforms Safe Markup into HTML
     */
    function ParseSafeMarkup($Val) {
        // Paired Elements
        $Val = preg_replace('@(::)(/?)(p|ul|ol|li|dl|dt|dd|b|i|code|sup|pre|tt|em|blockquote)(::)@', '<\\2\\3>', $Val);
        // Empty Elements
        $Val = preg_replace('/(::)(br|hr)(::)/', '<\\2 />', $Val);
        // Character References
        $Val = preg_replace('/(::)([a-z]{2,6}|[a-z]{2,4}[0-9]{1,2})(::)/', '&\\2;', $Val);
        // Character Entities
        $Val = preg_replace('/(::)([0-9]{2,4})(::)/', '&#\\2;', $Val);
        // Plain URI's
        $Val = preg_replace('@(?<!::)(http://|https://|ftp://|gopher://|news:|mailto:)([\w/!#$%&\'()*+,.:;=?\@~-]+)([\w/!#$%&\'()*+:;=?\@~-])@i', '<a href="\\1\\2\\3">\\1\\2\\3</a>', $Val);
        // Ancored URI's
        $Val = preg_replace('@(::a::)(http://|https://|ftp://|gopher://|news:|mailto:)([\w/!#$%&\'()*+,.:;=?\@~-]+)([\w/!#$%&\'()*+:;=?\@~-])(::a::)(.*)(::/a::)@iU', '<a href="\\2\\3\\4">\\6</a>', $Val);

        return $Val;
    }

    /**
     *
     * <p>
     * <kbd>2037-12-31 23:59:59</kbd> is max unix_timestamp in MySQL.
     * <kbd>2038-01-18 22:14:09</kbd> is max time validated by mktime() in PHP.
     * </p>
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#TimestampToUnix
     */
    function TimestampToUnix($FileName, $FileLine, $Time) {
        if (!preg_match('/^(19[7-9][0-9]|20[0-2][0-9]|203[0-7])(0[0-9]|1[0-2])'
                . '([0-2][0-9]|3[0-1])([0-1][0-9]|2[0-3])([0-5][0-9])'
                . '([0-5][0-9])$/', $Time, $Atom)
            || !checkdate($Atom[2], $Atom[3], $Atom[1]))
        {
            $this->KillQuery($FileName, $FileLine, "$Time is an invalid "
                    . 'timestamp. Perhaps the date exceeds Unix timestamp '
                    . 'or input was not formatted properly.');
        } else {
            return @mktime($Atom[4], $Atom[5], $Atom[6], $Atom[2], $Atom[3],
                    $Atom[1]);
        }
    }

    /**
     *
     * <p>
     * <kbd>2037-12-31 23:59:59</kbd> is max unix_timestamp in MySQL.
     * <kbd>2038-01-18 22:14:09</kbd> is max time validated by mktime() in PHP.
     * </p>
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#DatetimeToUnix
     */
    function DatetimeToUnix($FileName, $FileLine, $Time) {
        if (!preg_match('/^(19[7-9][0-9]|20[0-2][0-9]|203[0-7])-(0[0-9]|1[0-2])-'
                . '([0-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9]):'
                . '([0-5][0-9])$/', $Time, $Atom)
            || !checkdate($Atom[2],$Atom[3],$Atom[1]))
        {
            $this->KillQuery($FileName, $FileLine, "$Time is an invalid "
                    . 'timestamp. Perhaps the date exceeds Unix timestamp '
                    . 'or input was not formatted properly.');
        } else {
            return @mktime($Atom[4], $Atom[5], $Atom[6], $Atom[2], $Atom[3],
                    $Atom[1]);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#OverflowProtectionInSQL
     */
    function OverflowProtectionInSQL($FileName, $FileLine, $Break = 5) {
        if (!is_int($Break)) {
            $this->KillQuery($FileName, $FileLine, 'Break argument for '
                    . 'Overflow Protection must be integer.');
        }

        static $OverflowCounter = 1;

        if ($OverflowCounter++ > $Break) {
            // Write an event into the error log.
            $this->KillQuery($FileName, $FileLine, 'Overflow Protection '
                    . 'was triggered.');
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#CopyObjectContentsIntoSQL
     */
    function CopyObjectContentsIntoSQL($FileName, $FileLine, $From) {
        global $$From;

        if (!is_object($$From)) {
            $this->KillQuery($FileName, $FileLine, 'The object you called, '
                    . "$From, doesn't seem to be set.");
        }

        foreach ($$From as $Key => $Val) {
            $this->$Key = $Val;
        }
    }
}

/**
 *
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_ErrorHandler extends SQLSolution_General {

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#KillQuery
     */
    function KillQuery($FileName, $FileLine, $Message) {
        /*
         * Close any tags which were started in order to ensure
         * error pages contain "well-formed" XML syntax.
         */
        switch ($this->SQLTagStarted) {
            case 'td':
            case 'th':
                echo "</$this->SQLTagStarted>";
            case 'tr':
                echo '</tr>';
            case 'table':
                echo "</table>\n";
                break;
            case '':
                break;

            default:
                echo "</$this->SQLTagStarted>\n";
        }

        echo "\n<h3>A Database Problem Occurred.\n";
        echo '<br />Please make a note of the present time and what you were ';
        echo "doing.\n<br />Then contact the System Administrator.</h3>\n";

        // debug tool -> // echo "\n<p>File: $FileName\n<br />Line: $FileLine\n</p>\n\n<p>Error:<br />\n" . htmlspecialchars($Message) . "\n</p>\n\n<p>Most Recent Query String:<br />\n" . htmlspecialchars($this->SQLQueryString) . "\n</p>\n\n";

        echo "</body></html>\n\n";
        exit;
    }
}
