<?php
    function curlSlack($url,$method,$data=NULL){
        $headers = [
            'Authorization: Bearer xoxb-4078356373495-4128382869687-P8NlmR7Y1VZEfOYpJTZyBIQA',
            'Content-Type: application/json;charset=utf-8'
        ];
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        if($method=="GET"){
            if(isset($data)){
                $url = $url."?".http_build_query($data);
            }
        }else if($mthod="POST"){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data) ;
        }
        $options[CURLOPT_URL]=$url;
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
        
    }

    function getUserInfo($userId){
        $url = "https://slack.com/api/users.info";
        $method = "GET";
        $data = array(
            "user"=>$userId,
        );
        $url2 = "https://slack.com/api/users.info?user=".$userId;
        return curlSlack($url,$method,$data);
    }

    function sendMessage($ch,$message,$as_user=true){
        $data = array(
            "channel" => $ch,
            "text" => $message,
            "as_user" => $as_user
        );
        $url = "https://slack.com/api/chat.postMessage";
        return curlSlack($url,"POST",$data);
    }
    #echo getUserInfo("U043EJ79AC8");


?>