<?php
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);
    
    $isAuthenticated = false;
    $isSuccess = false;
    if(isset($_SESSION["name"])){
        $isAuthenticated = $_SESSION["name"];
    }
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        session_destroy();
        $isSuccess = true;
        $isAuthenticated = false;
    }
    $context = array(
        "isSuccess"=>$isSuccess,
        "isAuthenticated"=>$isAuthenticated,
    );
    echo $twig->render('auth/logout.html', $context);
?>