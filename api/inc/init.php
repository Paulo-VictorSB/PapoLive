<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

header("Content-Type: application/json; charset=UTF-8");

require_once "Database.php";
require_once "Response.php";
require_once "Helper.php";

date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$res = new Response();

if (!$_ENV['API_ACTIVE']) {
    $res->set_status('error');
    $res->set_error_message($_ENV['API_ACTIVE']);
    $res->response();
}

$request_method = $_SERVER['REQUEST_METHOD'];

$mysql_config = [
    'host' => $_ENV['MYSQL_HOST'],
    'database' => $_ENV['MYSQL_DATABASE'],
    'username' => $_ENV['MYSQL_USER'],
    'password' => $_ENV['MYSQL_PASS']
];

$db = new Database($mysql_config);