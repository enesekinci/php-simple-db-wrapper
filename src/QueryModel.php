<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use BadMethodCallException;
use EnesEkinci\PhpSimpleDBWrapper\Exception\JsonEncodingException;
use PDO;

abstract class QueryModel
{
    protected static $database = null;
    protected $connection;
    protected static $table;
    protected $with = [];
    protected $withCount = [];
    protected $original;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function __construct()
    {
    }

    protected static function database()
    {
        if (is_null(static::$database)) {
            static::$database = new QueryBuilder();
            static::$database->table(static::table());
            static::$database->setFetchStyle(PDO::FETCH_CLASS);
            static::$database->setClass(static::className());
        }
        return static::$database;
    }

    protected static function className()
    {
        return get_called_class();
    }

    public static function table()
    {
        return static::$table;
    }

    public static function get()
    {
        return static::database()->get(static::className());
    }

    public function update(array $fields)
    {
        $this->syncOriginal();
        return static::database()->update($fields, static::className());
    }

    public static function updateInDatabase(array $fields)
    {
        return static::database()->update($fields, static::className());
    }

    public static function insert(array $fields)
    {
        // dd(static::database()->insert($fields, static::className()), static::database()->lastInsertId());
        return static::database()->insert($fields, static::className());
    }

    public static function create(array $fields)
    {
        $result = static::database()->insert($fields, static::className());
        if (!$result) {
            return false;
        }
        $lastInsertId = static::database()->lastInsertId();
    }

    public static function findById(int $id)
    {
        static::database()->where(['id', $id])->take(1)->get(static::className());
        $result = static::database()->first();
        return $result;
    }

    public function toArray()
    {
        return $this->getAttributes();
    }

    public function getAttributes()
    {
        $properties = get_object_vars($this);
        unset($properties['database'], $properties['connection'], $properties['class'], $properties['with'], $properties['withCount'], $properties['original']);
        return $properties;
    }

    public function toJson()
    {
        $json = json_encode($this->toArray());
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonEncodingException(json_last_error_msg());
        }
        return $json;
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function syncOriginal()
    {
        $this->original = $this->getAttributes();
        return $this;
    }

    public function __call($methodName, $arguments)
    {
        $queryMethods = ['where', 'orWhere', 'whereIn', 'orderBy', 'take', 'skip', 'select', 'delete', 'get'];
        dd($queryMethods, "caşş");
        if (in_array($methodName, $queryMethods)) {
            static::database()->{$methodName}(...$arguments);
            return $this;
        } elseif (method_exists(self::class, $methodName)) {
            return $this->{$methodName}(...$arguments);
        }
        throw new BadMethodCallException('method does not exist');
    }

    public static function __callStatic($methodName, $arguments)
    {
        $queryMethods = ['where', 'orWhere', 'whereIn', 'orderBy', 'take', 'skip', 'select', 'get', 'delete', 'max', 'min', 'avg', 'sum'];
        if (in_array($methodName, $queryMethods)) {
            return static::database()->{$methodName}(...$arguments);
        } elseif ($methodName === 'update') {
            return self::updateInDatabase($arguments);
        } elseif (method_exists(self::class, $methodName)) {
            return self::$methodName(...$arguments);
        }
        throw new BadMethodCallException('method does not exist');
    }
}
