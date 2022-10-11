<?php
        include_once "tools.php";
        $db = new Sqlite3("db.sqlite3");
        $token = createToken($db);
        $db->close();
?>