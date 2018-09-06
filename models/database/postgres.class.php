<?php
include_once 'dbagent.class.php';
class PostgresDb extends DbAgent
{
    public function __construct() {
    }
    private $_204 = 0;
    public function begin() {
        if ($this->_204 != 0) {
            return;
        }
        $this->execute("BEGIN;");
        $this->_204 = 1;
    }
    public function commit() {
        if ($this->_204 != 1) {
            return;
        }
        $this->execute("COMMIT;");
        $this->_204 = 0;
    }
    public function rollback() {
        if ($this->_204 != 1) {
            return;
        }
        $this->execute("ROLLBACK;");
        $this->_204 = 0;
    }
    public function _open($host, $port, $database_name, $user, $pass) {
        $connstr = "host=$host port=$port dbname=$database_name user=$user password=$pass";
        $this->_122 = pg_connect($connstr);
        return $this->_122;
    }
    public function close() {
        pg_close($this->_122);
    }
    public function getRecord($query, $fetch_type = PGSQL_ASSOC) {
        $result = pg_query($this->_122, $query) or $this->throwQueryError($query);
        if ($rs = pg_fetch_array($result, null, $fetch_type)) {
            $ret = $rs;
        } 
        else $ret = NULL;
        pg_free_result($result);
        return $ret;
    }
    public function getMultiRecord($query, &$total_row, $start_row = 0, $max_row = 0, $fetch_type = PGSQL_ASSOC) {
        try {
            $result = pg_query($this->_122, $query) or $this->throwQueryError($query);
        }
        catch(Exception $e) {
            throw $e;
        }
        $total_row = pg_num_rows($result);
        if ($start_row >= $total_row) {
            $start_row = 0;
        }
        if ($start_row > 0) {
            $a = pg_fetch_row($result, $start_row);
        }
        $ret = array();
        $index = 0;
        while ($rs = pg_fetch_array($result, null, $fetch_type)) {
            $ret[] = $rs;
            $index++;
            if ($max_row > 0 && $max_row <= $index) {
                break;
            }
        }
        pg_free_result($result);
        return $ret;
    }
    public function get($query, $def = NULL) {
        $result = pg_query($this->_122, $query) or $this->throwQueryError($query);
        if ($rs = pg_fetch_row($result)) {
            if (is_null($rs[0])) $ret = $def;
            else $ret = $rs[0];
        } 
        else {
            $ret = $def;
        }
        pg_free_result($result);
        return $ret;
    }
    public function execute($query) {
        $result = @pg_query($this->_122, $query);
        if (!$result) {
            //TODO LOG
            //$this->echoQueryError($query);
            throw new Exception(pg_last_error($this->_122));
        }
        $ret = pg_affected_rows($result);
        pg_free_result($result);
        return $ret;
    }
    public function standardlize(&$data, $form) {
        if (!is_array($data)) {
            return;
        }
        $fields = $form->getFields();
        $fieldtypes = array();
        foreach ($fields as $field) {
            $type = $form->getType($field);
            if ($type == DbAgent::$DB_BOOLEAN || $type == DbAgent::$DB_DATE || $type == DbAgent::$DB_DATETIME) {
                $fieldtypes[$field] = $type;
            } 
            else if ($form->isComboBoxMultiple($field)) {
                $fieldtypes[$field] = $type;
            }
        }
        foreach ($data as $key => & $record) {
            if (!is_array($record)) {
                if (isset($fieldtypes[$key])) $record = $this->_70($record, $fieldtypes[$key]);
            } 
            else {
                foreach ($fieldtypes as $field => $type) {
                    if ($form->isComboBoxMultiple($field)) {
                        $record[$field] = explode(",", $record[$field]);
                    } 
                    else {
                        if (isset($record[$field])) {
                            $record[$field] = $this->_70($record[$field], $type);
                        }
                    }
                }
            }
        }
    }
    private function _70($data, $type) {
        switch ($type) {
            case DbAgent::$DB_BOOLEAN:
                if ($data == 't' || $data == 'true') return true;
                if ($data == 'f' || $data == 'false') return false;
                else return null;
                break;

            default:
                return str_replace('-', '/', $data);
                break;
            }
        }
        public function applyQueryOrder($form) {
            $orderinfo = $form->getOrderInfo();
            if (isset($orderinfo) && is_array($orderinfo) && count($orderinfo) > 0) {
                $query = '';
                foreach ($orderinfo as $field => $order) {
                    $query = $query . ', ' . $form->getDatabaseCol($field);
                    if ($order == MultiForm::$ORDER_DESC) {
                        $query = "$query DESC";
                    }
                }
                $form->setQueryOrderby(substr($query, 2));
            }
        }
        public function applyQuerySearch($form) {
            $si = $form->getSearchInfo();
            if (!isset($si)) return;
            $fields = $si->getSearchFields();
            $where = '';
            $having = '';
            foreach ($fields as $field) {
                $alltext = $form->isTextDataField($field);
                $type = $form->getType($field);
                $op = $si->getSearchOp($field);
                $dbcol = $form->getDatabaseCol($field);
                if (!isset($op) || $op == '') continue;
                if ($si->isSearchRange($field)) {
                    $value1 = $si->getSearchCond1($field);
                    $value2 = $si->getSearchCond2($field);
                    if ($value1 == "" && $value2 == "") {
                        $query = $dbcol . ' is null';
                    } 
                    else {
                        $query = '';
                        if ($value1 != "") {
                            $query = $query . ' and ' . $dbcol . '>=' . $this->queryEncode($value1, $type, $alltext);
                        }
                        if ($value2 != "") {
                            $query = $query . ' and ' . $dbcol . '<=' . $this->queryEncode($value2, $type, $alltext);
                        }
                        $query = substr($query, 5);
                    }
                } 
                else {
                    $value = $si->getSearchCond($field);
                    if (is_array($value)) {
                        $incond = "";
                        foreach ($value as $tmp) {
                            $incond = "$incond, " . $this->queryEncode($tmp, $type, $alltext);
                        }
                        $incond = substr($incond, 2);
                        $query = "$dbcol in($incond)";
                    } 
                    else {
                        if (!isset($value) || $value == "") {
                            $query = $dbcol . ' is null';
                        } 
                        else {
                            $likecmd = !$form->isComboField($field);
                            $likecmd = $likecmd && !($type == DbAgent::$DB_NUMBER || $type == DbAgent::$DB_BOOLEAN);
                            if ($likecmd) {
                                $query = $dbcol . ' like ' . $this->queryEncode($value, $type, $alltext);
                            } 
                            else {
                                $query = $dbcol . '=' . $this->queryEncode($value, $type, $alltext);
                            }
                        }
                    }
                }
                if ($op == SearchInfo::$DIFFERENT) {
                    $query = "not($query)";
                }
                if ($form->isGroupbyFields($field)) {
                    $having = $having . "\n and " . $query;
                } 
                else {
                    $where = $where . "\n and " . $query;
                }
            }
            if ($where != '') {
                $origin = $form->getQueryWhere();
                if (isset($origin) || $origin != '') {
                    $form->setQueryWhere("($origin) $where");
                } 
                else {
                    $form->setQueryWhere(substr($where, 6));
                }
            }
            if ($having != '') {
                $origin = $form->getQueryHaving();
                if (isset($origin) || $origin != '') {
                    $form->setQueryHaving("($origin) $having");
                } 
                else {
                    $form->setQueryHaving(substr($having, 6));
                }
            }
        }
    } ?>