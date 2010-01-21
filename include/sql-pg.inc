<?php

/**
 * SQL Solution's PostgreSQL specific code.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2009
 * @version    $Name:  $ $Id: sql-pg.inc,v 5.12 2009-12-28 16:29:12 danielc Exp $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see        sql-common.inc
 */

/**
 * SQL Solution's PostgreSQL specific methods.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2009
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_PostgreSQLSpecifics extends SQLSolution_Customizations {

    /**
     *
     * @var  integer
     */
    var $SQLPgRow = 0;

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

        $Connect = '';
        if ($this->SQLHost) {
            $Connect .= 'host=' . $this->SQLHost;
        }
        if ($this->SQLPort) {
            $Connect .= ' port=' . $this->SQLPort;
        }
        if ($this->SQLDbName) {
            $Connect .= ' dbname=\'' . addslashes($this->SQLDbName) . '\'';
        }
        if ($this->SQLUser) {
            $Connect .= ' user=\'' . addslashes($this->SQLUser) . '\'';
        }
        if ($this->SQLPassword) {
            $Connect .= ' password=\'' . addslashes($this->SQLPassword) . '\'';
        }
        if ($this->SQLOptions) {
            $Connect .= ' options=' . $this->SQLOptions;
        }
        if ($this->SQLTTY) {
            $Connect .= ' tty=' . $this->SQLTTY;
        }
        $this->SQLConnection = @pg_connect($Connect)
                               or die ($this->KillQuery($FileName, $FileLine,
                                                        $php_errormsg));
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
        $Connect = '';
        if ($this->SQLHost) {
            $Connect .= 'host=' . $this->SQLHost;
        }
        if ($this->SQLPort) {
            $Connect .= ' port=' . $this->SQLPort;
        }
        if ($this->SQLDbName) {
            $Connect .= ' dbname=\'' . addslashes($this->SQLDbName) . '\'';
        }
        if ($this->SQLUser) {
            $Connect .= ' user=\'' . addslashes($this->SQLUser) . '\'';
        }
        if ($this->SQLPassword) {
            $Connect .= ' password=\'' . addslashes($this->SQLPassword) . '\'';
        }
        if ($this->SQLOptions) {
            $Connect .= ' options=' . $this->SQLOptions;
        }
        if ($this->SQLTTY) {
            $Connect .= ' tty=' . $this->SQLTTY;
        }
        $this->SQLConnection = @pg_pconnect($Connect)
                               or die ($this->KillQuery($FileName, $FileLine,
                                                        $php_errormsg));
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
        $this->Connect($FileName, $FileLine);
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
        @pg_close($this->SQLConnection);
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
        if (!$this->SQLConnection) {
            $this->Connect($FileName, $FileLine);
        }

        $php_errormsg = '';
        $this->SQLPgRow = 0;

        if ($this->SQLRecordSet = @pg_exec($this->SQLConnection, $this->SQLQueryString)) {
            if (!$this->SQLRecordSetFieldCount = @pg_numfields($this->SQLRecordSet)) {
                $this->SQLRecordSetFieldCount = 0;
            }
            if (!$this->SQLRecordSetRowCount = @pg_numrows($this->SQLRecordSet)) {
                $this->SQLRecordSetRowCount = 0;
            }

        } elseif ($php_errormsg == '') {
            // Probably a database error.
            $this->KillQuery($FileName, $FileLine,
                             @pg_errormessage($this->SQLConnection));
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
        if (!$this->SQLConnection) {
            $this->Connect($FileName, $FileLine);
        }

        $php_errormsg = '';
        $this->SQLPgRow = 0;

        if ($this->SQLRecordSet = @pg_exec($this->SQLConnection, $this->SQLQueryString)) {
            $this->SQLRecordSetFieldCount = 0;
            $this->SQLRecordSetRowCount = 0;
            return 1;
        } else {
            $Msg = @pg_errormessage($this->SQLConnection);
            if (preg_match('/violates unique constraint/', $Msg)) {
                // Couldn't insert/update record due to duplicate key.
            } else {
                $this->KillQuery($FileName, $FileLine, $Msg);
            }
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
        @pg_freeresult($this->SQLRecordSet);
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

        if ($Output = @pg_fieldname($this->SQLRecordSet, $FieldNumber)) {
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

        if ($Output = @pg_fieldtype($this->SQLRecordSet, $FieldNumber)) {
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

        if ($Output = @pg_fieldsize($this->SQLRecordSet, $FieldNumber)) {
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
        if ($this->SQLPgRow < $this->SQLRecordSetRowCount) {

            $php_errormsg = '';

            if ($Temp = @pg_fetch_array($this->SQLRecordSet, $this->SQLPgRow,
                                        PGSQL_ASSOC))
            {
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
                $this->SQLPgRow++;
                return $Output;
            } elseif ($php_errormsg != '') {
                $this->KillQuery($FileName, $FileLine, $php_errormsg);
            }
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
        if ($this->SQLPgRow < $this->SQLRecordSetRowCount) {
            $php_errormsg = '';

            if ($Temp = @pg_fetch_array($this->SQLRecordSet, $this->SQLPgRow,
                                        PGSQL_NUM))
            {
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
                $this->SQLPgRow++;
                return $Output;
            } elseif ($php_errormsg != '') {
                $this->KillQuery($FileName, $FileLine, $php_errormsg);
            }
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
        $this->SQLQueryString = "SELECT CURRVAL('$Sequence')";
        $this->RunQuery(__FILE__, __LINE__);
        list($Val) = $this->RecordAsEnumArray(__FILE__, __LINE__);
        return $Val;
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
        $this->SQLPgRow = $Row;
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
            return "'" . pg_escape_string($this->SQLConnection, $Value) . "'";
        }
    }
}
