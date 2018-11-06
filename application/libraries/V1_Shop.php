<?php
/**
 * 店铺类
 * 
 */
class V1_Shop  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Shop_model'));
    }


    // /**
    //  * 新增店铺数据
    //  * @param $arr 店铺数据
    //  */
    // public function add($arr)
    // {
    //     return true;
    // }

    // /**
    //  * 修改店铺数据
    //  * @param $arr 店铺数据
    //  */
    // public function update($arr)
    // {
    //     return true;
    // }


    // /**
    //  * 删除店铺数据
    //  */
    // public function delete()
    // {
    //     return true;
    // }

    // /**
    //  * 过滤店铺数据参数
    //  * @param $arr 店铺数据
    //  */
    // public function filter($arr)
    // {
    //     return true;
    // }

    // /**
    //  * 搜索店铺数据
    //  */
    // public function search()
    // {
    //     return true;
    // }

    /**
    * 设置分页
    *@param $page int 当前页码
    *@param $per_page int 每页数量
    */
    function set_page($page,$per_page){
        $this->CI->Shop_model->page_index = max(intval($page),1);
        $this->CI->Shop_model->page_size = min(intval($per_page),100);        
    }

    /**
    * 设置分页
    *@param $filter_arr array 
    */
    function set_filter($filter_arr=[]){
        $where_arr = NULL;
        if(isset($filter_arr['role_id']) && $filter_arr['role_id'] >0){
            $where_arr[] = "role_id = {$filter_arr['role_id']}";
        }

        if(isset($filter_arr['keyword']) && $filter_arr['keyword'] != ''){
            $where_arr[] = "((contractno like '%{$filter_arr['keyword']}%') or (brandname like '%{$filter_arr['keyword']}%') or (areano like '%{$filter_arr['keyword']}%'))";
        }
        $this->where = $where_arr?implode(" and ",$where_arr):NULL;
    }

    /**
    * 店铺列表
    */
    function shop_list($fields = '*',$order_by = 'shopid asc'){
        $list_info = $this->CI->Shop_model->select($this->where,$fields,$order_by);

        return $list_info;
    }



    function add($arr){
        extract($arr);
        if($shop_type == ''){
            $this->tips = '店铺业态不能为空';
            return FALSE;
        } 

        if($brand_name == ''){
            $this->tips = '品牌全称不能为空';
            return FALSE;
        }  

        if($area_no == ''){
            $this->tips = '铺位号不能为空';
            return FALSE;
        }        

        if($contract_no == ''){
            $this->tips = '合同号不能为空';
            return FALSE;
        }         

        if($this->CI->Shop_model->count(['contractno'=>$contract_no])){
            $this->tips = '合同号重复，请确保是正确合同号';
            return FALSE;
        }

        if($rent_fee <= 0){
            $this->tips = '租金无效';
            return FALSE;
        }           

        if($property_fee <= 0){
            $this->tips = '物业费无效';
            return FALSE;
        }             

        if($area_size <= 0){
            $this->tips = '店铺面积不能为空';
            return FALSE;
        }  

        $new_id = $this->CI->Shop_model->insert(['brandname'=>$brand_name,
                                                 'areano'=>$area_no,
                                                 'rentfee'=>$rent_fee,
                                                 'contractno'=>$contract_no,
                                                 'propertyfee'=>$property_fee,
                                                 'shoptype'=>$shop_type,
                                                 'areasize'=>$area_size,
                                                 'brandname2'=>$abbreviation
                                                ]);            
        if($new_id){
            $this->tips = '新增成功';
            return TRUE;
        }else{
            $this->tips = '新增失败';
            return FALSE;
        }         
    }


    function update($arr){
        extract($arr);
        $info = $this->CI->Shop_model->one(['shopid'=>$shop_id]);
        if(!$info){
            $this->tips = '信息不存在';
            return FALSE;
        }

        if($shop_type == ''){
            $this->tips = '店铺业态不能为空';
            return FALSE;
        } 

        if($brand_name == ''){
            $this->tips = '品牌全称不能为空';
            return FALSE;
        }  

        if($area_no == ''){
            $this->tips = '铺位号不能为空';
            return FALSE;
        }        

        if($contract_no == ''){
            $this->tips = '合同号不能为空';
            return FALSE;
        }  

        if($contract_no != $info['contractno']){
            if($this->CI->Shop_model->count(['contractno'=>$contract_no])){
                $this->tips = '合同号重复，请确保是正确合同号';
                return FALSE;
            }            
        }       

        if($rent_fee <= 0){
            $this->tips = '租金无效';
            return FALSE;
        }           

        if($property_fee <= 0){
            $this->tips = '物业费无效';
            return FALSE;
        }             

        if($area_size <= 0){
            $this->tips = '店铺面积不能为空';
            return FALSE;
        } 

        $status = $this->CI->Shop_model->update(['brandname'=>$brand_name,
                                                 'areano'=>$area_no,
                                                 'rentfee'=>$rent_fee,
                                                 'contractno'=>$contract_no,
                                                 'propertyfee'=>$property_fee,
                                                 'shoptype'=>$shop_type,
                                                 'areasize'=>$area_size,
                                                 'brandname2'=>$abbreviation
                                                ],['shopid'=>$shop_id]);
        if($status){
            $this->tips = '修改成功';
            return TRUE;
        }else{
            $this->tips = '修改失败';
            return FALSE;
        }         
    }


    function delete($shop_id){
        $info = $this->CI->Shop_model->one(['shopid'=>$shop_id]);
        if(!$info){
            $this->tips = '信息不存在';
            return FALSE;
        }

        $status = $this->CI->Shop_model->delete(['shopid'=>$shop_id]);
        if($status){
            $this->tips = '删除成功';
            return TRUE;             
        }else{
            $this->tips = '删除失败';
            return FALSE;   
        }        
    }


    function get_shop_info($shop_id){
        $info = $this->CI->Shop_model->one(['shopid'=>$shop_id]);
        if(!$info){
            $this->tips = '信息不存在';
            return NULL;
        }else{
            $data['shop_id'] = $info['shopid']; 
            $data['shop_type'] = $info['shoptype']; 
            $data['brand_name'] = $info['brandname']; 
            $data['abbreviation'] = $info['brandname2']; 
            $data['area_no'] = $info['areano']; 
            $data['contract_no'] = $info['contractno']; 
            $data['rent_fee'] = $info['rentfee'];
            $data['property_fee'] = $info['propertyfee'];
            $data['area_size'] = $info['areasize'];
            $data['contract_period'] = 0;
            $data['is_not_rent'] = $info['closed'];
            $data['min_month_turnover'] = 0;

            $this->tips = '数据获取成功';
            return $data;
        }        
    }

}