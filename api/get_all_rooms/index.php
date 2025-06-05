<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);

check_request_method($request_method, 'GET');

$results = $db->execute_query(
    "SELECT * FROM rooms WHERE created_at < expired_at"
);

$res->set_response_data($results->results);
$res->response();