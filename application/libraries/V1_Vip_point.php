<?php
/**
 * 会员管理
 * 
 */
class V1_Vip_point  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Vip_model'));
    }

    function vip_list($filter_arr=[]){
        $where_arr = NULL;
        if(isset($filter_arr['begin_date'])) {
            
            $where_start_time = strtotime($filter_arr['begin_date']) ? strtotime($filter_arr['begin_date']) : 0;
            $where_arr[]= "`created` >= '".date('Y-m-d H:i:s',$where_start_time)."'";
        }

        //设置搜索结束时间段
        if(isset($filter_arr['end_date'])) {
           
            $where_end_time = strtotime($filter_arr['end_date']) ? strtotime($filter_arr['end_date'])  : time();
            $where_arr[]= "`created` <= '".date('Y-m-d H:i:s',$where_end_time)."'";
        } 
        $this->where = $where_arr?implode(" and ",$where_arr):NULL;
        
        $this->CI->Vip_model->page_size = PHP_INT_MAX;
    	$list_info = $this->CI->Vip_model->select($this->where,'*','vip_id asc');
        // $data_list = NULL;
        $data_list['arr']['january'] = $data_list['arr']['february'] =  $data_list['arr']['march'] =$data_list['arr']['april'] = $data_list['arr']['may'] = $data_list['arr']['june'] = $data_list['arr']['july'] = $data_list['arr']['august'] = $data_list['arr']['september'] = $data_list['arr']['october'] = $data_list['arr']['november'] = $data_list['arr']['december'] = 0;

        if($list_info){
            foreach ($list_info as $key => $value) {
                switch (date('m',strtotime($value['created']))) {
                    case '01':
                        $data_list['arr']['january'] += 1;  
                        break;
                    case '02':
                        $data_list['arr']['february'] += 1;  
                        break;
                    case '03':
                        $data_list['arr']['march'] += 1;  
                        break;
                    case '04':
                        $data_list['arr']['april'] += 1;  
                        break;
                    case '05':
                        $data_list['arr']['may'] += 1;  
                        break;
                    case '06':
                        $data_list['arr']['june'] += 1;  
                        break;
                    case '07':
                        $data_list['arr']['july'] += 1;  
                        break;
                    case '08':
                        $data_list['arr']['august'] += 1;  
                        break;
                    case '09':
                        $data_list['arr']['september'] += 1;  
                        break;
                    case '10':
                        $data_list['arr']['october'] += 1;  
                        break;
                    case '11':
                        $data_list['arr']['november'] += 1;  
                        break;
                    case '12':
                        $data_list['arr']['december'] += 1;  
                        break;                    
                    default:
                        # code...
                        break;
                }
            }
        }

    	$count = $this->CI->Vip_model->count();
    	$data_list['count'] = $count;
        $_data_list = NULL;
        if($data_list)
        foreach ($data_list as $key => $value) {
            $_data_list[]= $value;
        }

    	return $_data_list;
    }

}    