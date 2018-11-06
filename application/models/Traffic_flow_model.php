<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车流表
 */
class Traffic_flow_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'traffic_flow';
        parent::__construct();
    }

}