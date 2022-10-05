<?php
        require_once '../vendor/autoload.php';
        $loader = new \Twig\Loader\FilesystemLoader('view');
        $twig = new \Twig\Environment($loader);

        var_dump($_GET);
        $context = array(
        );
        echo $twig->render('test/get.html',$context);
?>