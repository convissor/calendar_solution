<?php

/**
 * SQL Solution's MySQL specific code.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2009
 * @version    $Name:  $ $Id: sql-my.inc,v 5.19 2009-12-28 16:29:12 danielc Exp $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see        sql-common.inc
 */

/**
 * SQL Solution's MySQL specific methods.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2009
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_MySQLSpecifics extends SQLSolution_Customizations {

    /*
     * C O N N E C T I O N      S E C T I O N
     */

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#Connect
     */
    function Connect($FileName, $FileLine) {
        ini_set('track_errors', 1);
        $php_errormsg = '';

        if (version_compare(phpversion(), '4.3.0', '>=')) {
            $this->SQLConnection = @mysql_connect($this->SQLHost,
                                                  $this->SQLUser,
                                                  $this->SQLPassword,
                                                  $this->SQLNewLink,
                                                  $this->SQLClientFlags)
                    or die
                    ($this->KillQuery($FileName, $FileLine, $php_errormsg));
        } else {
            $this->SQLConnection = @mysql_connect($this->SQLHost,
                                                  $this->SQLUser,
                                                  $this->SQLPassword)
                    or die
                    ($this->KillQuery($FileName, $FileLine, $php_errormsg));
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#PersistentConnect
     */
    function PersistentConnect($FileName, $FileLine) {
        ini_set('track_errors', 1);
        $php_errormsg = '';

        if (version_compare(phpversion(), '4.3.0', '>=')) {
            $this->SQLConnection = @mysql_pconnect($this->SQLHost,
                                                   $this->SQLUser,
                                                   $this->SQLPassword,
                                                   $this->SQLClientFlags)
                    or die
                    ($this->KillQuery($FileName, $FileLine, $php_errormsg));
        } else {
            $this->SQLConnection = @mysql_pconnect($this->SQLHost,
                                                   $this->SQLUser,
                                                   $this->SQLPassword)
                    or die
                    ($this->KillQuery($FileName, $FileLine, $php_errormsg));
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#ObtainHandle
     */
    function ObtainHandle($FileName, $FileLine) {
        if (!$this->SQLConnection) {
            $this->Connect($FileName, $FileLine);
        }
        $this->SQLDbHandle = @mysql_select_db($this->SQLDbName,
                $this->SQLConnection)
                or die ($this->KillQuery($FileName, $FileLine,
                'Could not select database. '
                . 'Invalid database name or connection ID.'));
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#Disconnect
     */
    function Disconnect($FileName, $FileLine) {
        @mysql_close($this->SQLConnection);
    }


    /*
     * Q U E R Y      S E C T I O N
     */


    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RunQuery
     */
    function RunQuery($FileName, $FileLine) {
        if (!isset($this->SQLDbHandle)) {
            $this->ObtainHandle($FileName, $FileLine);
        }

        $php_errormsg = '';

        if ($this->SQLRecordSet = @mysql_query($this->SQLQueryString,
                $this->SQLConnection)) {

            if (!$this->SQLRecordSetFieldCount =
                    @mysql_num_fields($this->SQLRecordSet)) {
                $this->SQLRecordSetFieldCount = 0;
            }

            if (!$this->SQLRecordSetRowCount =
                    @mysql_num_rows($this->SQLRecordSet)) {
                $this->SQLRecordSetRowCount = 0;
            }
        } elseif ($php_errormsg == '') {
            // Probably a database error.
            $this->KillQuery($FileName, $FileLine,
                    @mysql_error($this->SQLConnection));
        } else {
            // Some PHP error.  Probably a bad Connection.  Complain.
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RunQuery_RowsNeeded
     */
    function RunQuery_RowsNeeded($FileName, $FileLine, $RowsNeeded = 1) {

        $this->RunQuery($FileName, $FileLine);

        if ($this->SQLRecordSetRowCount == $RowsNeeded) {
            return 1;
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RunQuery_NoDuplicates
     */
    function RunQuery_NoDuplicates($FileName, $FileLine) {
        if (!isset($this->SQLDbHandle)) {
            $this->ObtainHandle($FileName, $FileLine);
        }

        $php_errormsg = '';

        if ($this->SQLRecordSet = @mysql_query($this->SQLQueryString,
                $this->SQLConnection)) {
            $this->SQLRecordSetFieldCount = 0;
            $this->SQLRecordSetRowCount = 0;
            return 1;
        } elseif ($php_errormsg == '') {
            switch (@mysql_errno($this->SQLConnection)) {
                case 1022:
                case 1062:
                    // Couldn't insert/update record due to duplicate key.
                    break;

                default:
                    // Some other database error.  Trap it.
                    $this->KillQuery($FileName, $FileLine,
                            @mysql_error($this->SQLConnection));
            }

        } else {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#ReleaseRecordSet
     */
    function ReleaseRecordSet($FileName, $FileLine) {
        @mysql_free_result($this->SQLRecordSet);
    }


    /*
     * F I E L D      D E F I N I T I O N S      S E C T I O N
     */


    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldName
     */
    function FieldName($FileName, $FileLine, $FieldNumber) {
        $php_errormsg = '';

        if ($Output = @mysql_field_name($this->SQLRecordSet, $FieldNumber)) {
            return $Output;
        } else {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldType
     */
    function FieldType($FileName, $FileLine, $FieldNumber) {
        $php_errormsg = '';

        if ($Output = @mysql_field_type($this->SQLRecordSet, $FieldNumber)) {
            return $Output;
        } else {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#FieldLength
     */
    function FieldLength($FileName, $FileLine, $FieldNumber) {
        $php_errormsg = '';

        if ($Output = @mysql_field_len($this->SQLRecordSet, $FieldNumber)) {
            return $Output;
        } else {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }


    /*
     * R E C O R D      D A T A      S E C T I O N
     */


    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     * @param  array    $SkipSafeMarkup  an array of field names to not parse
     *                                   safe markup on
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordAsAssocArray
     */
    function RecordAsAssocArray($FileName, $FileLine, $SkipSafeMarkup = array()) {
        $php_errormsg = '';

        if ($Temp = @mysql_fetch_array($this->SQLRecordSet, MYSQL_ASSOC)) {

            foreach ($Temp as $Key => $Val) {
                if ($this->SQLEscapeHTML != 'N') {
                    $Val = htmlspecialchars($Val);
                }
                if ($this->SQLSafeMarkup == 'Y') {
                    if (!in_array($Key, $SkipSafeMarkup)) {
                        $Val = $this->ParseSafeMarkup($Val);
                    }
                }
                $Output[$Key] = $Val;
            }
            return $Output;
        } elseif ($php_errormsg != '') {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     * @param  array    $SkipSafeMarkup  an array of field numbers to not parse
     *                                   safe markup on
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#RecordAsEnumArray
     */
    function RecordAsEnumArray($FileName, $FileLine, $SkipSafeMarkup = array()) {
        $php_errormsg = '';

        if ($Temp = @mysql_fetch_array($this->SQLRecordSet, MYSQL_NUM)) {
            foreach ($Temp as $Key => $Val) {
                if ($this->SQLEscapeHTML != 'N') {
                    $Val = htmlspecialchars($Val);
                }
                if ($this->SQLSafeMarkup == 'Y') {
                    if (!in_array($Key, $SkipSafeMarkup)) {
                        $Val = $this->ParseSafeMarkup($Val);
                    }
                }
                $Output[] = $Val;
            }
            return $Output;
        } elseif ($php_errormsg != '') {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#InsertID
     */
    function InsertID($FileName, $FileLine, $Table = '', $Field = '', $Where = '',
                      $Sequence = '') {

        $php_errormsg = '';

        if ($Output = @mysql_insert_id($this->SQLConnection)) {
            return $Output;
        } elseif ($php_errormsg == '') {
            $this->KillQuery($FileName, $FileLine, 'No auto_increment id. '
                    . 'This query does not generate one or this table does '
                    . 'not have one.');
        } else {
            $this->KillQuery($FileName, $FileLine, $php_errormsg);
        }
    }

    /**
     *
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     *
     * @link   http://www.SqlSolution.info/sql-man.htm#GoToRecord
     */
    function GoToRecord($FileName, $FileLine, $Row = 0) {
        @mysql_data_seek($this->SQLRecordSet, $Row);
    }

    /**
     * Makes input safe for use as a value in queries
     *
     * Surrounds the string with quote marks.  If the value is NULL, change it
     * to the unquoted string "NULL".
     *
     * @param  string   $FileName  the file which called this method
     * @param  integer  $FileLine  the line number where this method was called
     * @param  mixed    $Value     the value to be escaped
     *
     * @return string  the escaped string
     */
    function Escape($FileName, $FileLine, $Value) {
        if (!$this->SQLConnection) {
            $this->Connect($FileName, $FileLine);
        }

        if ($Value === null) {
            return 'NULL';
        } else {
            return "'" . mysql_real_escape_string($Value, $this->SQLConnection) . "'";
        }
    }
}
