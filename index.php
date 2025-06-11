<?php
session_start();

$rotasValidas = ['chat', 'index', 'logout'];
$route = $_GET['route'] ?? 'index';

// Garante que a rota é válida
if (!in_array($route, $rotasValidas)) {
    header("Location: ?route=index");
    exit;
}

// Protege o chat: só entra se estiver logado
if ($route === 'chat' && !isset($_SESSION['username'])) {
    header("Location: ?route=index");
    exit;
}

// Se o usuário já está logado e tenta acessar index, redireciona para chat
if ($route === 'index' && isset($_SESSION['username'])) {
    header("Location: ?route=chat");
    exit;
}

// Renderiza a página
require_once "templates/header.php";

switch ($route) {
    case 'chat':
        require_once "inc/chat.php";
        break;
    case 'logout':
        require_once "inc/logout.php";
        break;
    case 'index':
    default:
        require_once "inc/index.php";
        break;
}

require_once "templates/footer.php";
