<?php
/**
 * 车流管理
 * 
 */
class V1_Traffic_flow  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Traffic_flow_model'));
		$this->CI->load->helper(array('jt_admin'));
    }

    /**
     * 车流列表
     */
    function traffic_list($begin_date,$end_date){
		$startDate = is_date($begin_date)?$begin_date:date('Y-m-01',strtotime("-1 month "));
		$endDate = is_date($end_date)?$end_date:date('Y-m-d',strtotime($startDate." +1 month - 1 day"));
	
		$_datalist = $this->CI->Traffic_flow_model->select("created >= '{$startDate}' and created <= '{$endDate}'","*","created asc");

		$dataListByCurrentMonth = array();
		$_dateRang = array();

		$max_north_jinsha = 0;
		$max_north_zhenbei = 0;
		$max_south_jinsha = 0;
		$max_south_zhenbei = 0;
		$max_all = 0;
		if($_datalist)
		foreach($_datalist as $k=>$v)
		{
			// $created = $v['created']."(".week_name2($v['created']).")";
			// $_dateRang[$created]=intval($v['num']);
			$dataListByCurrentMonth[] = array(
									'date'=>$v['created'],
									"north_jinsha"=>$v['north_jinsha'],
									"north_zhenbei"=>$v['north_zhenbei'],
									"south_jinsha"=>$v['south_jinsha'],
									"south_zhenbei"=>$v['south_zhenbei'],
									'num'=>$v['num']
								);
			$max_north_jinsha = max($max_north_jinsha,$v['north_jinsha']);
			$max_north_zhenbei = max($max_north_zhenbei,$v['north_zhenbei']);
			$max_south_jinsha = max($max_south_jinsha,$v['south_jinsha']);
			$max_south_zhenbei = max($max_south_zhenbei,$v['south_zhenbei']);
			$max_all = max($max_all,$v['num']);
		}
        // print_r($dataListByCurrentMonth);
        // die();


		// if($dataListByCurrentMonth){
		// 	foreach ($dataListByCurrentMonth as $key => $value) {
		// 		$dataListByCurrentMonth[$key]['is_max_north_jinsha'] = ($max_north_jinsha==$value['north_jinsha']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_north_zhenbei'] = ($max_north_zhenbei==$value['north_zhenbei']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_south_jinsha'] = ($max_south_jinsha==$value['south_jinsha']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_south_zhenbei'] = ($max_south_zhenbei==$value['south_zhenbei']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_all'] = ($max_all==$value['num']) ?true:false;
		// 	}
		// }
        // print_r($dataListByCurrentMonth);
        // die();

		return $dataListByCurrentMonth;
		// $this->view('traffic_flow',array('jquery_ui'=>true,'startDate'=>$startDate,'endDate'=>$endDate,'dateRang'=>$_dateRang,'dataListByCurrentMonth'=>$dataListByCurrentMonth));    	
    }

    /**
     * 导入车流数据
     */
    function traffic_flow_import_data($goods_arr){
        if(!is_array($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE; 
        }

        if(!count($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE;             
        }

        $date = '';
        $_new_import_data = array();
        foreach($goods_arr as $k=>$v)
        {

            $date = $v['created'];

            if(!is_date($date))
            {
                $this->tips = '日期数据格式不正确,请检查'.($k+1).'号数据：'.$date;
                return FALSE;
            }
  
            $north_jinsha = intval($v['north_jinsha']);
            $north_zhenbei = intval($v['north_zhenbei']);
            $south_jinsha = intval($v['south_jinsha']);
            $south_zhenbei = intval($v['south_zhenbei']);

            // if($north_jinsha < 10|| $north_zhenbei <10|| $south_jinsha <10|| $south_zhenbei <10){
            //     $this->tips = '数据数量不正确,请检查 '.($date).' 数据';
            //     return FALSE;
            // }
            
            $_new_import_data[] = array(
            'created'=>$date,
            'north_jinsha'=> $north_jinsha,
            'north_zhenbei'=>$north_zhenbei,
            'south_jinsha'=>$south_jinsha,
            'south_zhenbei'=>$south_zhenbei,
            'num'=>$north_jinsha+$north_zhenbei+$south_jinsha+$south_zhenbei#$v['num']
            );
            #判断每个月应该导入多少条数据
            $month =  date('Y-m',strtotime($v['created']));
            $month_arr[$month][] = $v['created'];
        }
        
        if($month_arr){
            foreach ($month_arr as $key => $value) {
                $month = date('Y-m',strtotime($key));
                $day_count = date('t',strtotime($key));
                $count_new_import_data = count($value);

                if($day_count != $count_new_import_data){
                    $this->tips = "{$month}本月应导入数据: {$day_count}条，当前只有 {$count_new_import_data} 条";
                    return FALSE;
                }                
            }
        }

        $this->CI->Traffic_flow_model->delete("date_format(created,'%Y-%m') = '".date("Y-m",strtotime($date))."'");
        foreach ($_new_import_data as $key => $value) {
            $this->CI->Traffic_flow_model->insert($value);
        }
        
        return TRUE;    
    }    

    /**
     * 上传EXCEL
     */
    function traffic_flow_excel($admin_id){
        $config['upload_path'] = FCPATH . '/uploadfile/excel/shoptrade/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = '500';
        $config['file_name'] = date('Ymdhis') . $admin_id;

        dir_create($config['upload_path']);//创建正式文件夹
        $this->CI->load->library('upload', $config);
        //强制将扩展名转为其原始类型的扩展名
        if(!isset($_FILES['file'])){
            $this->tips = '上传失败,上传文件不存在!';
            return FALSE;
        }
        $file = $_FILES['file'];
        $file_tmp_name = $_FILES['file']['tmp_name'];
        $finfo   = finfo_open(FILEINFO_MIME);
        if (!$finfo) {
            $this->tips = 'Opening fileinfo database failed';
            return FALSE;
        }
        $mimetype = finfo_file($finfo, $file_tmp_name);
        finfo_close($finfo);

        $file_type_arr = explode(';',$mimetype);

        switch ($file_type_arr[0]) {
                case 'application/octet-stream':
                    $extend_name = '.xlsx';
                    $extend_type = 'application/octet-stream';
                    break;
                case 'application/vnd.ms-office':
                    $extend_name = '.xls';
                    $extend_type = 'application/vnd.ms-office';
                    break;
                case 'application/vnd.ms-excel':
                    $extend_name = '.xls';
                    $extend_type = 'application/vnd.ms-excel';
                    break;
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    $extend_name = '.xlsx';
                    $extend_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    break;
                case 'application/zip':
                    $extend_name = '.xlsx';
                    $extend_type = 'application/zip';
                    break;
                default:
                    $this->tips = lang('<p>The filetype you are attempting to upload is not allowed.</p>');
                    return FALSE;
                break;
            }
        $_FILES['file']['name'] = strstr($file['name'],'.',true).$extend_name;
        $_FILES['file']['type'] = $extend_type;

        if (!$this->CI->upload->do_upload('file')) {
            $this->tips = $this->CI->upload->display_errors();
            return FALSE;
        } else {
            $filedata = $this->CI->upload->data();
            $file_path = $config['upload_path'] . $filedata['file_name'];
            if (!file_exists($file_path)) {
                exit("not found EXCEL:" . $file_path);
            }
            if($extend_name == '.xls'){
                $objPHPExcel = new PHPExcel_Reader_Excel5();
            }else{
                $objPHPExcel = new PHPExcel_Reader_Excel2007();
            }
            $PHPExcel = $objPHPExcel->load($file_path); // 载入excel文件
            //得到统计表
            $sheetCount = $PHPExcel->getSheetCount();
            $excel_arr = NULL;
            for($i=0;$i<$sheetCount;$i++){
                $currentSheet = $PHPExcel->getSheet($i);
                //取得一共有多少列
                $allColumn = 'F';//$currentSheet->getHighestColumn();   
                //取得一共有多少行
                $allRow = $currentSheet->getHighestRow();  

                //循环读取数据，默认编码是utf8，这里转换成gbk输出
                for($currentRow = 3;$currentRow<=$allRow;$currentRow++)
                {
                    //单无格
                    $currentValue ='';
                    for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++)
                    {
                        $address = $currentColumn.$currentRow;

                        $currentValue .= trim($currentSheet->getCell($address)->getValue());

                        if($currentColumn == 'A'){
                            if(!$currentValue){
                                break;
                            }
                            $arr[$currentColumn] = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($currentValue));
                        }else{
                            $arr[$currentColumn] = trim( $currentSheet->getCell($address)->getValue());
                        }
                       
                           
                    }

                    if(trim($currentValue)=="")continue;

                    $_arr = array(
                                    'created'=> $arr['A'],
                                    'north_jinsha'=> $arr['C'],
                                    'north_zhenbei'=> $arr['D'],
                                    'south_jinsha'=> $arr['E'],
                                    'south_zhenbei'=> $arr['F'],
                                    'num'=> $arr['C']+$arr['D']+$arr['E']+$arr['F']
                    );                        
                    $excel_arr[] = $_arr;
                }
            }

            return $excel_arr;
        }   
    } 



}   
