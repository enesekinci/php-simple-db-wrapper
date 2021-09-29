<?php

function dd()
{
    foreach (func_get_args() as $item) {
        echo "<pre>";
        print_r($item);
        echo "</pre>";
    }
    exit;
}
