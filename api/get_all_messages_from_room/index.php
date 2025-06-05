<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);

check_request_method($request_method, 'GET');

$request_fields = [
    'room_uid'
];

if (!check_required_fields_in_json($data, $request_fields)) {
    invalid_input_fields('Missing input fileds.');
}

$params = [
    ':room_uid' => $data['room_uid']
];

$check_if_room_exists = $db->execute_query("SELECT uid FROM rooms WHERE uid = :room_uid", $params);

if ($check_if_room_exists->affected_rows == 0) {
    invalid_data('Room not exists');
}

var_dump($results = $db->execute_query(
    "SELECT m.*, r.expired_at " . 
    "FROM messages m " .
    "LEFT JOIN rooms r " .
    "ON m.room_uid = r.uid " .
    "WHERE r.created_at < r.expired_at " .
    "AND m.room_uid = :room_uid " .
    "ORDER BY created_at DESC", $params
));

$res->set_response_data($results->results);
$res->response();