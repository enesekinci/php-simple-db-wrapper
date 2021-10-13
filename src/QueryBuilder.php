<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use PDO;
use Throwable;

final class QueryBuilder
{
    protected $_pdo;
    protected $_query;
    protected $_table;
    protected $_select = '*';
    protected $_count;
    protected $_error = false;
    protected $_fetchStyle = PDO::FETCH_OBJ;
    protected $_lastInsertId;
    protected $_limit;
    protected $_offset;
    protected $_where = [];
    protected $_orderBy = [];
    protected $_result = null;

    public function __construct()
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

    public function table($table)
    {
        $this->_table = $table;
        return $this;
    }

    public function orderBy($column, $sort = "ASC")
    {
        $this->_orderBy[] = [$column, $sort];
        return $this;
    }

    public function take(int $limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    public function skip(int $offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    public function select($columns = '*')
    {
        $this->_select = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }

    public function result()
    {
        return $this->_result;
    }

    public function getColumns($table)
    {
        return $this->query("SHOW COLUMNS FROM {$table}")->result();
    }

    public function query($sql, $params = [], $class = false)
    {
        $this->_error = false;

        $this->_query = $this->_pdo->prepare($sql);

        if (!$this->_query) {
            return $this;
        }
        if ($params) {
            foreach (array_values($params) as $key => $param) {
                $this->_query->bindValue($key + 1, $param);
            }
        }

        $result = $this->_query->execute();

        if (!is_bool($result) || $result !== true) {
            $this->_error = true;
            dd("result error", $result, $this->_error);
        }

        if ($result) {
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

        return $this;
    }

    public function get($class = false)
    {
        $SQL = QueryGenerator::select($this->_table, $this->_select, $this->_limit, $this->_offset, $this->_where, $this->_orderBy);
        $this->query($SQL, [], $class);
        $this->restartParams();
        return $this->result();
    }

    public function insert(array $fields, $class = false)
    {
        $SQL = QueryGenerator::insert($this->_table, $fields);
        $this->query($SQL, $fields, $class);
        $this->restartParams();
        return $this->result();
    }

    public function update(array $fields, $class = false)
    {
        $SQL = QueryGenerator::update($this->_table, $fields, $this->_where);
        $this->query($SQL, $fields, $class);
        $this->restartParams();
        return $this->error();
    }

    public function delete()
    {
        $SQL = QueryGenerator::delete($this->_table, $this->_where);
        $this->query($SQL, [], false);
        $this->restartParams();
        return $this->error();
    }

    public function count()
    {
        return $this->_count;
    }

    public function error()
    {
        return !$this->_error;
    }

    public function first()
    {
        $result = $this->result();
        return (!empty($result)) ? $result[0] : false;
    }

    public function lastInsertId()
    {
        return $this->_lastInsertId;
    }


    public function chunck(int $length, bool $preserve_keys = false)
    {
        return array_chunk($this->_result, $length, $preserve_keys);
    }

    protected function addCondition(array $where)
    {
        $this->_where[] = $where;
    }


    public function initial()
    {
        #
    }

    public function find()
    {
        #
    }

    public function findById()
    {
        #
    }

    public function where(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $this->_where[] = $condition;
            } else {
                $this->_where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function orWhere(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['or'] = true;
                $this->_where[] = $condition;
            } else {
                $conditions['or'] = true;
                $this->_where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function pluck()
    {
    }

    public function value()
    {
    }



    public function max()
    {
    }

    public function avg()
    {
    }

    public function distinct()
    {
    }

    public function groupBy()
    {
    }

    public function join()
    {
    }

    public function leftJoin()
    {
    }

    public function rightJoin()
    {
    }

    public function crossJoin()
    {
    }

    public function whereNull()
    {
    }

    public function union()
    {
    }

    public function whereJsonContains()
    {
    }

    public function whereBetween()
    {
    }

    public function whereNotBetween()
    {
    }

    public function whereIn()
    {
    }

    public function whereNotIn()
    {
    }

    public function whereNotNull()
    {
    }

    public function whereDate()
    {
    }

    public function setFetchStyle($fetchStyle)
    {
        $this->_fetchStyle = $fetchStyle;
    }

    protected function restartParams(): void
    {
        $this->_table = null;
        // $this->_query = null;
        $this->_select = '*';
        // $this->_error = false;
        // $this->_fetchStyle = PDO::FETCH_OBJ;
        $this->_limit = null;
        $this->_offset = null;
        $this->_where = [];
        $this->_orderBy = [];
    }
}
