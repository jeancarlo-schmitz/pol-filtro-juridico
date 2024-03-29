<?php
define("DS", DIRECTORY_SEPARATOR);
define("DIR", __DIR__);
define("DIR_SRC", __DIR__ . DS . "src");
if ($_SERVER['HTTP_HOST'] === 'localhost' ||
    $_SERVER['HTTP_HOST'] === 'localhost:8000' ||
    $_SERVER['HTTP_HOST'] === 'localhost:8082'/*personal localhost*/
) {
    define("IS_DEV", true);
}else{
    define("IS_DEV", false);
}

