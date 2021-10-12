<?php

require_once('vendor/autoload.php');

use EnesEkinci\PhpSimpleDBWrapper\QueryBuilder as DB;
use EnesEkinci\PhpSimpleDBWrapper\QueryBuilder;

// $con = Database::connect();
// $sql = "SELECT * FROM users WHERE id = ? AND u = ?";
// $params = [1, "test"];

// $con->_read("users", [
//     'columns' => "id,u",
//     'conditions' => ["u = test", 'id = 1'],
//     'order' => 'id DESC',
//     "limit" => "1,15",
//     "offset" => "1",
//     "joins" => [
//         [
//             "user_details",
//             "ud.userId = users.id",
//             "ud",
//             "left",
//         ],
//     ],
// ]);

// $con->insert("users", ["u" => 'test3']);

// $con->delete('users', 3);

// $user = new User();
// $user->id = 15;
// $user->u = "test3";

// dd(User::findById(1));



/**
 * 
 */

$QueryBuilder = new QueryBuilder();

$data = $QueryBuilder->table('users')->select('u')->orderBy('u')->take(1)->skip(1)->where()->orWhere()->get();

$QueryBuilder->table('users')->update(['u' => 'test']);
dd($data);
