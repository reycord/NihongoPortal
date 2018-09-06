<?php
define("DATA_FETCH_BOTH", PGSQL_BOTH);
define("DATA_FETCH_NUMBER", PGSQL_NUM);
define("DATA_FETCH_ASSOC", PGSQL_ASSOC);

abstract class DbAgent
{
    protected $_122;
    abstract protected function __construct();
    static private $_210 = "gacoi.dbagent.conninfo";
    static private $_208 = "gacoi.dbagent.object";
    static public $DB_NUMBER = 1;
    static public $DB_TEXT = 2;
    static public $DB_DATE = 4;
    static public $DB_TIME = 5;
    static public $DB_DATETIME = 6;
    static public $DB_NUMBER_TIME = 7;
    static public $DB_BOOLEAN = 8;
    static public $DB_FILE = 9;
    static public $DB_STRING = 10;
    static public $DB_RICHTEXT = 11;
    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();
    public static function queryEncode($value, $type, $alltext = false) {
        if ($alltext) {
            if (isset($value) && $value != "") return "'" . str_replace("'", "''", $value) . "'";
            return "null";
        }
        switch ($type) {
            case DbAgent::$DB_BOOLEAN:
                if (isset($value) && !is_null($value) && ($value == 'true' || $value == 1)) return "true";
                return "false";
            case DbAgent::$DB_NUMBER:
                if (is_numeric($value)) return $value;
                return "null";
            case DbAgent::$DB_TIME:
                $value = DbAgent::convertHhmm2Time($value);
                if (isset($value)) return "'" . $value . "'";
                return "null";
            case DbAgent::$DB_NUMBER_TIME:
                return DbAgent::convertHhmm2Hour($value, 'null');
            case DbAgent::$DB_DATE:
            default:
                if (isset($value) && $value != "") return "'" . str_replace("'", "''", $value) . "'";
                return "null";
        }
    }
    static public function getInstanceFromSession() {
        if (isset($_SESSION[DbAgent::$_208]) && isset($_SESSION[DbAgent::$_210])) {
            $conninfo = $_SESSION[DbAgent::$_210];
            $dbinfo = $_SESSION[DbAgent::$_208];
            $db = unserialize(base64_decode($dbinfo));
            $db->open($conninfo[0], $conninfo[1], $conninfo[2], $conninfo[3], $conninfo[4]);
            return $db;
        }
        return null;
    }
    public function getConn() {
        return $this->_122;
    }
    public function setConn($_122) {
        $this->_122 = $_122;
    }
    public function open($host, $port, $database_name, $user, $pass) {
        $conninfo = array($host, $port, $database_name, $user, $pass);
        $_SESSION[DbAgent::$_210] = $conninfo;
        $_122 = $this->_open($host, $port, $database_name, $user, $pass);
        $_SESSION[DbAgent::$_208] = base64_encode(serialize($this));
        $this->setConn($_122);
    }
    abstract protected function _open($host, $port, $database_name, $user, $pass);
    abstract public function close();
    abstract public function getRecord($query, $fetch_type = DATA_FETCH_ASSOC);
    abstract public function getMultiRecord($query, &$total_row, $start_row = 0, $max_row = 0, $fetch_type = DATA_FETCH_ASSOC);
    public function getList($query) {
        $rs = $this->getMultiRecord($query, $t, 0, 0, DATA_FETCH_NUMBER);
        if (is_array($rs)) {
            $ret = array();
            foreach ($rs as $rec) {
                $ret[] = $rec[0];
            }
            return $ret;
        }
        return null;
    }
    public function getMap($query) {
        $rs = $this->getMultiRecord($query, $t, 0, 0, DATA_FETCH_NUMBER);
        if (is_array($rs)) {
            $ret = array();
            foreach ($rs as $rec) {
                $ret[$rec[0]] = $rec[1];
            }
            return $ret;
        }
        return null;
    }
    public function transform($query, $keyfield, $valuefield) {
        $rs = $this->getMultiRecord($query, $t);
        if (is_array($rs)) {
            $preret = array();
            foreach ($rs as $rec) {
                $newrec = array();
                foreach ($rec as $key => $value) {
                    if ($key != $keyfield && $key != $valuefield) {
                        $newrec[$key] = $value;
                    }
                }
                $pharsekey = implode($newrec);
                if (!isset($preret[$pharsekey])) {
                    $preret[$pharsekey] = $newrec;
                }
                $preret[$pharsekey][$rec[$keyfield]] = $rec[$valuefield];
            }
            $ret = array();
            foreach ($preret as $rec) {
                $ret[] = $rec;
            }
            return $ret;
        }
        return null;
    }
    public function getMapOfArray($query, $key) {
        $rs = $this->getMultiRecord($query, $t);
        if (is_array($rs)) {
            $ret = array();
            foreach ($rs as $rec) {
                $ret[$rec[$key]] = $rec;
            }
            return $ret;
        }
        return null;
    }
    abstract public function get($query, $def = null);
    abstract public function execute($query);
    public function throwQueryError($query) {
        throw new Exception("Query failed: " . pg_last_error() . "\n$query");
    }
    public function echoQueryError($query) {
        echo "<pre>$query</pre>\n";
    }
    abstract public function standardlize(&$data, $form);
    abstract public function applyQueryOrder($form);
    abstract public function applyQuerySearch($form);
    protected static function convertHhmm2Hour($t, $def = null) {
        if (is_numeric($t)) {
            return $t;
        }
        if (is_array($hm = explode(":", $t)) && count($hm) >= 2) {
            if (is_numeric($hm[0]) && is_numeric($hm[1])) {
                return $hm[0] + $hm[1] / 60.0;
            }
        }
        return $def;
    }
    protected static function convertHhmm2Time($t, $def = null) {
        if (!isset($t) || trim($t) == '') {
            return $def;
        }
        if (strlen($t) <= 5) {
            $t = $t . ':00';
        }
        $dt = strtotime(date('Y/m/d') . " $t");
        if (isset($dt) && $dt != '') {
            return date('H:i:s', $dt);
        } 
        else return $def;
    }
    public function echoComboBox($query, $def = null) {
        $rs = $this->getMultiRecord($query, $total, 0, 0, DATA_FETCH_NUMBER);
        echo "<option value=''>---</option>\n";
        foreach ($rs as $rec) {
            if ($def == $rec[0]) echo "<option selected value='" . $rec[0] . "'>" . $rec[1] . "</option>\n";
            else echo "<option value='" . $rec[0] . "'>" . $rec[1] . "</option>\n";
        }
    }
} ?>