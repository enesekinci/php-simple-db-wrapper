<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use PDO;

class Model
{
    protected $_modelName;
    protected $_validates = true;
    protected $_validationErrors = [];
    public $id;
    protected static $_db;
    protected static $_table;
    protected static $_softDelete = false;

    public function __construct()
    {
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', '', static::$_table)));
        $this->onConstruct();
    }

    public static function getDB()
    {
        if (!self::$_db) {
            self::$_db = Database::connect();
        }
        return self::$_db;
    }

    public static function getColumns()
    {
        return static::getDB()->getColumns(static::$_table);
    }

    public function getColumnsForSave()
    {
        $columns = static::getColumns();
        $fields = [];

        foreach ($columns as $column) {
            $key = $column->Field;
            $fields[$key] = $this->{$key};
        }
        return $fields;
    }

    protected static function _softDeleteParams($params)
    {
        if (static::$_softDelete) {
            if (array_key_exists('conditions', $params)) {
                if (is_array($params['conditions'])) {
                    $params['conditions'][] = "deleted != 1";
                } else {
                    $params['conditions'] = " AND deleted != 1";
                }
            } else {
                $params['conditions'] = "deleted != 1";
            }
        }
        return $params;
    }

    protected static function _fetchStyleParams($params)
    {
        if (!isset($params['fetchStyle'])) {
            $params['fetchStyle'] = PDO::FETCH_CLASS;
        }
        return $params;
    }

    public static function find($params = [])
    {
        $params = static::_fetchStyleParams($params);
        $params = static::_softDeleteParams($params);

        $resultsQuery = static::getDB()->find(static::$_table, $params, static::class);

        if (!$resultsQuery)
            return [];
        return $resultsQuery;
    }

    public static function findFirst($params = [])
    {
        $params = static::_fetchStyleParams($params);
        $params = static::_softDeleteParams($params);

        $resultsQuery = static::getDB()->findFirst(static::$_table, $params, static::class);

        if (!$resultsQuery)
            return [];
        return $resultsQuery;
    }

    public static function findById(int $id)
    {
        return static::findFirst(['conditions' => 'id = ?', 'bind' => [$id]]);
    }

    public function save()
    {
        $this->validator();
        $save = false;
        if ($this->_validates) {
            $this->beforeSave();
            $fields = $this->getColumnsForSave();
            if ($this->isNew()) {
                $save = $this->insert($fields);
                if ($save) {
                    $this->id = static::getDB()->lastInsertId();
                }
            }
        }
    }

    public function insert($fields)
    {
        # code...
    }

    public function isNew()
    {
        # code...
    }

    public function beforeSave()
    {
    }

    public function validator()
    {
    }

    public function onConstruct()
    {
        # code...
    }
}
