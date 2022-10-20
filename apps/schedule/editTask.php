<?php
    session_start();
    include("utils.php");
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $isAuthenticated = false;
    $isSuccess = false;
    $isDeleted = false;
    $errors = array();
    $task = NULL;
    
    #ログインされていれば表示内容を変更
    if(isset($_SESSION["id"])){
        $isAuthenticated = $_SESSION["name"];
    }

    #getでリクエストされたらタスクの表示
    if(isset($_GET["id"])){
        $db = new Sqlite3("db.sqlite3");
        $rtn = $db->query("select * from task where id = ".$_GET["id"]);
        $tmp = $rtn->fetchArray();

        #編集をしたいタスクのidが存在するか
        if(!$tmp or $tmp["user_id"] != $_SESSION["id"] or !$isAuthenticated){
            array_push($errors,"このタスクは存在しません。");
        }else{
            $task = new Task($tmp["name"],$tmp["time"],$tmp["holiday"],$tmp["id"],$tmp["date"]);

            #postされたらタスクを編集
            if($_SERVER["REQUEST_METHOD"]=="POST"){

                #deleteがsubmitされたらタスク削除
                if(isset($_POST["delete"])){
                    $db->query("delete from task where id =".$_GET["id"]);
                    $isDeleted = $tmp["name"];
                }else{
                    $task->name = $_POST["name"];
                    $task->date = $_POST["date"];
                    $task->isHoliday = isset($_POST["holiday"]);
                    if(isset($_POST["time"]))$task->time= $_POST["time"];
                    $query = "update task set name='".$task->name."',date='".$task->date."',time=NULL,holiday=True where id = ".$_GET["id"];
                    if($task->time){
                        if($task->isHoliday){
                            $query = "update task set name='".$task->name."',date='".$task->date."',time='".$task->time."',holiday=True where id = ".$_GET["id"];
                        }else{
                            $query = "update task set name='".$task->name."',date='".$task->date."',time='".$task->time."',holiday=False where id = ".$_GET["id"];
                        }
                    }
                    $db->query($query);
                    $isSuccess = $task->name;
                }
            }
        }
    }else{
        array_push($errors,"このタスクは存在しません。");
    }
    $context = array(
        "isSuccess"=>$isSuccess,
        "isAuthenticated"=>$isAuthenticated,
        "isDeleted"=>$isDeleted,
        "task"=>$task,
        "errors"=>$errors,
    );
    echo $twig->render('schedule/edit_task.html', $context);
?>