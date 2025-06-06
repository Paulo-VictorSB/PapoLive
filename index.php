<?php

session_start();

$inc = ['chat', 'index', 'logout'];

if (!isset($_GET['route']) || !in_array($_GET['route'], $inc)) {
    header("Location: ?route=index");
    exit;
}

$view = $_GET['route'];

require_once "templates/header.php";
switch($view) {
    case 'chat' :
        require_once "inc/chat.php";
        break;
    case 'logout' :
        require_once "inc/logout.php";
        break;
    default :
        require_once "inc/index.php";
}
require_once "templates/footer.php";
