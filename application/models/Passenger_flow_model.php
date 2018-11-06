<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 客流表
 */
class Passenger_flow_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'passenger_flow';
        parent::__construct();
    }

}