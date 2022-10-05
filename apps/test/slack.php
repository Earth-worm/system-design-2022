<?php
    $headers = [
        'Authorization: Bearer xoxb-4078356373495-4142458472290-6a1zwybP1jjBOynbHTQfrKVR',
        'Content-Type: application/json;charset=utf-8'
    ];
    
    
    $url = "https://slack.com/api/chat.postMessage";
    
    $post_fields = [
        "channel" => "@mimizu0108",
        "text" => "こんにちは",
        "as_user" => true
    ];
    
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($post_fields) 
    ];

    /*
    GET
    https://slack.com/api/users.list
        $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST=>'GET'
        CURLOPT_POSTFIELDS => json_encode($post_fields) 
    ];
    */

    echo json_encode($post_fields);
    
    $ch = curl_init();
    
    curl_setopt_array($ch, $options);
    
    $result = curl_exec($ch); 
    echo $result;
    
    curl_close($ch);
?>