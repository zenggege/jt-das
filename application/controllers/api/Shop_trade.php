<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 店铺销售额管理
 */
class Shop_trade extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Shop_trade']);
    }

    /**
     * 上传EXCEL
     */
    function upload_excel_post(){
        $result = $this->v1_shop_trade->shop_trade_excel($this->admin_id);
        if($result){
            $this->respond(['status'=>true,'tips'=>'上传成功','data'=>$result],HTTP_CREATED);
        }
        $this->respond(['status'=>false,'tips'=>$this->v1_shop_trade->tips],HTTP_BAD_REQUEST);
    }

    /**
     * 导入店铺销售额数据
     */

    function import_post(){
        $goods_arr = $this->post('goods_arr',true); 

        $result = $this->v1_shop_trade->shop_trade_import_data($goods_arr);
        if($result){
            $this->respond(array('status'=>true,'tips'=>'导入成功'),HTTP_OK);
        }else{
            $this->respond(array('status'=>false,'tips'=>$this->v1_shop_trade->tips),HTTP_BAD_REQUEST);
        }      
    }

}    