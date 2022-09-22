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
    $urls = array(
        #"url"=>"filename"
        "/"=>"index.php",
        "/testhtml"=>"test.html",
    );
    include_app("test");
    include_app("auth");
    if(array_key_exists($_SERVER["REQUEST_URI"],$urls)){
        include $urls[$_SERVER["REQUEST_URI"]];
    }else{
        $fileName = substr($_SERVER["REQUEST_URI"],1);
        $file = file_get_contents($fileName);
        header("Content-Type: ".mime_content_type($fileName));
        header("Content-Length: " . strlen($file));
        echo $file;
    }
?>