<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

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

    public function getColumns()
    {
        return static::getDB()->getColumns(static::$_table);
    }

    public function onConstruct()
    {
        # code...
    }
}
