<?php
    session_start();
    include("tools.php");
    require_once "../vendor/autoload.php";
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);
    $db = new Sqlite3("db.sqlite3");

    $isAuthenticated = isset($_SESSION["id"]);
    $html = NULL;
    $errors = array();
    $month = date("Y-m");
    $id = NULL;
    if($isAuthenticated or isset($_GET["token"])){
        if(isset($_GET["token"])){
            $rtn = $db->query("select * from token where url='".$_GET["token"]."'");
            if($rtn->fetchArray()){
                $isAuthenticated = $_GET["id"];
            }else{
                array_push($errors,"このトークンは有効ではありません。");
            }
        }else{
            $isAuthenticated = $_SESSION["id"];
            if(isset($_GET["id"])){
                $id = $_GET["id"];
                $rtn = $db->query("select * from user where id ='".$id."'");
                if(!$rtn->fetchArray()){
                    array_push($errors,"このユーザは存在しません。");
                }
            }
        }
        if(isset($_GET["month"])){
            $month = $_GET["month"];
        }
        if(empty($errors)){
            $id = $isAuthenticated;
            $schedule = new Schedule($month);
            $rtn = $db->query("select * from task where user_id = '".$id."' and date like '".$month."%'");
            while($row = $rtn->fetchArray()){
                $schedule->addTask(intval(substr($row["date"],-2)),$row["name"],$row["holiday"]==1,$row["time"],$row["id"]);
            }
            $html =  $schedule->genHTML();
        }
    }
    $months = array("now"=>$month,
                    "before"=>date('Y-m', strtotime($month." -1 month")),
                    "next"=>date('Y-m', strtotime($month." +1 month")),);
    $context = array(
        "isAuthenticated"=>$isAuthenticated,
        "html"=>$html,
        "errors"=>$errors,
        "months"=>$months,
        "id"=>$id,
    );
    echo $twig->render('schedule/task_list.html',$context);
    $db->close();
?>