<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use PDO;
use stdClass;
use Throwable;
use EnesEkinci\PhpSimpleDBWrapper\Collect\Collect;

final class QueryBuilder
{
    protected $pdo;
    protected $query;
    protected $table;
    protected $select = '*';
    protected $count;
    protected $max = [];
    protected $min = [];
    protected $avg;
    protected $sum;
    protected $error = false;
    protected $fetchStyle = PDO::FETCH_OBJ;
    protected $lastInsertId;
    protected $limit;
    protected $offset;
    protected $where = [];
    protected $orderBy = [];
    protected $joins = [];
    protected $result = null;
    protected $class = null;

    public function __construct()
    {
        $this->host = "localhost";
        $this->dbname = "orm_test";
        $this->user = "root";
        $this->password = "";

        try {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->user, $this->password);
            // set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Throwable $th) {
            die($th->getMessage());
        }
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function orderBy($column, $sort = "ASC")
    {
        $this->orderBy[] = [$column, $sort];
        return $this;
    }

    public function take(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function skip(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }

    public function result()
    {
        return $this->result;
    }

    public function getColumns($table)
    {
        return $this->query("SHOW COLUMNS FROM {$table}")->result();
    }

    public function query($sql, $params = [], $class = false)
    {
        if (!$class) {
            $class = $this->class;
        }

        $this->error = false;

        dd($sql);

        $this->query = $this->pdo->prepare($sql);

        if (!$this->query) {
            return $this;
        }
        if ($params) {
            foreach (array_values($params) as $key => $param) {
                $this->query->bindValue($key + 1, $param);
            }
        }

        $result = $this->query->execute();

        if (!is_bool($result) || $result !== true) {
            $this->error = true;
            dd("result error", $result, $this->error);
        }

        if ($result) {
            if ($class &&  $this->fetchStyle === PDO::FETCH_CLASS) {
                $this->result = new Collect($this->query->fetchAll($this->fetchStyle, $class));
            } else {
                $this->result = new Collect($this->query->fetchAll($this->fetchStyle));
            }
            $this->count = $this->query->rowCount();
            $this->lastInsertId = $this->pdo->lastInsertId();
        } else {
            $this->error = true;
        }
        return $this;
    }

    public function get($class = false)
    {
        if (!$class) {
            $class = $this->class;
        }

        $SQL = QueryGenerator::select($this->table, $this->select, $this->limit, $this->offset, $this->where, $this->orderBy, [], [], $this->groupBy);
        $this->query($SQL, [], $class)
            ->restartParams();
        return $this->result();
    }

    public function insert(array $fields, $class = false)
    {
        if (!$class) {
            $class = $this->class;
        }

        $SQL = QueryGenerator::insert($this->table, $fields);
        $this->query($SQL, $fields, $class);
        $this->restartParams();
        return $this->error();
    }

    public function update(array $fields, $class = false)
    {
        if (!$class) {
            $class = $this->class;
        }

        $SQL = QueryGenerator::update($this->table, $fields, $this->where);
        $this->query($SQL, $fields, $class);
        $this->restartParams();
        return $this->error();
    }

    public function delete()
    {
        $SQL = QueryGenerator::delete($this->table, $this->where);
        $this->query($SQL, [], false);
        $this->restartParams();
        return $this->error();
    }

    public function count()
    {
        return $this->count;
    }

    public function error()
    {
        return !$this->error;
    }

    public function first()
    {
        $result = $this->result();
        return (!empty($result)) ? $result[0] : false;
    }

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }


    public function chunck(int $length, bool $preserve_keys = false)
    {
        return array_chunk($this->result, $length, $preserve_keys);
    }

    protected function addCondition(array $where)
    {
        $this->where[] = $where;
    }

    public function where(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $this->where[] = $condition;
            } else {
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereNot(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['NOT'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['NOT'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function orWhere(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['OR'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['OR'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereNull(string $column)
    {
        $condition = [$column, 'NULL' => true];
        $this->where[] = $condition;
        return $this;
    }

    public function whereNotNull(string $column)
    {
        $condition = [$column, 'NOT_NULL' => true];
        $this->where[] = $condition;
        return $this;
    }

    public function whereIn(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['IN'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['IN'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereNotIn(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['NOT_IN'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['NOT_IN'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereBetween(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['BETWEEN'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['BETWEEN'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereNotBetween(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['NOT_BETWEEN'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['NOT_BETWEEN'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereLike(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['LIKE'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['LIKE'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function whereNotLike(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $condition['NOT_LIKE'] = true;
                $this->where[] = $condition;
            } else {
                $conditions['NOT_LIKE'] = true;
                $this->where[] = $conditions;
                break;
            }
        }
        return $this;
    }

    public function each(callable $callback)
    {
        return array_map($callback, $this->result());
    }

    public function max(string $column, ?string $as = null)
    {
        /**
         * @todo 
         * Buradaki result dönüşü QueryModel olarak dönüyor buna çeki düzen verilecek.
         */
        $this->max = ['column' => $column, 'as' => $as ?? $column];
        $SQL = QueryGenerator::select($this->table, $this->select, $this->limit, $this->offset, $this->where, $this->orderBy, $this->max);
        $this->query($SQL, [])
            ->restartParams();
        return $this->first()->{$column} ?? null;
    }

    public function min(string $column, ?string $as = null)
    {
        /**
         * @todo 
         * Buradaki result dönüşü QueryModel olarak dönüyor buna çeki düzen verilecek.
         */
        $this->min = ['column' => $column, 'as' => $as ?? $column];
        $SQL = QueryGenerator::select($this->table, $this->select, $this->limit, $this->offset, $this->where, $this->orderBy, $this->max, $this->min);
        $this->query($SQL, [])
            ->restartParams();
        return $this->first()->{$column} ?? null;
    }

    public function avg(array $column, ?string $as)
    {
        $this->avg = ['column' => $column, 'as' => $as];
        return $this;
    }

    public function sum(array $column, ?string $as)
    {
        $this->sum = ['column' => $column, 'as' => $as];
        return $this;
    }

    public function groupBy(string $column)
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function join(string $table, string $column, string $relationColumn, string $joinType)
    {
        $this->joins[] = [
            'type' => $joinType,
            'table' => $table,
            'column' => $column,
            'relationColumn' => $relationColumn,
        ];
        return $this;
    }

    public function whereJsonContains()
    {
        #
    }

    public function setFetchStyle($fetchStyle)
    {
        $this->fetchStyle = $fetchStyle;
        return $this;
    }

    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    protected function restartParams(): void
    {
        // $this->table = null;
        // $this->query = null;
        $this->select = '*';
        // $this->error = false;
        // $this->fetchStyle = PDO::FETCH_OBJ;
        $this->limit = null;
        $this->offset = null;
        $this->where = [];
        $this->orderBy = [];
    }

    /*
    public function __set($key, $value)
    {
        if ($key === 'result') {
            $this->result = is_array($value) ? new Collect($value) : $value;
        } else {
            $this->{$key} = $value;
        }
    }
    */
}
