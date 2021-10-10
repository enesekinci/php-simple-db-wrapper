<?php

namespace EnesEkinci\PhpSimpleDBWrapper;

use EnesEkinci\PhpSimpleDBWrapper\_Model;

class User extends _Model
{
    protected static $_table = "users";
    public $id;
    public $u;
}
