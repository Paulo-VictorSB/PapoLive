<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);

check_request_method($request_method, 'POST');

$request_fields = [
    'username'
];

if (!check_required_fields_in_json($data, $request_fields)) {
    invalid_input_fields('Missing input fileds.');
    exit();
}

$params = [
    ':username' => $data['username']
];

$check_if_name_exists = $db->execute_query("SELECT uid FROM users WHERE username = :username", $params);

if ($check_if_name_exists->affected_rows != 0) {
    $res->set_status('error');
    $res->set_error_message('Username already exists.');
    $res->response();
}

if (preg_match('/[^a-zA-Z0-9\-]/', $data['username'])) {
    invalid_data('Your username cannot contain special characters.');
    exit();
}

if (strlen($data['username']) < 5) {
    invalid_data('Your username cannot contain less than 5 characters');
    exit();
}

if (strlen($data['username']) > 20) {
    invalid_data('Your username cannot contain more than 20 characters');
    exit();
}

$params = [
    ':uid' => uniqid(),
    ':username' => $data['username'],
];

$db->execute_non_query(
        "INSERT INTO users " .
        "(uid, username, created_at) " .
        "VALUES " .
        "(:uid, :username, NOW())",
    $params
);

$res->response();
