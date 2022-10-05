<?php
    include "tools.php";
    $userId = $_POST["user_id"];
    $db = new Sqlite3("db.sqlite3");
    if(strcmp($_POST["text"],"")!=0){
        $tarId = strstr(strstr($_POST["text"],"|",1),"U");
        $tarUser = $db->query("select * from user where id ='".$tarId."'")->fetchArray();
        if(strcmp($tarId,"")==0 or !$tarUser){
            sendMessage($userId,"not exist such a user");
        }else{
            $token = createToken($db);
            sendMessage($userId,$_ENV["Url"]."/schedule/tasklist?id=".$tarId."&token=".$token);
        }
    }else{
        $token = createToken($db);
        sendMessage($userId,$_ENV["Url"]."/schedule/tasklist?id=".$userId."&token=".$token);
    }
    $db->close();
?>