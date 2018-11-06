<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 会员表
 */
class Vip_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_aci_';
        $this->table_name = 'vip';
        parent::__construct();
    }

}