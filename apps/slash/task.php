<?php
    require_once("tools.php");
    echo $_POST["text"];
    if(strcmp($_POST["text"],"")==0){
        $text = $_POST["text"];
        $text = str_replace(array("\r\n","\r"),"\n",$text);
        $arr = explode("\n",$text);
        foreach($arr as $t){
            echo $t;
        }
    }
?>