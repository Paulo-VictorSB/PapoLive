<?php

function check_request_method($request_method, $expected_request_method) 
{
    if (strtoupper($request_method) !== strtoupper($expected_request_method)) {
        global $res;
        $res->set_status('error');
        $res->set_error_message('Invalid request method. Expected ' . strtoupper($expected_request_method));
        $res->response();
    }
}

function check_required_fields_in_json($data, $request_fields)
{
    foreach ($request_fields as $key) {
        if (!key_exists($key, $data)) {
            return false;
        }
    }

    return true;
}

function invalid_input_fields($message) {
    global $res;
    $res->set_status('error');
    $res->set_error_message($message);
    $res->response();
}

function invalid_data($message) {
    global $res;
    $res->set_status('error');
    $res->set_error_message($message);
    $res->response();
}