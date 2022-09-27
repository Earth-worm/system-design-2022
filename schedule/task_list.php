<?php
    session_start();
    include("tools.php");
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $month = date("Y-m");
    $isAuthenticated = isset($_SESSION["id"]);
    $schedule = new Schedule($month);
    $db = new Sqlite3("db.sqlite3");
    $rtn = $db->query("select * from task where date like '".$month."%'");
    while($row = $rtn->fetchArray()){
        $schedule->addTask(intval(substr($row["date"],-2)),$row["name"],$row["holiday"],$row["time"]);
    }
    $html =  $schedule->genHTML();
    $context = array(
        "isAuthenticated"=>$isAuthenticated,
        "html"=>$html,
    );
    echo $twig->render('schedule/task_list.html',$context);
?>