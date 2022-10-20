<?php
    require_once '../vendor/autoload.php';
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $request_url = $_SERVER["REQUEST_URI"];
    if(!$request_url = strstr($request_url,"?",true)){
        $request_url = $_SERVER["REQUEST_URI"];
    }
    $urls = array(
        #"url"=>"filename"
        "/"=>"home.php",
        "/a"=>"a.php",
    );
    include_app("auth");
    include_app("schedule");
    include_app("slash");

    if(array_key_exists($request_url,$urls)){#リクエストされたページが存在する。
        include $urls[$request_url];
    }else{ #staticファイルか404のどっちか。
        $fileName = substr($request_url,1);
        if($file = @file_get_contents($fileName)){ #staticファイルが存在する
            header("Content-Type: ".mime_content_type($fileName));
            header("Content-Length: " . strlen($file));
            echo $file;
        }else{#staticもない
            header("HTTP/1.1 404 Not Found");
            include ("404.php");
            exit;
        }
    }

?>