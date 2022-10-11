<?php
    $tokenList = file(__DIR__.'/tokenlist.txt');
    $now = time();
    $count = 0;
    foreach($tokenList as $row){
        $data = explode(" ",$row);
        if(intval($data[0]) > $now) break;
        $count++;
    }
    for($i=$count;$i>=0;$i--){
        unset($tokenList[$i]);
    }
    echo __DIR__."/tokenlist.txt";
    file_put_contents(__DIR__.'/tokenlist.txt',$tokenList);
    file_put_contents(__DIR__.'/time.txt',time()."\n", FILE_APPEND);
?>