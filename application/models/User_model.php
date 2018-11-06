<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 管理员表
 */
class User_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'staff';
        parent::__construct();
    }

}