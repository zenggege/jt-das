<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商铺交易表
 */
class Shop_trade_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'shop_trade';
        parent::__construct();
    }

}