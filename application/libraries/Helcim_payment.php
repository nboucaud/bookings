<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helcim_payment {
    protected $CI;
    private $api_key;
    private $account_id;
    private $test_mode;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('config');
        $config = $this->CI->config->item('helcim');
        
        $this->api_key = $config['api_key'];
        $this->account_id = $config['account_id'];
        $this->test_mode = $config['test_mode'];
    }

    public function process_payment($data) {
        $endpoint = 'https://' . ($this->test_mode ? 'sandbox' : 'www') . '.helcim.com/api/purchase';
        
        $payload = [
            'cardToken' => $data['token'],        // From Helcim.js
            'amount' => $data['amount'],
            'ipAddress' => $this->CI->input->ip_address(),
            'ecommerce' => TRUE,
            'customerId' => $data['customer_id'],
            'comments' => 'Appointment #'.$data['appointment_id']
        ];

        $response = $this->send_request($endpoint, $payload);
        return $this->parse_response($response);
    }

    private function send_request($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer '.$this->api_key
        ]);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }

    private function parse_response($response) {
        $data = json_decode($response, TRUE);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'Helcim API invalid JSON: '.$response);
            return ['success' => FALSE, 'error' => 'Invalid API response'];
        }
        
        return $data;
    }
}