<?php
    $message = "ダイレクトメッセージ送信テスト";

    $url = "https://slack.com/api/chat.postMessage";
    $post = [];
    $post['token'] = 'Slack APPのトークンID';
    $post['channel'] = '@ユーザー名';
    $post['icon_url'] = 'アイコンに指定したい画像のURL';
    $post['text'] = $message;
    
    $response = curlSend($url, $post);  
    
    //CURLで通信
    function curlSend($url, $post) {
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_HEADER, false);
        curl_setopt($conn, CURLOPT_URL, $url);
        curl_setopt($conn, CURLOPT_POST, true);
        curl_setopt($conn, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($conn);
    
        if (!curl_errno($conn)) {
            $header = curl_getinfo($conn);
        }
    
        curl_close($conn);
    
        if ($header['http_code'] != '200') {
            //エラー
            return false;
        }
    
        return $response;
    }
?>