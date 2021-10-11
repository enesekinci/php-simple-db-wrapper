<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

final class QueryGenerator
{
    public static function selectGenerator($table, $columns = '*', $limit = null, $offset = null, $where = [], $orderBy = [], array $joins = [])
    {
        $columns = is_array($columns) ? implode(',', $columns) : $columns;
        $joinString = "";
        $conditionString = "";
        $conditionStrings = [];
        $orderString = "";

        if ($where) {
            foreach ($where as $condition) {
                if (count($condition) > 2 && isset($condition[2])) {
                    $conditionStrings[] = "{$condition[0]} {$condition[1]} {$condition[2]}";
                } else {
                    $conditionStrings[] = "{$condition[0]} =  {$condition[1]}";
                }
            }
        }

        foreach ($conditionStrings as $key => $string) {

            if (array_key_exists('or', $where[$key])) {
                echo $key . "<br/>";
                $conditionString .=  " OR {$string}";
            } else {
                $conditionString .=  " AND {$string}";
            }
        }

        $conditionString = ltrim($conditionString, ' OR');
        $conditionString = ltrim($conditionString, ' AND');
        if ($conditionString) {
            $conditionString = "WHERE {$conditionString}";
        }

        if ($orderBy) {
            $orders = [];
            foreach ($orderBy as $order) {
                $orders[] = "{$order[0]} {$order[1]}";
            }
            $orderBy = implode(",", $orders);
            $orderString = "ORDER BY {$orderBy}";
        }

        if ($limit) {
            $limit = "LIMIT {$limit}";
        }

        if ($offset) {
            $offset = "OFFSET {$offset}";
        }

        if ($joins) {
            foreach ($joins as $join) {
                $joinString .= static::_buildJoin($join);
            }
            $joinString .= " ";
        }

        $QueryString = "SELECT {$columns} FROM {$table} {$joinString} {$conditionString} {$orderString} {$limit} {$offset}";
        return $QueryString;
    }

    public static function insertGenerator($table, $columns, $fields)
    {
        #
    }

    public static function updateGenerator($table, $columns, $fields, $where = [])
    {
        #
    }

    public static function deleteGenerator($table, $where = [])
    {
        #
    }

    protected static function _buildJoin($join = [])
    {
        $table = $join[0];
        $condition = $join[1];
        $alias = $join[2];
        $type = (isset($join[3])) ? strtoupper($join[3]) : ' INNER ';
        $jString = " {$type} JOIN {$table} {$alias} ON {$condition}";
        return $jString;
    }
}
