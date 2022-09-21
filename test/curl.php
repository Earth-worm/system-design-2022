<?php
    echo "here is curl";
    $ch = curl_init();
    curl_setopt_array($ch,array(
            CURLOPT_URL=>"http://www.google.com/",
            CURLOPT_RETURNTRANSFER=>true,
        )
    );
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    echo htmlspecialchars($res);
    var_dump($info)
?>