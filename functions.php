<?php

function dd()
{
    $HTML = '<html><head></head><body style="background-color: black;color: white;">';
    echo $HTML;
    foreach (func_get_args() as $item) {
        echo "<hr/>";
        echo "<pre>";
        print_r($item);
        echo "</pre>";
    }
    echo "<hr/></body></html>";
    exit;
}
