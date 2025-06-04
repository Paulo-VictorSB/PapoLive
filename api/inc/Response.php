<?php

class Response
{
    private $status;
    private $error_message;
    private $response_data;
    private $aditional_fields;

    public function __construct()
    {
        $this->status = 'success';
        $this->error_message = '';
        $this->response_data = null;
        $this->aditional_fields = [];
    }

    public function set_status($status = 'success')
    {
        $this->status = $status;
    }

    public function set_error_message($message)
    {
        $this->error_message = $message;
    }

    public function set_response_data($response_data)
    {
        $this->response_data = $response_data;
    }

    public function set_aditional_field($field_name, $field_value)
    {
        $this->aditional_fields[$field_name] = $field_value;
    }

    public function response()
    {
        $tmp = [];
        $tmp['status'] = $this->status;
        if (!empty($this->error_message)) {
            $tmp['error_message'] = $this->error_message;
        }
        $tmp['data'] = $this->response_data;

        // adicional fields
        if (!empty($this->aditional_fields) && is_array($this->aditional_fields)) {
            foreach ($this->aditional_fields as $key => $value) {
                $tmp[$key] = $value;
            }
        }

        $tmp['time_response'] = time();
        $tmp['api_version'] = $_ENV['API_VERSION'];

        echo json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
