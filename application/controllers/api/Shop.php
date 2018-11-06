<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 店铺管理
 */
class Shop extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Shop']);
    }

    /**
     * 店铺列表
     */
    function index_get(){
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
        $keyword = safe_replace(trim($this->get('keyword',true)));#关键词

        if(!$per_page) $per_page = 10;
        $this->v1_shop->set_page($page,$per_page);
        $this->v1_shop->set_filter(['keyword'=>$keyword]);
        $data = $this->v1_shop->shop_list();
        
        $this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data,
                        'data_count'=>intval($this->Shop_model->data_count),
                        'page_count'=>intval($this->Shop_model->page_count),
                        'page_index'=>$this->Shop_model->page_index,
                        'page_size'=>$this->Shop_model->page_size],HTTP_OK);

    }

    /**
     * 单个店铺详细
     */
    function info_get($shop_id=0){
        $shop_id = intval($shop_id);
        $data = $this->v1_shop->get_shop_info($shop_id);
        if($data){
            $this->respond(['status'=>true,'tips'=>$this->v1_shop->tips,'data'=>$data],HTTP_OK);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_shop->tips,'data'=>$data],HTTP_BAD_REQUEST);
        }
    }

    /**
     * 新增店铺
     */
    function info_post(){
        $shop_type = trim($this->post('shop_type',true));#店铺业态
        $brand_name = trim($this->post('brand_name',true));#品牌全称
        $abbreviation = trim($this->post('abbreviation',true));#品牌简称
        $area_no = trim($this->post('area_no',true));#铺位号
        $contract_no = trim($this->post('contract_no',true));#合同号
        $rent_fee = floatval($this->post('rent_fee',true));#租金
        $property_fee = floatval($this->post('property_fee',true));#物业
        $area_size = trim($this->post('area_size',true));#店铺面积
        #高级信息
        $contract_period = trim($this->post('contract_period',true));#合同期
        $is_not_rent = trim($this->post('is_not_rent',true));#是否退租
        $min_month_turnover = trim($this->post('min_month_turnover',true));#最低月营业额度

        $arr = compact('shop_type','brand_name','abbreviation','area_no','contract_no','rent_fee','property_fee','area_size','contract_period','is_not_rent','min_month_turnover');
        $result = $this->v1_shop->add($arr);      
        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_shop->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_shop->tips],HTTP_BAD_REQUEST);
        }
    }

    /**
     * 修改店铺
     */
    function info_put(){
        $shop_id = intval($this->put('shop_id',true));#店铺ID
        $shop_type = trim($this->put('shop_type',true));#店铺业态
        $brand_name = trim($this->put('brand_name',true));#品牌全称
        $abbreviation = trim($this->put('abbreviation',true));#品牌简称
        $area_no = trim($this->put('area_no',true));#铺位号
        $contract_no = trim($this->put('contract_no',true));#合同号
        $rent_fee = floatval($this->put('rent_fee',true));#租金
        $property_fee = floatval($this->put('property_fee',true));#物业
        $area_size = trim($this->put('area_size',true));#店铺面积
        #高级信息
        $contract_period = trim($this->put('contract_period',true));#合同期
        $is_not_rent = trim($this->put('is_not_rent',true));#是否退租
        $min_month_turnover = trim($this->put('min_month_turnover',true));#最低月营业额度

        $arr = compact('shop_id','shop_type','brand_name','abbreviation','area_no','contract_no','rent_fee','property_fee','area_size','contract_period','is_not_rent','min_month_turnover');
        $result = $this->v1_shop->update($arr);      
        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_shop->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_shop->tips],HTTP_BAD_REQUEST);
        }

    }

    /**
     * 删除店铺
     */
    function info_delete($shop_id=0){
        $shop_id = intval($shop_id);
        $status = $this->v1_shop->delete($shop_id);
        if($status){
            $this->respond(['status'=>true,'tips'=>$this->v1_shop->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_shop->tips],HTTP_BAD_REQUEST);
        }
    }

}