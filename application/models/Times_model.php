<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Time 表
 */
class Times_model extends JT_Model
{

    public function __construct() {
        $this->db_tablepre = 't_jt_';
        $this->table_name = 'times';
        parent::__construct();
    }

}