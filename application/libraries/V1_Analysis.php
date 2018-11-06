<?php
/**
 * 分析类
 * 
 */
class V1_Analysis  {
    var $CI;
    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Shop_model','Shop_trade_model','VW_Shop_trade_model'));
		$this->CI->load->helper(array('jt_admin'));
    }

    function turnover($date1,$date2){#营业额
    	if($date1 == '' || !is_month(date('Y-m',strtotime($date1)))){
    		$this->tips = '第一个月无效';
    		return FALSE;
    	}

    	if($date2 == '' || !is_month(date('Y-m',strtotime($date2)))){
    		$this->tips = '第二个月无效';
    		return FALSE;
    	}

    	if(substr($date1,0,7) == substr($date2,0,7)){
    		$this->tips = '两个月不能相同';
    		return FALSE;
    	}

    	$date1 = date('Y-m',strtotime(substr($date1,0,7)));
    	$date2 = date('Y-m',strtotime(substr($date2,0,7)));

    	#统计这两个月份每天的销售额
    	$day1 = date('t',strtotime($date1));
    	$day2 = date('t',strtotime($date2));

    	$_data1 = $_data2 =NULL;
    	$data1 = $data2 = NULL;
    	for ($i=1; $i<=$day1 ; $i++) { 
    		$_date = ($i < 10)?$date1.'-0'.$i:$date1.'-'.$i;
    		$data1['date'] = $_date;
    		$sql = "`created` BETWEEN '".$_date.' 00:00:00'."' AND '".$_date.' 23:59:59'."'";
    		$data1['total'] = $this->CI->Shop_trade_model->sum('subtotal2',$sql);
    		$_data1[] = $data1;
    	}

    	for ($i=1; $i<=$day2 ; $i++) { 
    		$_date = ($i < 10)?$date2.'-0'.$i:$date2.'-'.$i;
    		$data2['date'] = $_date;
    		$sql = "`created` BETWEEN '".$_date.' 00:00:00'."' AND '".$_date.' 23:59:59'."'";
    		$data2['total'] = $this->CI->Shop_trade_model->sum('subtotal2',$sql);
    		$_data2[] = $data2;
    	}


    	return ['one'=>$_data1,'two'=>$_data2];

    }


    function get_all_details($date){#日报所有商家明细
		if(!is_date($date))$date = date("Y-m-d",strtotime("-1 days"));
		$_dataListArr  = $this->CI->VW_Shop_trade_model->select(array('created'=>$date),'brandname,areano,shopid,subtotal2,created','areano ASC,subtotal2 desc');

		$dataListArr =$finalListArr = $data = array();
		if($_dataListArr)		
		foreach($_dataListArr as $k=>$v)
		{
			$floorNO = substr($v['areano'],0,4);
			$dataListArr[$floorNO][]= $v;
		}

		$n= 1;
		$subtotal=  0;
		$onetofive=0;
		$sub_onetofive=0;
		  
		$sixtoseven=0;
		$sub_sixtoseven=0;
		$max_total=0;
		if($dataListArr)
		foreach($dataListArr as $k=>$v)
		{

			$sort_arr = array();
			$sum_sub_total=  0;
			if($v)
			foreach ($v as $key => $row) {

				$row['subtotal2'] = $row['subtotal2']>=0?$row['subtotal2']:$row['subtotal'];
				
				$row['subtotal2'] = max($row['subtotal2'],0);
				
				$yesterday_info =   $this->CI->VW_Shop_trade_model->one(array('shopid'=>$row['shopid'],'created'=>date("Y-m-d",strtotime($row['created']." -1 days"))),'*','areano ASC,subtotal2 desc');
				$v[$key]['yester_subtotal2'] = 0;
				if($yesterday_info)
				{
					$v[$key]['yester_subtotal2'] = max( $yesterday_info['subtotal2']>0?$yesterday_info['subtotal2']:$yesterday_info['subtotal'],0);
				}
				
				
				$n += 1;
				$max_total= max($max_total,$row['subtotal2']);
				if(is_work_day($row['created']))
				{
					 $sub_onetofive += $row['subtotal2'];
					 $onetofive++;
				}else
				{
					 $sub_sixtoseven += $row['subtotal2'];
					 $sixtoseven++;
				}

				$sort_arr[$key] = $row['subtotal2'];
				$sum_sub_total +=  $row['subtotal2'];
				$row['subtotal2'] = number_format($row['subtotal2'],2,'.',',');
				$row['yester_subtotal2'] = number_format($v[$key]['yester_subtotal2'],2,'.',',');
				$data[] = $row;
			}
			$subtotal += $sum_sub_total;
			if($v)
			{	
				array_multisort($sort_arr, SORT_DESC,  $v); 
				$_finalListArr['total'] = number_format($sum_sub_total,2,'.',',');
				$_finalListArr['areano'] = $k;
				$_finalListArr['count'] = count($v);
				$_finalListArr['list'] = $data;
			}
			$finalListArr[] = $_finalListArr;
			unset($data);
		}

		$arr = ['subtotal'=>number_format($subtotal, 2, '.', ','),#区间合计
				'average'=>$subtotal?number_format(($subtotal/($n-1)), 2, '.', ','):'0.00',#区间日均
				'working_day'=>$onetofive?number_format(($sub_onetofive/$onetofive), 2, '.', ','):'0.00',#区间工作日均
				'rest_day'=>$sixtoseven?number_format(($sub_sixtoseven/$sixtoseven), 2, '.', ','):'0.00',#区间休息日均
				'maximum_sales'=>number_format($max_total, 2, '.', ','),#最高销售额
				'data'=>$finalListArr,
				'date'=>$date
			   ];
		return $arr;
    }


    function get_single_details($id,$month1,$month2){#单个商家明细
		$month1 = is_month($month1)?$month1:date('Y-m',strtotime("-1 month"));
		$month2 = is_month($month2)?$month2:date('Y-m');
		// echo $month1;
		// echo "\r\n";
		// echo $month2;
		// die();

		$data = NULL;
		$datainfo  = $this->CI->Shop_model->one(array('shopid'=>$id));
		for ($i=1; $i<=31; $i++) {
			$_date = ($i < 10)?$month1.'-0'.$i:$month1.'-'.$i;
			$sql = "`created` = '".$_date."' AND shopid = {$datainfo['shopid']}"; 
			$total = $this->CI->Shop_trade_model->sum('subtotal2',$sql);
			// echo $this->CI->Shop_trade_model->last_query();
			// echo "\r\n";
			// die();

			$_date2 = ($i < 10)?$month2.'-0'.$i:$month2.'-'.$i;
			$sql2 = "`created` = '".$_date2."' AND shopid = {$datainfo['shopid']}"; 
			$total2 = $this->CI->Shop_trade_model->sum('subtotal2',$sql2);
			// echo $this->CI->Shop_trade_model->last_query();
			// die();
			$_data['date'] = ($i < 10)?'0'.$i:$i;
			$_data['arr'][] = $total;
			$_data['arr'][] = $total2;
			$data[] = $_data;
			unset($_data);
		}
		// print_r($data);
		// die();
		

		// $this->CI->Shop_trade_model->page_size = PHP_INT_MAX;
		// $dataList = $this->CI->Shop_trade_model->select(array('shopid'=>$datainfo['shopid'],'created >='=>$startDate,'created <='=>$endDate),'shopid,created,subtotal,subtotal2','created desc');
		// // echo $this->CI->Shop_trade_model->last_query();
		// // die();
		// $n= 1;
		// $subtotal =0;
		// $onetofive=0;
		// $sub_onetofive=0;
		  
		// $sixtoseven=0;
		// $sub_sixtoseven=0;
		// $max_total=0;
		// foreach($dataList as $v){
		// 	$precentage = $v['subtotal2']>0?((abs($v['subtotal']-$v['subtotal2'])/max($v['subtotal2'],$v['subtotal']))*100):0;
		// 	$n += 1;
		// 	$max_total= max($max_total,$v['subtotal2']);
		// 	// echo $max_total;
		// 	// die();
		// 	$subtotal += $v['subtotal2'];
			
		// 	if(is_work_day($v['created']))
		// 	{
		// 		 $sub_onetofive += $v['subtotal2'];
		// 		 $onetofive++;
		// 	}else
		// 	{
		// 		 $sub_sixtoseven += $v['subtotal2'];
		// 		 $sixtoseven++;
		// 	}
		// }
		// $arr = ['subtotal'=>number_format($subtotal, 2, '.', ','),#区间合计
				// 'average'=>number_format(($subtotal/($n-1)), 2, '.', ','),#区间日均
				// 'working_day'=>$onetofive?number_format(($sub_onetofive/$onetofive), 2, '.', ','):0,#区间工作日均
				// 'rest_day'=>$sixtoseven?number_format(($sub_sixtoseven/$sixtoseven), 2, '.', ','):0,#区间休息日均
				// 'maximum_sales'=>number_format($max_total, 2, '.', ','),#最高销售额
				// 'data'=>$data
			   // ];
		$arr = ['data'=>$data,
				'month'=>[$month1,$month2]
			   ];
		return $arr;
    }

    function get_contrast($shop_id1,$shop_id2,$year){#多商家数据对比
    	$shop_id1 = $shop_id1?$shop_id1:1;
    	$shop_id2 = $shop_id2?$shop_id:2;
    	$year = is_year($year)?$year:date('Y');
    	echo $shop_id1;
    	echo "\r\n";
    	echo $shop_id2;
    	echo "\r\n";
    	echo $year;
    	die();

    	#首先找到两个商铺的信息
    	$shop_info1 = $this->CI->Shop_model->one(['shopid'=>$shop_id1]);
    	$shop_info2 = $this->CI->Shop_model->one(['shopid'=>$shop_id2]);



    }



    function get_monthly_sales($month1,$month2){#月销售额
		$month1 = is_month($month1)?$month1:date('Y-m',strtotime("-1 year +1 day"));
		$month2 = is_month($month2)?$month2:date('Y-m');

    	#统计这两个月份每天的销售额
    	$day1 = date('t',strtotime($month1));
    	$day2 = date('t',strtotime($month2));
		//按月
		// $startDate = '2015-01-01';
		// $endDate = date('Y-m-d');
		$sql ="SELECT created,sum(subqty2) as sumqty,sum(subtotal2) as sumtotal FROM vw_sys_shop_trade WHERE created>='{$month1}' and created<='{$month2}' group by created order by created asc";
		$_datalist = $this->CI->VW_Shop_trade_model->query($sql);

		$new_data = $_new_data = $data =  array();

		$avg = $avg_onetofive = $avg_sixtoseven = $max = $m = NULL;
		if($_datalist)
		foreach($_datalist as $k=>$v)
		{
			$month = intval(date("Ym",strtotime($v['created'])));
			$new_data[$month][] = $v;
		}

		if($new_data)
		foreach($new_data as $k=>$v)
		{	
			$onetofive = 0;#工作日数
			$sixtoseven = 0;#休息日数
			$sub_onetofive=0;#工作日统计
			$sub_sixtoseven=0;#休息日统计
			$max_sales = 0;#月最高销售
			if($v)
			foreach($v as $kk=>$vv)
			{
				$vv['sumtotal'] = intval( $vv['sumtotal']);
				if(is_work_day($vv['created']))
				{
					$sub_onetofive += $vv['sumtotal'];
					$onetofive++;
				}else{
					$sub_sixtoseven += $vv['sumtotal'];
					$sixtoseven++;
				}
				$max_sales = max($max_sales,$vv['sumtotal']);
			}
			
			$_new_data[$k]['sum'] = intval($sub_onetofive+$sub_sixtoseven);
			$_new_data[$k]['sum_onetofive'] = intval($sub_onetofive);
			$_new_data[$k]['sum_sixtoseven'] = intval($sub_sixtoseven);
			// echo '工作日统计='.$sub_onetofive;
			// echo "\r\n";
			// echo '工作日数='.$onetofive;
			// echo "\r\n";
			// echo '休息日统计='.$sub_sixtoseven;
			// echo "\r\n";
			// echo '休息日数='.$sixtoseven;
			// die();
			$_new_data[$k]['avg_onetofive'] = ($sub_onetofive && $onetofive)?intval($sub_onetofive/$onetofive):0;#工作日日均
			$_new_data[$k]['avg_sixtoseven'] = ($sub_sixtoseven && $sixtoseven)?intval($sub_sixtoseven/$sixtoseven):0;#休息日日均
			$_new_data[$k]['avg'] = intval($_new_data[$k]['sum']/($onetofive+$sixtoseven));#日均
			$_new_data[$k]['max'] = $max_sales;#最高月销售
	
			$_new_data[$k]['caption'] = $k;

			$avg[]= $_new_data[$k]['avg'];
			$avg_onetofive[] = $_new_data[$k]['avg_onetofive'];
			$avg_sixtoseven[] = $_new_data[$k]['avg_sixtoseven'];
			$max[] = $max_sales;
			$m[] = $k;
		}
		
		// if($_new_data){
		// 	foreach ($_new_data as $key => $value) {
		// 		$data[] = $value;
		// 	}
		// }
		
		return ['data'=>$data,'avg'=>$avg,'avg_onetofive'=>$avg_onetofive,'avg_sixtoseven'=>$avg_sixtoseven,'max'=>$max,'m'=>$m];    	
    }


    function get_floor_sales($month1,$month2){#楼层销售额
		$month1 = is_month($month1)?$month1:date('Y-m',strtotime("-1 month -1 day"));
		$month2 = is_month($month2)?$month2:date('Y-m',strtotime("-2 day"));
    	#统计这两个月份每天的销售额
    	$day1 = date('t',strtotime($month1));
    	$day2 = date('t',strtotime($month2));

		//先得到楼层
		$_floorArr = array('N-B1','N-B2','N-F1','N-F2','N-F3','N-F4','S-B1','S-B2','S-F1','S-F2','S-F3','S-F4');
		$data = NULL;
		foreach($_floorArr as $k=>$v)
		{
				$sql = "`created` BETWEEN '".$month1.'-01'."' AND '".$month1.'-'.$day1."' AND areano like '%{$v}%'";
    			$total = $this->CI->VW_Shop_trade_model->sum('subtotal2',$sql);
				$sql2 = "`created` BETWEEN '".$month2.'-01'."' AND '".$month2.'-'.$day2."' AND areano like '%{$v}%'";
    			$total2= $this->CI->VW_Shop_trade_model->sum('subtotal2',$sql2);
    			$_data['areano'] = $v;
    			$_data['arr'][] = $total;
    			$_data['arr'][] = $total2;
    			$data[] = $_data;
    			unset($_data);
		}

    	return ['data'=>$data,'month'=>[date('Y年n月',strtotime($month1)),date('Y年n月',strtotime($month2))]];
    }


    function get_shoptype_sales($month1,$month2){#业态销售额
		$month1 = is_month($month1)?$month1:date('Y-m',strtotime("-1 month -1 day"));
		$month2 = is_month($month2)?$month2:date('Y-m',strtotime("-2 day"));
    	#统计这两个月份每天的销售额
    	$day1 = date('t',strtotime($month1));
    	$day2 = date('t',strtotime($month2));

		$_datalist = $this->CI->Shop_model->select('','shoptype,count(shoptype) as c','areano asc','shoptype');

		$datalist = array();
		foreach($_datalist as $k=>$v)
		{
			$sql = "`created` BETWEEN '".$month1.'-01'."' AND '".$month1.'-'.$day1."' AND shoptype = '{$v['shoptype']}'";
			$total = $this->CI->VW_Shop_trade_model->sum('subtotal2',$sql);
			$sql2 = "`created` BETWEEN '".$month2.'-01'."' AND '".$month2.'-'.$day2."' AND shoptype = '{$v['shoptype']}'";
			$total2= $this->CI->VW_Shop_trade_model->sum('subtotal2',$sql2);
			$_data['shoptype'] = $v['shoptype'];
			$_data['arr'][] = $total;
			$_data['arr'][] = $total2;
			$datalist[] = $_data;
			unset($_data);

		}
		
		return ['data'=>$datalist,'month'=>[date('Y年n月',strtotime($month1)),date('Y年n月',strtotime($month2))]];
    }


    function get_shop_sales($page,$per_page,$date1,$date2,$keyword,$shop_id){#店铺营业额
        $this->CI->Shop_model->page_index = max(intval($page),1);
        $this->CI->Shop_model->page_size = min(intval($per_page),10);

		$startDate = is_date($date1)?$date1:date('Y-m-d',strtotime("-1 month -1 day"));
		$endDate = is_date($date2)?$date2:date('Y-m-d',strtotime("-2 day"));

    	$where_arr[] = 'closed = 0';
    	if($keyword != ''){
    		$where_arr[] = "concat(brandname,areano,brandname2) like '%".$keyword."%'";
    	}
    	if($shop_id){
    		$where_arr[] = "shopid = {$shop_id}";
    	}
    	$this->where = $where_arr?implode(" and ",$where_arr):NULL;

    	$data_list = $this->CI->Shop_model->select($this->where,'shopid,brandname,areano,brandname2,closed','areano asc');
    	if($data_list){
			foreach($data_list as $k=>$v)
			{
				$data_list[$k]['total'] = $this->CI->Shop_trade_model->sum('subtotal2',array('shopid'=>$v['shopid'],'created >='=>$startDate,'created <='=>$endDate));
			}
    	}
    	return $data_list;

    }


    function get_rankings($page,$per_page,$date1,$date2,$shop_id){#坪效排行
        $this->CI->Shop_model->page_index = max(intval($page),1);
        $this->CI->Shop_model->page_size = min(intval($per_page),10);

		$startDate = is_date($date1)?$date1:date('Y-m-d',strtotime("-1 month -1 day"));
		$endDate = is_date($date2)?$date2:date('Y-m-d',strtotime("-2 day"));

		#第一步找出大于一个月的商家
		$where = '';
		if($shop_id){
			$where = "shopid = {$shop_id}";
		}
        $fields = 'shopid,brandname,areano,rentfee,propertyfee,areasize,closed';
		$datalist = $this->CI->Shop_model->select($where,$fields,'brandname asc');
		// print_r($datalist);
		// die();
		if($datalist)
		foreach($datalist as $k=>$v)
		{
			if($v['areasize']==0){
				unset($datalist[$k]);
				continue;
			}
			$this->CI->Shop_trade_model->page_size = PHP_INT_MAX;
			$datalistByShopId = $this->CI->Shop_trade_model->select(array('shopid'=>$v['shopid'],'created >='=>$startDate,'created <='=>$endDate));
			$total =0;
			$sub_valid_day=0;
			if($datalistByShopId)
			foreach($datalistByShopId as $kk=>$vv)
			{
				$created = $vv['created'];
				$datalist[$k][$created] =  $vv['subtotal2'];
				$total+=$vv['subtotal2'];
				 $sub_valid_day++;
				 //if(intval($vv['subtotal2'])>0)
			}
			
			$datalist[$k]['subtotal'] = $total;
			$datalist[$k]['validteday'] = $sub_valid_day;
			$datalist[$k]['monthlysale'] = $sub_valid_day>0?round(($total/$sub_valid_day*365/12),2):0;
			
			$datalist[$k]['percentage'] = $datalist[$k]['monthlysale']>0?round(($datalist[$k]['monthlysale']/$v['areasize']),2):0;
			
			if( $total==0) unset($datalist[$k]);
		}
		
		
		$sort_arr = array();
		foreach ($datalist as $key => $row) {
			$sort_arr[$key] = $row['percentage'];
		}
		if($datalist)array_multisort($sort_arr, SORT_DESC,  $datalist); 
		return $datalist;
    }


    function get_lease_sale($page,$per_page,$date1,$date2){#租售比分析
    	// echo date('Y-m-d',strtotime("-1 month -1 day"));
    	// echo "\r\n";
    	// echo date('Y-m-d',strtotime("-2 day"));
    	// die();
        $this->CI->Shop_model->page_index = max(intval($page),1);
        $this->CI->Shop_model->page_size = min(intval($per_page),10);

		$startDate = is_date($date1)?$date1:date('Y-m-d',strtotime("-1 month -1 day"));
		$endDate = is_date($date2)?$date2:date('Y-m-d',strtotime("-2 day"));

        $fields = 'shopid,brandname,areano,rentfee,propertyfee';
		$datalist = $this->CI->Shop_model->select('',$fields,'brandname asc');
		if($datalist)
		foreach($datalist as $k=>$v)
		{   
			$this->CI->Shop_trade_model->page_size = PHP_INT_MAX;
			$datalistByShopId = $this->CI->Shop_trade_model->select(array('shopid'=>$v['shopid'],'created >='=>$startDate,'created <='=>$endDate));
			// echo $this->CI->Shop_trade_model->last_query();
			// die();
			$total =0;
			$sub_valid_day=0;
			if($datalistByShopId)
			foreach($datalistByShopId as $kk=>$vv)
			{
				$created = $vv['created'];
				$datalist[$k][$created] =  $vv['subtotal2'];
				$total+=$vv['subtotal2'];
				 $sub_valid_day++;
				 //if(intval($vv['subtotal2'])>0)
			}
			
			$datalist[$k]['subtotal'] = $total;
			$datalist[$k]['validteday'] = $sub_valid_day;
			$datalist[$k]['monthlysale'] = $sub_valid_day>0?round(($total/$sub_valid_day*365/12),2):0;
			$datalist[$k]['percentage'] = $datalist[$k]['monthlysale']>0?($datalist[$k]['rentfee']+$datalist[$k]['propertyfee'])/$datalist[$k]['monthlysale']*100:0;

			if( $total==0) unset($datalist[$k]);
		}
		
		$sort_arr = array();
		foreach ($datalist as $key => $row) {
			$sort_arr[$key] = $row['percentage'];
		}
		if($datalist)array_multisort($sort_arr, SORT_ASC,  $datalist); 

		return $datalist;
    }

}    