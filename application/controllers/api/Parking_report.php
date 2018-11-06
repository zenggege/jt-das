<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 停车场积分管理
 */
class Parking_report extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Parking_report']);
    }

    function index_get(){
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
        $begin_date = intval($this->get('begin_date',true));#开始日期
        $end_date = intval($this->get('end_date',true));#结束日期
        $keyword = safe_replace(trim($this->get('keyword',true)));#关键词

        if(!$per_page) $per_page = 10;
        $this->v1_parking_report->set_page($page,$per_page);
        $this->v1_parking_report->set_filter(['group_id'=>$group_id,'keyword'=>$keyword]);
        $data = $this->v1_parking_report->jifen_list();
        
        $this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data,
                        'data_count'=>intval($this->User_model->data_count),
                        'page_count'=>intval($this->User_model->page_count),
                        'page_index'=>$this->User_model->page_index,
                        'page_size'=>$this->User_model->page_size],HTTP_OK);
    }



}