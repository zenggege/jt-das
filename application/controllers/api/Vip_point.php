<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 会员积分管理
 */
class Vip_point extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Vip_point']);
    }

    function index_get(){#会员数据
        $begin_date = trim($this->get('begin_date',true));#开始月
        $end_date = trim($this->get('end_date',true));#结束月

        $data = $this->v1_vip_point->vip_list(['begin_date'=>$begin_date,'end_date'=>$end_date]);

    	$this->respond(array('status'=>true,'tips'=>'拉取信息成功','data'=>$data),HTTP_OK);
    }

    function jifen_get(){#积分排行榜
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
        $begin_date = intval($this->get('begin_date',true));#开始日期
        $end_date = intval($this->get('end_date',true));#结束日期
        $keyword = safe_replace(trim($this->get('keyword',true)));#关键词

        if(!$per_page) $per_page = 10;#PHP_INT_MAX;
        $this->v1_vip_point->set_page($page,$per_page);
        $this->v1_vip_point->set_filter(['group_id'=>$group_id,'keyword'=>$keyword]);
        $data = $this->v1_vip_point->jifen_list();
        
        $this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data,
                        'data_count'=>intval($this->User_model->data_count),
                        'page_count'=>intval($this->User_model->page_count),
                        'page_index'=>$this->User_model->page_index,
                        'page_size'=>$this->User_model->page_size],HTTP_OK);
    }

    function parking_jifen_get(){#停车场积分
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
        $begin_date = intval($this->get('begin_date',true));#开始日期
        $end_date = intval($this->get('end_date',true));#结束日期
        $keyword = safe_replace(trim($this->get('keyword',true)));#关键词

        if(!$per_page) $per_page = 10;
        $this->v1_vip_point->set_page($page,$per_page);
        $this->v1_vip_point->set_filter(['group_id'=>$group_id,'keyword'=>$keyword]);
        $data = $this->v1_vip_point->jifen_list();
        
        $this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data,
                        'data_count'=>intval($this->User_model->data_count),
                        'page_count'=>intval($this->User_model->page_count),
                        'page_index'=>$this->User_model->page_index,
                        'page_size'=>$this->User_model->page_size],HTTP_OK);
    }

}   