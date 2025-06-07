<?php

require_once "../inc/init.php";

$data = json_decode(file_get_contents("php://input"), true);

check_request_method($request_method, 'POST');

$request_fields = [
    'user_uid',
    'room_uid',
    'content'
];

if (!check_required_fields_in_json($data, $request_fields)) {
    invalid_input_fields('Missing input fileds.');
}

$params = [
    ':user_uid' => $data['user_uid']
];

$check_if_name_exists = $db->execute_query("SELECT uid FROM users WHERE uid = :user_uid", $params);

if ($check_if_name_exists->affected_rows === 0) {
    invalid_data('User not exists');
}

$params = [
    ':room_uid' => $data['room_uid']
];

$check_if_room_exists = $db->execute_query("SELECT uid FROM rooms WHERE uid = :room_uid", $params);

if ($check_if_room_exists->affected_rows == 0) {
    invalid_data('Room not exists');
}

if (empty($data['content'])) {
    invalid_data('The message cannot be empty.');
}

if (strlen($data['content']) > 1000) {
    invalid_data('Your message cannot contain more than 1000 characters');
}

$params = [
    ':uid' => uniqid('', true),
    ':user_uid' => $data['user_uid'],
    ':room_uid' => $data['room_uid'],
    ':content' => $data['content']
];

$db->execute_non_query(
        "INSERT INTO messages " .
        "(uid, user_uid, room_uid, content, created_at) " .
        "VALUES " .
        "(:uid, :user_uid, :room_uid, :content, NOW())",
    $params
);

$res->response();
