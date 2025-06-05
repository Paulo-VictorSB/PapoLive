<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);
$data['room'] = trim($data['room']);

check_request_method($request_method, 'POST');

$request_fields = [
    'room'
];

if (!check_required_fields_in_json($data, $request_fields)) {
    invalid_input_fields('Missing input fileds.');
}

if (!isset($data['password'])) {
    $data['password'] = '';
}

$params = [
    ':room' => $data['room']
];

$check_if_name_exists = $db->execute_query("SELECT uid FROM rooms WHERE name = :room", $params);

if ($check_if_name_exists->affected_rows != 0) {
    $res->set_status('error');
    $res->set_error_message('Room name already exists.');
    $res->response();
}

if (preg_match('/[^a-zA-Z0-9\- ]/', $data['room'])) {
    invalid_data('Your room name cannot contain special characters.');
}

if (strlen($data['room']) < 5) {
    invalid_data('Your room name cannot contain less than 5 characters');
}

if (strlen($data['room']) > 20) {
    invalid_data('Your room name cannot contain more than 20 characters');
}

if (!empty($data['password'])) {
    if (strlen($data['password']) < 5) {
        invalid_data('Your password cannot contain less than 5 characters');
    }

    if (strlen($data['password']) > 20) {
        invalid_data('Your password cannot contain more than 20 characters');
    }
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
}

$params = [
    ':uid' => uniqid('', true),
    ':room' => $data['room'],
    ':password' => !empty($data['password']) ? $data['password'] : null
];

$db->execute_non_query(
    "INSERT INTO rooms " .
        "(uid, name, created_at, expired_at, password) " .
        "VALUES " .
        "(:uid, :room, NOW(), NOW() + INTERVAL 7 DAY, :password)",
    $params
);

$res->response();
