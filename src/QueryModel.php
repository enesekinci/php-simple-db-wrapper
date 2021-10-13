<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use RuntimeException;

abstract class QueryModel
{
    protected $connection;
    protected $table;
    protected $with = [];
    protected $withCount = [];
    protected $original;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function syncOriginal()
    {
        $this->original = $this->getAttributes();

        return $this;
    }

    public function getAttributes()
    {
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            // throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        // return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    public function syncOriginalAttributes($attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $modelAttributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            $this->original[$attribute] = $modelAttributes[$attribute];
        }

        return $this;
    }
}

class JsonEncodingException extends RuntimeException
{
}
