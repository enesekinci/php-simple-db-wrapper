<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

final class QueryGenerator
{
    public static function selectGenerator($table, $columns = '*', $limit = null, $offset = null, $where = [], $orderBy = [])
    {
        $columns = is_array($columns) ? implode(',', $columns) : '*';
        $joins = "";
        $conditionString = "";
        $orderString = "";

        if ($where) {
            foreach ($where as $condition) {
                if (is_array($condition)) {
                    $this->_where[] = $condition;
                } else {
                    $this->_where[] = $conditions;
                    break;
                }
            }
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

        $QueryString = "SELECT {$columns} FROM {$table} {$joins} {$conditionString} {$orderString} {$limit} {$offset}";
        dd($QueryString, func_get_args());
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
}
