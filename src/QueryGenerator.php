<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use EnesEkinci\PhpSimpleDBWrapper\QueryConditionGenerator;

final class QueryGenerator
{
    public static function select($table, $columns = '*', $limit = null, $offset = null, $where = [], $orderBy = [], array $max = [], array $min = [], array $groupBy = [], array $joins = [])
    {

        $columns = static::_buildColumns($columns, $max, $min);

        $conditionString = QueryConditionGenerator::buildConditions($where);

        $orderString = static::_buildOrderBy($orderBy);

        $limit = $limit ? "LIMIT {$limit}" : $limit;

        $offset = $offset ? "OFFSET {$offset}" : $offset;

        $joinString = static::_buildJoin($joins);

        $groupByString = static::_buildGroupBy($groupBy);

        $QueryString = "SELECT {$columns} FROM `{$table}` {$joinString} {$conditionString} {$groupByString} {$orderString} {$limit} {$offset}";
        return $QueryString;
    }

    public static function insert($table, $fields)
    {
        // $fieldParamString = static::_buildFieldParams($fields);
        // $valueParamString = static::_buildFieldValues($fields);

        $fields = array_map(fn ($field) => "`{$field}`", array_keys($fields));
        $fieldParamString = implode(',', $fields);
        $valueParamString = implode(',', array_fill(0, count($fields), '?'));

        $QueryString = "INSERT INTO {$table} ({$fieldParamString}) VALUES ({$valueParamString})";
        return $QueryString;
    }

    public static function update($table, $fields, $where = [])
    {
        $conditionString = QueryConditionGenerator::buildConditions($where);
        $fieldParamString = static::_buildFieldParams($fields);
        $QueryString = "UPDATE `{$table}` SET {$fieldParamString} {$conditionString}";
        return $QueryString;
    }

    public static function delete(string $table, array $where = [])
    {
        $conditionString = QueryConditionGenerator::buildConditions($where);
        $QueryString = "DELETE FROM {$table} {$conditionString}";
        return $QueryString;
    }

    protected static function _buildFieldParams($params)
    {
        $fieldString = '';
        foreach ($params as $field => $value) {
            $fieldString .= '`' . $field . '` = ?,';
        }
        $fieldString = trim($fieldString);
        $fieldString = rtrim($fieldString, ",");
        return $fieldString;
    }

    protected static function _buildFieldValues($fields)
    {
        $valueString = "";
        $values = [];
        foreach ($fields as $value) {
            $valueString .= "'{$value}' , ";
            $values[] = $value;
        }
        $valueString = rtrim(trim($valueString), ',');
        return $valueString;
    }

    protected static function _buildOrderBy($orderBy)
    {
        $orderString = "";
        if ($orderBy) {
            $orders = [];
            foreach ($orderBy as $order) {
                $orders[] = "{$order[0]} {$order[1]}";
            }
            $orderBy = implode(",", $orders);
            $orderString = "ORDER BY {$orderBy}";
        }
        return $orderString;
    }

    protected static function _buildJoin($joins)
    {
        $jString = "";
        if ($joins) {
            foreach ($joins as $join) {
                $table = $join[0];
                $condition = $join[1];
                $alias = $join[2];
                $type = (isset($join[3])) ? strtoupper($join[3]) : ' INNER ';
                $jString = " {$type} JOIN {$table} {$alias} ON {$condition}";
            }
            $jString .= " ";
        }
        return $jString;
    }

    protected static function _buildColumns($columns, $max = [], $min = [])
    {
        if ($max) {
            $columns = " MAX(`{$max['column']}`) ";
            if ($max['as']) {
                $columns .= "AS `{$max['as']}`";
            }
        } elseif ($min) {
            $columns = " MIN(`{$min['column']}`) ";
            if ($min['as']) {
                $columns .= "AS `{$min['as']}`";
            }
        }
        $columns = is_array($columns) ? implode(',', $columns) : $columns;
        return $columns;
    }

    protected static function _buildGroupBy(array $groupBy)
    {
        $groupByString = "";
        if ($groupBy) {
            $groupByString .= 'GROUP BY ' . implode(',', $groupBy);
        }
        return $groupByString;
    }
}
