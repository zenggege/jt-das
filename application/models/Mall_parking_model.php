<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 停车
 */
class Mall_parking_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = '';
        $this->table_name = 'mall_parking';
        parent::__construct();
    }

}