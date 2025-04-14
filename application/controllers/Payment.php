<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->load->model('services_model');
    }

    public function index() {
        try {
            $appointment = json_decode(urldecode($this->input->get('appointment')), true);
            
            // Verify appointment exists
            $existing = $this->appointments_model->get_batch(['id' => $appointment['id']]);
            if (empty($existing) || $existing[0]['hash'] !== $appointment['hash']) {
                throw new Exception('Invalid appointment');
            }

            // Get service price
            $service = $this->services_model->get_batch(['id' => $existing[0]['id_services']])[0];
            
            $this->load->view('pages/payment', [
                'appointment' => $appointment,
                'amount' => $service['price'],
                'currency' => 'CAD' // Helcim defaults to CAD
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Payment error: ' . $e->getMessage());
            show_error('Payment processing unavailable', 500);
        }
    }

    public function complete() {
        // Verify Helcim response
        $this->verify_helcim_ip();
        
        $transaction_id = $this->input->post('transactionId');
        $appointment_id = $this->input->post('custom1');
        $status = ($this->input->post('transactionSuccess') == '1') ? 'paid' : 'failed';

        // Update appointment
        $this->appointments_model->update($appointment_id, [
            'payment_status' => $status,
            'helcim_transaction_id' => $transaction_id
        ]);

        // Redirect user
        if ($status === 'paid') {
            redirect('booking_confirmation?appointment=' . $appointment_id);
        } else {
            redirect('booking/failed?code=payment_declined');
        }
    }

    private function verify_helcim_ip() {
        $allowed_ips = ['209.234.240.0/21', '216.137.160.0/20'];
        $client_ip = $this->input->ip_address();
        
        foreach ($allowed_ips as $ip) {
            if ($this->ip_in_range($client_ip, $ip)) {
                return true;
            }
        }
        
        log_message('error', 'Invalid IP for Helcim callback: ' . $client_ip);
        show_error('Unauthorized', 403);
    }
    
}