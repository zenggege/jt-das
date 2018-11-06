<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 分析类 管理各种销售额数据
 */
class Analysis extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Analysis']);
    }

    function index_get(){#首页/营业额
    	$date1 = trim($this->get('date1',true));#第一个月
    	$date2 = trim($this->get('date2',true));#第二个月

    	$data = $this->v1_analysis->turnover($date1,$date2);
    	$data && $this->respond(['status'=>true,'tips'=>'数据获取成功','data1'=>$data['one'],'data2'=>$data['two']],HTTP_OK);
    	$this->respond(['status'=>false,'tips'=>$this->v1_analysis->tips],HTTP_BAD_REQUEST);
    	

    }

    function all_details_get(){#日报所有商家明细
    	$date = trim($this->get('date',true));#日期

    	$data = $this->v1_analysis->get_all_details($date);
    	$this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data['data'],
                        'subtotal'=>$data['subtotal'],
                        'average'=>$data['average'],
                        'working_day'=>$data['working_day'],
                        'rest_day'=>$data['rest_day'],
                        'maximum_sales'=>$data['maximum_sales'],
                        'date'=>$data['date']
                        ],HTTP_OK);
    }

    function single_details_get(){#单个商家明细
    	$id = intval($this->get('id',true));#商铺ID
    	$month1 = trim($this->get('month1',true));#第一个月
    	$month2 = trim($this->get('month2',true));#第二个月 

    	$data = $this->v1_analysis->get_single_details($id,$month1,$month2);
    	$this->respond(['status'=>true,
    				    'tips'=>'数据获取成功',
    				    'data'=>$data['data'],
                        'month'=>$data['month'],
    				    'subtotal'=>$data['subtotal'],
    				    'average'=>$data['average'],
    				    'working_day'=>$data['working_day'],
    				    'rest_day'=>$data['rest_day'],
    				    'maximum_sales'=>$data['maximum_sales'],
    				    ],HTTP_OK);
    }

    function contrast_get(){#多商家数据对比
    	$shop_id1 = intval($this->get('shop_id1',true));#第一个商铺ID
    	$shop_id2 = intval($this->get('shop_id2',true));#第二个商铺ID
    	$year = trim($this->get('year',true));#年

        $data = $this->v1_analysis->get_contrast($shop_id1,$shop_id2,$year);

    	$this->respond(['status'=>true,'tips'=>'数据获取成功','data'=>$data]);
    }

    function monthly_sales_get(){#月销售额
    	$month1 = trim($this->get('month1',true));#第一个月
    	$month2 = trim($this->get('month2',true));#第二个月

    	$data = $this->v1_analysis->get_monthly_sales($month1,$month2);
    	$this->respond(['status'=>true,
    					'tips'=>'数据获取成功',
    					// 'data'=>$data['data'],
    					'avg'=>$data['avg'],
    					'avg_onetofive'=>$data['avg_onetofive'],
    					'avg_sixtoseven'=>$data['avg_sixtoseven'],
    					'max'=>$data['max'],
    					'm'=>$data['m']
    				   ],HTTP_OK);
    }

    function floor_sales_get(){#楼层销售额
    	$month1 = trim($this->get('month1',true));#第一个月
    	$month2 = trim($this->get('month2',true));#第二个月

    	$data = $this->v1_analysis->get_floor_sales($month1,$month2);
    	$this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data['data'],
                        'month'=>$data['month']
                       ],HTTP_OK);    	
    }

    function shoptype_sales_get(){#业态销售额
        $month1 = trim($this->get('month1',true));#第一个月
        $month2 = trim($this->get('month2',true));#第二个月

        $data = $this->v1_analysis->get_shoptype_sales($month1,$month2);
    	$this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data['data'],
                        'month'=>$data['month']
                       ],HTTP_OK);
    }

    function shop_sales_get(){#店铺营业额
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
    	$date1 = trim($this->get('date1',true));#第一个日期
    	$date2 = trim($this->get('date2',true));#第二个日期
    	$keyword = trim($this->get('keyword',true));#关键词
    	$shop_id = intval($this->get('shop_id',true));#店铺ID

    	if(!$per_page) $per_page = 10;
    	$data = $this->v1_analysis->get_shop_sales($page,$per_page,$date1,$date2,$keyword,$shop_id);
  	
    	$this->respond(['status'=>true,
    					'tips'=>'数据获取成功',
    					'data'=>$data,
                        'data_count'=>intval($this->Shop_model->data_count),
                        'page_count'=>intval($this->Shop_model->page_count),
                        'page_index'=>$this->Shop_model->page_index,
                        'page_size'=>$this->Shop_model->page_size],HTTP_OK);
    }

    function rankings_get(){#坪效排行
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
    	$date1 = trim($this->get('date1',true));#第一个日期
    	$date2 = trim($this->get('date2',true));#第二个日期
    	$shop_id = intval($this->get('shop_id',true));#店铺ID

    	if(!$per_page) $per_page = 10;
    	$data = $this->v1_analysis->get_rankings($page,$per_page,$date1,$date2,$shop_id);

    	$this->respond(['status'=>true,
    					'tips'=>'数据获取成功',
    					'data'=>$data,
                        'data_count'=>intval($this->Shop_model->data_count),
                        'page_count'=>intval($this->Shop_model->page_count),
                        'page_index'=>$this->Shop_model->page_index,
                        'page_size'=>$this->Shop_model->page_size],HTTP_OK);

    }

    function lease_sale_get(){#租售比分析
        $page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
    	$date1 = trim($this->get('date1',true));#第一个日期
    	$date2 = trim($this->get('date2',true));#第二个日期

    	if(!$per_page) $per_page = 10;
    	$data = $this->v1_analysis->get_lease_sale($page,$per_page,$date1,$date2);

    	$this->respond(['status'=>true,
    					'tips'=>'数据获取成功',
    					'data'=>$data,
                        'data_count'=>intval($this->Shop_model->data_count),
                        'page_count'=>intval($this->Shop_model->page_count),
                        'page_index'=>$this->Shop_model->page_index,
                        'page_size'=>$this->Shop_model->page_size],HTTP_OK);

    }
}    