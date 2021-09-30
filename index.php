<?php

require_once('vendor/autoload.php');

use EnesEkinci\PhpSimpleDBWrapper\Database;
use EnesEkinci\PhpSimpleDBWrapper\User;

$con = Database::connect();
$sql = "SELECT * FROM users WHERE id = ? AND u = ?";
$params = [1, "test"];

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

$con->delete('users', 3);

$user = new User();
$user->id = 15;
$user->u = "test3";

dd(User::findById(1));
