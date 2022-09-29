<?php
    function include_app($_dir){
        global $urls;
        include $_dir."/urls.php";
        foreach($appUrls as $url => $fileName){
            $urls["/".$_dir."/".$url] = $_dir."/".$fileName;
        }
    }
    #アプリごとにurlを記載する関数
    #urls.phpと同じディレクトリに$_dirと同じ名前のフォルダを作成
    #->中にurls.phpを作成
    #->urls.phpに$appUrlsという名で仮想連想配列を作成(key:url,value:ファイル名)
    #static fileは#static/{$_appName}/{$fileName}に保存
    require_once '../vendor/autoload.php';
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $request_url = $_SERVER["REQUEST_URI"];
    if(!$request_url = strstr($request_url,"?",true)){
        $request_url = $_SERVER["REQUEST_URI"];
    }
    $urls = array(
        #"url"=>"filename"
        "/"=>"index.php",
        "/testhtml"=>"test.html",
        "/home"=>"home.php",
    );
    include_app("test");
    include_app("auth");
    include_app("schedule");
    if(array_key_exists($request_url,$urls)){
        include $urls[$request_url];
    }else{
        $fileName = substr($request_url,1);
        $file = file_get_contents($fileName);
        header("Content-Type: ".mime_content_type($fileName));
        header("Content-Length: " . strlen($file));
        echo $file;
    }
?>