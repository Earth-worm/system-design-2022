<?php
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $isAuthenticated = false;
    $isSuccess = false;
    $errors = array();

    #ログインされていれば表示内容を変更
    if(isset($_SESSION["id"])){
        $isAuthenticated = $_SESSION["name"];
    }

    #postされたらタスク作成処理
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $name = $_POST["name"];
        $date = $_POST["date"];
        $time = $_POST["time"];
        $holiday = isset($_POST["holiday"]);

        #入力されたデータが有効か判別
        if(empty($name)){
            array_push($errors,"名前が入力されていません。");
        }
        if(empty($date)){
            array_push($errors,"日付が入力されていません。");
        }

        #エラーが挙げられなければタスク作成
        if(empty($errors)){
            $user_id = $_SESSION["id"];
            $db = new Sqlite3("db.sqlite3");
            $query = "";

            #postされたデータに時間が指定されているか
            if(strcmp($time,"")==0){
                if($holiday){
                    $query = "insert into task(name,date,time,user_id,holiday) values('".$name."','".$date."',NULL,'".$user_id."',True)";
                }else{
                    $query = "insert into task(name,date,time,user_id,holiday) values('".$name."','".$date."',NULL,'".$user_id."',False)";
                }
            }else{
                if($holiday){
                    $query = "insert into task(name,date,time,user_id,holiday) values('".$name."','".$date."','".$time."','".$user_id."',True)";
                }else{
                    $query = "insert into task(name,date,time,user_id,holiday) values('".$name."','".$date."','".$time."','".$user_id."',False)";
                }
            }
            $db->query($query);
            $isSuccess = $name;
        }
    }
    $context = array(
        "isSuccess"=>$isSuccess,
        "isAuthenticated"=>$isAuthenticated,
        "errors"=>$errors,
    );
    echo $twig->render('schedule/create_task.html', $context);
?>