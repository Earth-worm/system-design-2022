<?php
    $db = new SQLite3('db.sqlite3');
    var_dump($db);
    $rtn = $db->query("select * from user");
    while ($row = $rtn->fetchArray()) {
        var_dump($row);
    }
    $db->close();
?>