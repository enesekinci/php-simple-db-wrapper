<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use PDO;
use Throwable;

class Database
{
    protected static ?self $_instance = null;
    protected $_pdo;
    protected $_query;
    protected $_error = false;
    protected $_result;
    protected $_count = 0;
    protected $_lastInsertId;
    protected $_fetchStyle = PDO::FETCH_OBJ;

    protected function __construct()
    {
        $this->host = "localhost";
        $this->dbname = "orm_test";
        $this->user = "root";
        $this->password = "";

        try {
            $this->_pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->user, $this->password);
            // set the PDO error mode to exception
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Throwable $th) {
            die($th->getMessage());
        }
    }

    public static function connect()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function query($sql, $params = [], $class = false)
    {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if ($params) {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            if ($this->_query->execute()) {
                if ($class &&  $this->_fetchStyle === PDO::FETCH_CLASS) {
                    $this->_result = $this->_query->fetchAll($this->_fetchStyle, $class);
                } else {
                    $this->_result = $this->_query->fetchAll($this->_fetchStyle);
                }
                $this->_count = $this->_query->rowCount();
                $this->_lastInsertId = $this->_pdo->lastInsertId();
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }

    public function _read($table, $params = [], $class = false)
    {
        $columns = "*";
        $joins = "";
        $conditionString = "";
        $bind = [];
        $order = "";
        $limit = "";
        $offset = "";

        //Fetching Style

        if (isset($params['fetchStyle'])) {
            $this->_fetchStyle = $params['fetchStyle'];
        }

        //Conditions
        if (isset($params['conditions'])) {
            if (is_array($params['conditions'])) {
                foreach ($params['conditions'] as $condition) {
                    $conditionString .= " " . $condition . " AND";
                }
                $conditionString = trim($conditionString);
                $conditionString = rtrim($conditionString, "AND");
            } else {
                $conditionString = $params['conditions'];
            }

            if ($conditionString != "") {
                $conditionString = " WHERE " . $conditionString;
            }
        }

        //Columns
        if (array_key_exists('columns', $params)) {
            $columns = $params['columns'];
        }
        //Joins
        if (array_key_exists('joins', $params)) {
            foreach ($params['joins'] as $join) {
                $joins .= $this->_buildJoin($join);
            }
            $joins .= " ";
        }
        //Binding
        if (array_key_exists('bind', $params)) {
            $bind = $params['bind'];
        }
        //Order
        if (array_key_exists('order', $params)) {
            $order = " ORDER BY " . $params['order'];
        }
        //Limit
        if (array_key_exists('limit', $params)) {
            $limit = " LIMIT " . $params['limit'];
        }
        //Offset
        if (array_key_exists('offset', $params)) {
            $offset = " OFFSET " . $params['offset'];
        }

        $sql = "SELECT {$columns} FROM {$table} {$joins} {$conditionString} {$order} {$limit} {$offset}";
        if ($this->query($sql, $bind, $class)) {
            if (count($this->_result))
                return true;
            return false;
        }
        return false;
        // echo $sql;
    }

    public function find($table, $params = [], $class = false)
    {
        if ($this->_read($table, $params, $class)) {
            return $this->results();
        }
        return false;
    }

    public function findFirst($table, $params = [], $class = false)
    {
        if ($this->_read($table, $params, $class)) {
            return $this->first();
        }
        return false;
    }

    public function insert($table, $fields = [])
    {
        $fieldString = "";
        $valueString = "";
        $values = [];
        foreach ($fields as $field => $value) {
            $fieldString .= '`' . $field . '`,';
            $valueString .= ' ?';
            $values[] = $value;
        }
        $fieldString = rtrim($fieldString, ',');
        $valueString = rtrim($valueString, ',');
        $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";
        if ($this->query($sql)->error()) {
            return true;
        }
        return false;
    }

    public function update($table, $id, $fields = [])
    {
        # code...
    }

    public function delete($table, $id)
    {
    }

    public function results()
    {
        # code...
    }

    public function first()
    {
        # code...
    }

    public function count()
    {
        # code...
    }

    public function lastInsertId()
    {
        # code...
    }

    public function getColumns($table)
    {
    }

    public function error()
    {
        # code...
    }



    protected function _buildJoin($join = [])
    {
        $table = $join[0];
        $condition = $join[1];
        $alias = $join[2];
        $type = (isset($join[3])) ? strtoupper($join[3]) : ' INNER ';
        $jString = " {$type} JOIN {$table} {$alias} ON {$condition}";
        return $jString;
    }
}
