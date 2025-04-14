<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the  array.
// Example: ['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

$config['useragent'] = 'Easy!Appointments';
$config['protocol'] = 'mail'; // or 'smtp'
$config['mailtype'] = 'html'; // or 'text'
$config['smtp_debug'] = '0'; // or '1'
$config['smtp_auth'] = 1; //or FALSE for anonymous relay.
$config['smtp_host'] = 'mail.smtp2go.com';
$config['smtp_user'] = 'infogito.com';
$config['smtp_pass'] = 'EHfyrGxNnDs4jJdR';
$config['smtp_crypto'] = 'tls'; // or 'tls'
$config['smtp_port'] = 8025;
$config['from_name'] = 'Bookings';
$config['from_address'] = 'reply@infogito.com';
$config['reply_to'] = 'reply@infogito.com';
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
