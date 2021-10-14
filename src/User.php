<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use EnesEkinci\PhpSimpleDBWrapper\QueryModel;

class User extends QueryModel
{
    protected static $table = "users";
    protected $id;
    protected $u;
}
