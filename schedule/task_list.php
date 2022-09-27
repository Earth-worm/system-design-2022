<?php
    session_start();
    include("tools.php");
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $isAuthenticated = isset($_SESSION["id"]);
    $html = NULL;
    $errors = array();
    $month = date("Y-m");
    if($isAuthenticated){
        $db = new Sqlite3("db.sqlite3");
        $isAuthenticated = $_SESSION["id"];
        $id = $isAuthenticated;
        if(isset($_GET["id"])){
            $id = $_GET["id"];
            $rtn = $db->query("select * from user where id ='".$id."'");
            if(!$rtn->fetchArray()){
                array_push($errors,"このユーザは存在しません。");
            }
        }
        if(isset($_GET["month"])){
            $month = $_GET["month"];
        }
        if(empty($errors)){
            $schedule = new Schedule($month);
            $rtn = $db->query("select * from task where user_id = '".$id."' and date like '".$month."%'");
            while($row = $rtn->fetchArray()){
                $schedule->addTask(intval(substr($row["date"],-2)),$row["name"],$row["holiday"],$row["time"],$row["id"]);
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
?>