<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use EnesEkinci\PhpSimpleDBWrapper\_Model;

class User extends QueryModel
{
    protected static $table = "users";
    protected $id;
    protected $u;
}
