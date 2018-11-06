<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 店铺销售额视图
 */
class VW_Shop_trade_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 'vw_sys_';
        $this->table_name = 'shop_trade';
        parent::__construct();
    }

}