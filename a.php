<?php
    $res = 1;
    $num = 25;
    for($i=365;$i>365-$num;$i--){
        $res = $res * $i / 365;
    }
    echo 1-$res;
?>