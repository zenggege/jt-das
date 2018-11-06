<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商铺表
 */
class Shop_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'shop';
        parent::__construct();
    }

}