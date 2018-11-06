<?php
/**
 * 首页接口管理类
 * 
 */
class V1_Home_page  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Shop_model','Vip_model','Shop_trade_model','Passenger_flow_model'));
    }

    function get_all_shops(){#统计所有店铺
    	$count_shops = $this->CI->Shop_model->count();
    	return $count_shops;
    }

    function get_expire_shop(){#即将到期店铺
    	$count_expire = 0;
    	return $count_expire;
    }

    function get_all_vip(){#统计所有会员
    	$count_vips = $this->CI->Vip_model->count();
    	return $count_vips;
    }

    function get_add_vip(){#本月新增会员
    	#得到今天日期
    	$today = SYS_DATE;
    	$first_day = date('Y-m-01',strtotime($today)); 
    	$first_day = $first_day.' 00:00:00';
    	$sql = "`created` BETWEEN '".$first_day."' AND '".$today."'";
    	$count = $this->CI->Vip_model->count($sql);
    	return $count;
    }

    function get_turnover(){#营业额
        $total = $this->CI->Shop_trade_model->sum('subtotal2');
        return $total;
    }

    function get_subaverage(){#低于平均值
        #先统计当月营业额
        #得到今天日期
        $today = date('Y-m-d',SYS_TIME);
        $first_day = date('Y-m-01',strtotime($today));
        #得到本月总营业额
        $sql = "`created` BETWEEN '".$first_day."' AND '".$today."'";
        $this_month_total = $this->CI->Shop_trade_model->sum('subtotal2',$sql);
        #再统计店铺数量
        $shop_count = $this->CI->Shop_model->count();
        #得到本月平均营业额
        $average = round(($this_month_total/$shop_count),2);
        #最后统计
        $sql1 = $sql." AND subtotal2 < ".$average;
        $count = $this->CI->Shop_trade_model->count($sql1);

        return $count;
    }

    function get_passenger_flow(){#客流
        $count = $this->CI->Passenger_flow_model->sum('num');
        return $count;
    }


    function get_average_passenger_flow(){#平均客流
        #先统计有多少条客流记录
        $count = $this->CI->Passenger_flow_model->count();
        #再得到所有客流总和
        $all = $this->get_passenger_flow();
        $result = round(($all/$count),1);

        return $result;
    }

    function count(){#前端要求一个接口返回
        $count_shops = $this->CI->Shop_model->count();#统计所有店铺
        $count_expire = 0;#即将到期店铺

        $count_vips = $this->CI->Vip_model->count();#统计所有会员
        $today = SYS_DATE;
        $first_day = date('Y-m-01',strtotime($today)); 
        $first_day = $first_day.' 00:00:00';
        $sql = "`created` BETWEEN '".$first_day."' AND '".$today."'";
        $add_count = $this->CI->Vip_model->count($sql);#本月新增会员

        $total = $this->CI->Shop_trade_model->sum('subtotal2');#营业额
        #先统计当月营业额
        #得到今天日期
        $today1 = date('Y-m-d',SYS_TIME);
        $first_day1 = date('Y-m-01',strtotime($today1));
        #得到本月总营业额
        $sql1 = "`created` BETWEEN '".$first_day1."' AND '".$today1."'";
        $this_month_total = $this->CI->Shop_trade_model->sum('subtotal2',$sql);
        #再统计店铺数量
        $shop_count = $this->CI->Shop_model->count();
        #得到本月平均营业额
        $average = round(($this_month_total/$shop_count),2);
        #最后统计
        $sql2 = $sql1." AND subtotal2 < ".$average;
        $subaverage_count = $this->CI->Shop_trade_model->count($sql2);#低于平均值


        $passenger_flow_count = $this->CI->Passenger_flow_model->sum('num');#客流
        #先统计有多少条客流记录
        $count = $this->CI->Passenger_flow_model->count();
        $result = round(($passenger_flow_count/$count),1);#平均客流

        return ['all_shops'=>$count_shops,
                'expire_shops'=>$count_expire,
                'all_vip'=>$count_vips,
                'add_vip'=>$add_count,
                'turnover'=>$total,
                'subaverage'=>$subaverage_count,
                'passenger_flow'=>$passenger_flow_count,
                'average_passenger_flow'=>$result];
        
    }

}    