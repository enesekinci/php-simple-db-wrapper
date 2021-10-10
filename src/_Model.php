<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use DateTime;
use DateTimeZone;
use PDO;
use stdClass;

class _Model
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
            } else {
                $save = $this->update($fields);
            }
            if ($save) {
                $this->afterSave();
            }
        }
        return $save;
    }

    public function insert($fields)
    {
        if (empty($fields))
            return false;
        if (array_key_exists('id', $fields))
            unset($fields['id']);
        return static::getDB()->insert(static::$_table, $fields);
    }

    public function update($fields)
    {
        if (empty($fields) || $this->id == '')
            return false;
        return static::getDB()->update(static::$_table, $this->id, $fields);
    }

    public function delete($fields)
    {
        if (empty($fields) || $this->id == '')
            return false;
        $this->beforeDelete();
        if (static::$_softDelete) {
            $deleted = $this->update(['deleted' => '1']);
        } else {
            $deleted = static::getDB()->delete(static::$_table, $this->id);
        }
        $this->afterDelete();
        return $deleted;
    }

    public function query($sql, $bind = [])
    {
        return static::getDB()->query($sql, $bind);
    }

    public function data()
    {
        $data = new stdClass();
        foreach (static::getColumns() as $column) {
            $columnName = $column->Field;
            $data->{$columnName} = $this->{$columnName};
        }
        return $data;
    }

    public function assign($params, $list = [], $blackList = true)
    {
        foreach ($params as $key => $value) {
            $whiteListed = true;
            if (sizeof($list) > 0) {
                if ($blackList) {
                    $whiteListed = !in_array($key, $list);
                } else {
                    $whiteListed = in_array($key, $list);
                }
            }
            if (property_exists($this, $key) && $whiteListed) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function runValidation($validator)
    {
        $key = $validator->field;
        if ($validator->success) {
            $this->addErrorMessage($key, $validator->msg);
        }
    }

    public function getErrorMessages()
    {
        return $this->_validationErrors;
    }

    public function validationPassed()
    {
        return $this->_validates;
    }

    public function addErrorMessage($field, $message)
    {
        $this->_validates = false;
        if (array_key_exists($field, $this->_validationErrors)) {
            $this->_validationErrors[$field] .= ' ' . $message;
        } else {
            $this->_validationErrors[$field] = $message;
        }
    }

    public function timeStamps()
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        $now = $dt->format('Y-m-d H:i:s');
        $this->update_at = $now;
        if ($this->isNew()) {
            $this->created_at = $now;
        }
    }

    public function isNew()
    {
        return property_exists($this, 'id') && !empty($this->id);
    }

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }

    public function validator()
    {
    }

    public function onConstruct()
    {
    }

    public function beforeDelete()
    {
    }

    public function afterDelete()
    {
    }
}
