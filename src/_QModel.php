<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use BadMethodCallException;
use EnesEkinci\PhpSimpleDBWrapper\Exception\JsonEncodingException;
use Exception;
use PDO;

abstract class _QModel
{
    protected $connection;
    protected $database;
    protected static $table;
    protected $with = [];
    protected $withCount = [];
    protected $original;
    protected $class;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function __construct()
    {
        $this->database = new QueryBuilder();
        $this->database->table(static::$table);
        $this->database->setFetchStyle(PDO::FETCH_CLASS);
        $this->class = get_class($this);
    }

    public static function table()
    {
        return static::$table;
    }

    public function __call($methodName, $arguments)
    {
        $queryMethods = ['where', 'orderBy', 'take', 'skip', 'select', 'insert', 'update', 'delete'];
        if (in_array($methodName, $queryMethods)) {
            $this->database->{$methodName}(...$arguments);
            return $this;
        } elseif (method_exists(self::class, $methodName)) {
            return $this->{$methodName}(...$arguments);
        }
        throw new BadMethodCallException('method does not exist');
    }

    public static function __callStatic($methodName, $arguments)
    {
        $queryMethods = ['where', 'orderBy', 'take', 'skip', 'select', 'get', 'insert', 'update', 'delete'];
        if (in_array($methodName, $queryMethods)) {
            // $this->database->{$methodName}(...$arguments);
        } elseif (method_exists(self::class, $methodName)) {
            // return $this->{$methodName}();
        }
        return;
    }

    public function get()
    {
        return $this->database->get($this->class);
    }

    public function update(array $fields)
    {
        $this->syncOriginal();
        return $this->database->update($fields, $this->class);
    }

    public function insert(array $fields)
    {
        return $this->database->insert($fields, $this->class);
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
}
