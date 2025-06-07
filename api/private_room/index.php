<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);

check_request_method($request_method, 'POST');

$request_fields = [
    'room_uid',
    'password'
];

if (!check_required_fields_in_json($data, $request_fields)) {
    invalid_input_fields('Missing input fileds.');
}

$params = [
    ':room_uid' => $data['room_uid']
];

$check_if_name_exists = $db->execute_query("SELECT uid FROM rooms WHERE uid = :room_uid", $params);

if ($check_if_name_exists->affected_rows == 0) {
    $res->set_status('error');
    $res->set_error_message('Room not exists');
    $res->response();
}

$params = [
    ':room_uid' => $data['room_uid'],
];

$results = $db->execute_query(
    "SELECT password FROM rooms WHERE uid = :room_uid", $params
);

if (!password_verify($data['password'], $results->results[0]->password)) {
    invalid_data("Incorrect password");
}

$res->response();