<?php
    require_once "tools.php";
    use Ramsey\Uuid\Uuid;
    $db = new Sqlite3(dirname(__DIR__)."/db.sqlite3");
    $token = Uuid::uuid4();
    $db->query("insert into token('url') values('".$token."')");
    $db->close();
    sendMessage($_POST["user_id"],$_ENV["Url"]."/schedule/tasklist?id=".$_POST["user_id"]."&token=".$token);
?>