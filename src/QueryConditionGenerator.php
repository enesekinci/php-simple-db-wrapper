<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

class QueryConditionGenerator
{

    public static function buildConditions(array $where)
    {
        $conditionString = "";
        $conditionStrings = [];

        foreach ($where as $condition) {
            if (array_key_exists('NOT', $condition)) {
                $conditionString = static::whereNot($condition);
            } elseif (array_key_exists('OR', $condition)) {
                $conditionString = static::orWhere($condition);
            } elseif (array_key_exists('NULL', $condition)) {
                $conditionString = static::whereNull($condition);
            } elseif (array_key_exists('NOT_NULL', $condition)) {
                $conditionString = static::whereNotNull($condition);
            } elseif (array_key_exists('IN', $condition)) {
                $conditionString = static::whereIn($condition);
            } elseif (array_key_exists('NOT_IN', $condition)) {
                $conditionString = static::whereInNot($condition);
            } elseif (array_key_exists('BETWEEN', $condition)) {
                $conditionString = static::whereBetween($condition);
            } elseif (array_key_exists('NOT_BETWEEN', $condition)) {
                $conditionString = static::whereBetween($condition);
            } elseif (array_key_exists('LIKE', $condition)) {
                $conditionString = static::whereLike($condition);
            } elseif (array_key_exists('NOT_LIKE', $condition)) {
                $conditionString = static::whereLike($condition);
            } else {
                $conditionString = static::where($condition);
            }
            $conditionStrings[] = $conditionString;
        }

        $query  = implode(' ', $conditionStrings);
        $query = ltrim($query, ' OR');
        $query = ltrim($query, ' AND');
        $query = 'WHERE ' . $query;
        return $query;
    }

    public static function where(array $where)
    {
        $column = $where[0];
        if (count($where) > 2 && isset($where[2])) {

            if (is_null($where[2])) {
                $comparison = 'IS NULL';
            } else {
                $comparison = "'{$where[2]}'";
            }
            $query = " AND `{$column}` {$where[1]} {$comparison}";
            return $query;
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $query = " AND `{$column}` = {$comparison}";
        return $query;
    }

    public static function orWhere(array $where)
    {
        $column = $where[0];
        if (count($where) > 2 && isset($where[2])) {

            if (is_null($where[2])) {
                $comparison = 'IS NULL';
            } else {
                $comparison = "'{$where[2]}'";
            }
            $query = " OR `{$column}` {$where[1]} {$comparison}";
            return $query;
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $query = " OR `{$column}` = {$comparison}";
        return $query;
    }

    public static function whereNot(array $where)
    {
        $column = $where[0];
        if (count($where) > 2 && isset($where[2])) {
            if (is_null($where[2])) {
                $comparison = 'IS NULL';
            } else {
                $comparison = "'{$where[2]}'";
            }
            $query = " NOT `{$column}` {$where[1]} {$comparison}";
            return $query;
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $query = " AND NOT `{$column}` = {$comparison}";
        return $query;
    }

    public static function whereNull(array $where)
    {
        $query = "`{$where[0]}` IS NULL ";
        return $query;
    }

    public static function whereNotNull(array $where)
    {
        $query = "`{$where[0]}` IS NOT NULL ";
        return $query;
    }

    public static function whereIn(array $where)
    {
        $column = $where[0];
        $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'{$value}'", $where[1]);
        $valueText = '(' . implode(',', $values) . ')';
        $query = " AND `{$column}` IN {$valueText}";
        return $query;
    }

    public static function whereInNot(array $where)
    {
        $column = $where[0];
        $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'{$value}'", $where[1]);
        $valueText = '(' . implode(',', $values) . ')';
        $query = " AND `{$column}` NOT IN {$valueText}";
        return $query;
    }

    public static function whereBetween(array $where)
    {
        $column = $where[0];
        $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'{$value}'", $where[1]);
        $values = implode(' AND ', $values);
        $query = " AND `{$column}` BETWEEN {$values}";
        return $query;
    }

    public static function whereNotBetween(array $where)
    {
        $column = $where[0];
        $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'{$value}'", $where[1]);
        $values = implode(' AND ', $values);
        $query = " AND `{$column}` NOT BETWEEN {$values}";
        return $query;
    }

    public static function whereLike(array $where)
    {
        $query = " AND `{$where[0]}` LIKE {$where[1]}";
        return $query;
    }

    public static function whereNotLike(array $where)
    {
        $query = " AND `{$where[0]}` NOT LIKE {$where[1]}";
        return $query;
    }
}
