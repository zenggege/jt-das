<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 客流管理
 */
class Passenger_flow extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Passenger_flow']);
    }
    
    /**
     * 客流列表
     */
    function index_get(){
        $begin_date = trim($this->get('begin_date',true));#开始月
        $end_date = trim($this->get('end_date',true));#结束月

        $data = $this->v1_passenger_flow->passenger_list($begin_date,$end_date);

        $this->respond(array('status'=>true,'tips'=>'拉取信息成功','data'=>$data),HTTP_OK);

    }

    /**
     * 上传EXCEL
     */
    function upload_excel_post(){
        $result = $this->v1_passenger_flow->passenger_flow_excel($this->admin_id);
        if($result){
            $this->respond(['status'=>true,'tips'=>'上传成功','data'=>$result],HTTP_CREATED);
        }
        $this->respond(['status'=>false,'tips'=>$this->v1_passenger_flow->tips],HTTP_BAD_REQUEST);
    }
     /**
     * 导入客流数据
     */
  
    function import_post(){
        $goods_arr = $this->post('goods_arr',true);

        $result = $this->v1_passenger_flow->passenger_flow_import_data($goods_arr);
        if($result){
            $this->respond(array('status'=>true,'tips'=>'导入成功'),HTTP_OK);
        }else{
            $this->respond(array('status'=>false,'tips'=>$this->v1_passenger_flow->tips),HTTP_BAD_REQUEST);
        }      
    } 
}    