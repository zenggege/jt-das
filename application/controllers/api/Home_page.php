<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 首页接口管理
 */
class Home_page extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Home_page']);
    }

    function all_shops_get(){#统计所有店铺
        $data = $this->v1_home_page->get_all_shops();
    	$this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function expire_shop_get(){#即将到期店铺
        $data = $this->v1_home_page->get_expire_shop();
		$this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function all_vip_get(){#统计所有会员
        $data = $this->v1_home_page->get_all_vip();
    	$this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function add_vip_get(){#本月新增会员
        $data = $this->v1_home_page->get_add_vip();
    	$this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function turnover_get(){#营业额
        $data = $this->v1_home_page->get_turnover();
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function subaverage_get(){#低于平均值
        $data = $this->v1_home_page->get_subaverage();
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));        
    }

    function passenger_flow_get(){#客流
        $data = $this->v1_home_page->get_passenger_flow();
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function average_passenger_flow_get(){#平均客流
        $data = $this->v1_home_page->get_average_passenger_flow();
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>$data));
    }

    function target_get(){#某年目标
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>[]));
    }

    function set_up_get(){#设置
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>[]));
    }

    function sales_get(){#某年销售额
        $this->respond(array('status'=>true,'tips'=>'数据获取成功','data'=>[]));
    }


    function  all_count_get(){#前端要求一个接口返回
        $data = $this->v1_home_page->count();
        $this->respond(array('status'=>true,
                             'tips'=>'数据获取成功',
                             'all_shops'=>$data['all_shops'],
                             'expire_shops'=>$data['expire_shops'],
                             'all_vip'=>$data['all_vip'],
                             'add_vip'=>$data['add_vip'],
                             'turnover'=>$data['turnover'],
                             'subaverage'=>$data['subaverage'],
                             'passenger_flow'=>$data['passenger_flow'],
                             'average_passenger_flow'=>$data['average_passenger_flow']
                             ));
    }


}    