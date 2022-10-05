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
    $url="aaa/aaa/vfvdsa/bdsng&token=AAAAAA";
    $url = http_build_query($url,["id"=>"idddd"]);
    echo $url;
?>