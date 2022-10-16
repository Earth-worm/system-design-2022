<?php
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);
    $isAuthenticated = false;
    if(isset($_SESSION["id"])){
        $isAuthenticated = $_SESSION["name"];
    }
    $context = array(
        "isAuthenticated"=>$isAuthenticated
    );
    echo $twig->render('404.html', $context);
?>