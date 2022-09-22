<?php
    session_start();

    $isAuthenticated = false;
    if(!isset($_SESSION["id"])){
        $isAuthenticated = $_SESSION["name"];
    }
?>