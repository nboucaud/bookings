<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_helcim_payment_columns extends CI_Migration {
    public function up() {
        $fields = [
            'payment_status' => [
                'type' => "ENUM('pending', 'paid', 'failed')",
                'default' => 'pending',
                'null' => FALSE
            ],
            'helcim_transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ]
        ];
        $this->dbforge->add_column('ea_appointments', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ea_appointments', 'payment_status');
        $this->dbforge->drop_column('ea_appointments', 'helcim_transaction_id');
    }
}