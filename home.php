<?php
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $isAuthenticated = isset($_SESSION["id"]);
    if($isAuthenticated){
        $isAuthenticated = $_SESSION["name"];
    }
    $context = array(
        "isAuthenticated"=>$isAuthenticated,
    );
    echo $twig->render('home.html', $context);
?>