<?php
    require_once dirname(dirname(__DIR__))."/vendor/autoload.php";
    require_once(dirname(__DIR__)."/tools.php");
    saveText("2");
    $db = new Sqlite3(dirname(__DIR__)."/db.sqlite3");
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
?>