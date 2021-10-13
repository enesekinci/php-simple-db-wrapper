<?php

require_once('vendor/autoload.php');

use EnesEkinci\PhpSimpleDBWrapper\QueryBuilder as DB;
use EnesEkinci\PhpSimpleDBWrapper\QueryBuilder;
use EnesEkinci\PhpSimpleDBWrapper\User;

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
$QueryBuilder->setFetchStyle(PDO::FETCH_CLASS);
$data = $QueryBuilder->table('users')->orderBy('u')->where()->orWhere()->get(User::class);
// $data = $QueryBuilder->table('users')->select('u')->orderBy('u')->take(1)->skip(1)->where()->orWhere()->get();
dd($data, $data[0]::$_table, User::class);
// $update = $QueryBuilder->table('users')->where(['u', 'test2'])->update(['u' => 'test']);

// $insert = $QueryBuilder->table('users')
// ->insert([
// 'u' => 'test3'
// ]);

$delete = $QueryBuilder->table('users')->where(['u', 'test2'])->orWhere(['u', 'test3'])->delete();

dd(["delete" => $delete]);
