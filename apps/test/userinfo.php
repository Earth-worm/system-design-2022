<?php
    include_once "tools.php";
    $tokenList = file("cron/tokenlist.txt");
    $now = time();
    echo $now,"<br>";
    $count = 0;
    foreach($tokenList as $row){
        $data = explode(" ",$row);
        echo $row;
        if(intval($data[0]) > $now) break;
        $count++;
    }
    for($i=$count;$i>=0;$i--){
        unset($tokenList[$i]);
    }
    file_put_contents("cron/tokenlist.txt",$tokenList)
?>