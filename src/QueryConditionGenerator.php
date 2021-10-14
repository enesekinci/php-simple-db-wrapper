<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

class QueryConditionGenerator
{

    public static function buildConditions(array $where)
    {
        $conditionString = "";
        $conditionStrings = [];

        foreach ($where as $condition) {

            if (array_key_exists('OR', $condition)) {
                $conditionString = static::orWhere($condition);
            } elseif (array_key_exists('NOT', $condition)) {
                $conditionString = static::whereNot($condition);
            } elseif (array_key_exists('NULL', $condition)) {
                $conditionString = static::whereNull($condition);
            } elseif (array_key_exists('IN', $condition)) {
                $conditionString = static::whereIn($condition);
            } elseif (array_key_exists('INNOT', $condition)) {
                $conditionString = static::whereInNot($condition);
            } elseif (array_key_exists('BETWEEN', $condition)) {
                $conditionString = static::whereBetween($condition);
            } elseif (array_key_exists('LIKE', $condition)) {
                $conditionString = static::whereLike($condition);
            } else {
                $conditionString = static::where($condition);
            }

            $conditionStrings[] = $conditionString;
        }

        $sentence  = implode(' ', $conditionStrings);
        $sentence = ltrim($sentence, ' OR');
        $sentence = ltrim($sentence, ' AND');
        $sentence = 'WHERE ' . $sentence;

        return $sentence;
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
            $sentence = " AND `{$column}` {$where[1]} {$comparison}";
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $sentence = " AND `{$column}` = {$comparison}";

        return $sentence;
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
            $sentence = " OR `{$column}` {$where[1]} {$comparison}";
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $sentence = " OR `{$column}` = {$comparison}";

        return $sentence;
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
            $sentence = " NOT `{$column}` {$where[1]} {$comparison}";
        }
        if (is_null($where[1])) {
            $comparison = 'IS NULL';
        } else {
            $comparison = "'{$where[1]}'";
        }
        $sentence = " NOT `{$column}` = {$comparison}";

        return $sentence;
    }

    public static function whereNull(array $where)
    {
        $sentence = "`{$where[0]}` IS NULL ";
        return $sentence;
    }

    public static function whereNotNull(array $where)
    {
        $sentence = "`{$where[0]}` IS NOT NULL ";
        return $sentence;
    }

    public static function whereIn(array $whereIn)
    {
        $column = $whereIn[0];
        $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'{$value}'", $whereIn[1]);
        $valueText = '(' . implode(',', $values) . ')';
        $sentence = " AND `{$column}` IN {$valueText}";
        return $sentence;
    }

    public static function whereInNot(array $whereInNot)
    {
        # code...
    }

    public static function whereBetween(array $whereBetween)
    {
        # code...
    }

    public static function whereLike(array $whereLike)
    {
        # code...
    }
}
