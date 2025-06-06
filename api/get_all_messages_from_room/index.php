<?php

require_once "../inc/init.php";

$data = [
    'room_uid' => $_GET['room_uid']
];

check_request_method($request_method, 'GET');

if (empty($data['room_uid'])) {
    $res->set_status('error');
    $res->set_error_message('Missing input fields');
    $res->response();
}

$params = [
    ':room_uid' => $data['room_uid']
];

$check_if_room_exists = $db->execute_query("SELECT uid FROM rooms WHERE uid = :room_uid", $params);

if ($check_if_room_exists->affected_rows == 0) {
    invalid_data('Room not exists');
}

$results = $db->execute_query(
    "SELECT m.uid msg_uid, r.name room, u.username user, m.content content, m.created_at created_at " .
    "FROM messages m " .
    "LEFT JOIN rooms r " .
    "ON m.room_uid = r.uid " .
    "LEFT JOIN users u " .
    "ON m.user_uid = u.uid " .
    "WHERE r.created_at < r.expired_at " .
    "AND m.room_uid = :room_uid " .
    "ORDER BY m.created_at DESC",
    $params
);

$res->set_response_data($results->results);
$res->response();