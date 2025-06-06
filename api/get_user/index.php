<?php

require_once "../inc/init.php";

$data = [
    'user' => $_GET['user']
];

check_request_method($request_method, 'GET');

if (empty($data['user'])) {
    $res->set_status('error');
    $res->set_error_message('Missing input fields');
    $res->response();
}

$params = [
    ':user' => $data['user']
];

$check_if_room_exists = $db->execute_query("SELECT uid FROM users WHERE username = :user", $params);

if ($check_if_room_exists->affected_rows == 0) {
    invalid_data('User not exists');
}

$results = $db->execute_query(
    "SELECT uid FROM users WHERE username = :user", $params
);

$res->set_response_data($results->results);
$res->response();