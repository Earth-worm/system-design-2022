<?php
    function saveText($text=NULL){
        $textfile = 'C:\xampp\htdocs\cron\time.txt';
        $contents = file_get_contents($textfile);
        if(isset($text)){
            $contents .= $text."\n";
        }else{
            $contents .= date("H-i-s")."\n";
        }
        file_put_contents($textfile, $contents);
    }

    require_once dirname(dirname(__DIR__))."/vendor/autoload.php";
    require_once(dirname(__DIR__)."/utils.php");
    $db = new Sqlite3(dirname(__DIR__)."/db.sqlite3");
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    $date = date("Y-m-d");
    $users = $db->query("select * from user");
    while($user = $users->fetchArray()){
        $tasks = $db->query("select * from task where user_id ='".$user["id"]."' and date='".$date."'");
        $message = "今日の予定\n";
        while($task = $tasks->fetchArray()){
            $message = $message.$task["name"]."\n";
        }
        sendMessage("@".$user["id"],$message);
    }
?>