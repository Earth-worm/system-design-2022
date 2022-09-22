<?php
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $errors = array();
    $isSuccess = false;
    $isAuthenticated = isset($_SESSION["id"]);
    if($isAuthenticated){
        $isAuthenticated = $_SESSION["name"];
    }
    if($_SERVER["REQUEST_METHOD"]=="POST" and !$isAuthenticated){
        if(empty($_POST["email"])){
            array_push($errors,"メールが入力されていません。");
        }
        if(empty($_POST["password"])){
            array_push($errors,"パスワードが入力されていません。");
        }
        $db = new Sqlite3("db.sqlite3");
        $rtn = $db->query("select * from user where email='".$_POST["email"]."'");
        $tmp = $rtn->fetchArray();
        if(!$tmp){
            array_push($errors,"メールアドレスが間違っています。");
        }
        if(empty($errors)){
            $hash_pass = hash("sha256",$_POST["password"]);
            if(strcmp($hash_pass,$tmp["pass"])!=0){
                array_push("パスワードが間違っています。");
            }else{
                $_SESSION["id"] = $tmp["id"];
                $_SESSION["name"]=$tmp["name"];
                $isSuccess = $tmp["name"];
            }
        }
    }
    $context = array(
        "errors"=>$errors,
        "isSuccess"=>$isSuccess,
        "isAuthenticated"=>$isAuthenticated,
    );
    echo $twig->render('auth/login.html', $context);
?>