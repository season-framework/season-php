<?php
namespace framework\interfaces;

class Model extends Base {

    protected $pdo;
    protected $tablename;

    private function _tableinfo() {
        $table = $this->tablename;
        $stmt = $this->pdo->prepare("DESC $table");
        if( ! $stmt->execute() ) return null;
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = new \stdClass;
        $result->pk = array();
        $result->columns = array();
        
        foreach ( $columns as $col ) {
            if ( $col["Key"] == "PRI" ) {
                $result->pk[] = $col["Field"];
            }
            $result->columns[] = $col["Field"];
        }
        return $result;
    }

    public function fields($table) {
        $stmt = $this->pdo->prepare("DESC $table");
        if( ! $stmt->execute() ) return null;
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = array();
        foreach ( $columns as $col ) {
            $result[strtolower($col["Field"])] = $col;
        }
        return $result;
    }

    public function build_update($values, $where = null) {
        $pdo = $this->pdo;
        $table = $this->tablename;

        $tableinfo = $this->_tableinfo();

        $update = array();
        $_values = array();
        foreach ( $values as $field => $v ) {
            if ( ! in_array( $field, $tableinfo->columns ) ) continue;
            $update[] = "`$field`=:$field";
            $_values[$field] = $v;
        }
        $values = $_values;

        $result = new \stdClass;
        $result->set = implode(',', $update);
        $result->bind = array();
        foreach ( $values as $f => $v ) {
            $result->bind[":$f"] = $v;
        }
        return $result;
    }

    public function build_insert($values) {
        $pdo = $this->pdo;
        $table = $this->tablename;

        $tableinfo = $this->_tableinfo();

        $insert = array();
        $keys = array();
        $_values = array();
        foreach ( $values as $field => $v ) {
            if ( ! in_array( $field, $tableinfo->columns ) ) continue;
            $insert[] = ":$field";
            $keys[] = "`$field`";
            $_values[$field] = $v;
        }
        $values = $_values;

        $result = new \stdClass;
        $result->fields = implode(',', $keys);
        $result->values = implode(',', $insert);
        $result->bind = array();
        foreach ( $values as $f => $v ) {
            $result->bind[":$f"] = $v;
        }
        return $result;
    }

}
