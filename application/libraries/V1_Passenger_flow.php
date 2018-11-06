<?php
/**
 * 客流管理
 * 
 */
class V1_Passenger_flow  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Passenger_flow_model'));
		$this->CI->load->helper(array('jt_admin'));
    }

    function passenger_list($begin_date,$end_date){

		$startDate = is_date($begin_date)?$begin_date:date('Y-m-d',strtotime("-1 month "));
		$endDate = is_date($end_date)?$end_date:date('Y-m-d',strtotime("-1 day"));
			
		$_datalist = $this->CI->Passenger_flow_model->select("created >= '{$startDate}' and created <= '{$endDate}'","*","created asc");

		$dataListByCurrentMonth = array();
		$_dateRang = array();

		$max_in = 0;
		$max_out = 0;
		$max_all = 0;
		if($_datalist)
		foreach($_datalist as $k=>$v)
		{
			// $created = $v['created']."(".week_name2($v['created']).")";
			// $_dateRang[$created]=intval($v['num']);
			$dataListByCurrentMonth[] = array(
														'date'=>$v['created'],
														"in"=>$v['in'],
														"out"=>$v['out'],
														'num'=>$v['num']
													);
			$max_in = max($max_in,$v['in']);
			$max_out = max($max_out,$v['out']);
			$max_all = max($max_all,$v['num']);
		}


		// if($dataListByCurrentMonth){
		// 	foreach ($dataListByCurrentMonth as $key => $value) {
		// 		$dataListByCurrentMonth[$key]['is_max_in'] = ($max_in==$value['in']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_out'] = ($max_out==$value['out']) ?true:false;
		// 		$dataListByCurrentMonth[$key]['is_max_all'] = ($max_all==$value['num']) ?true:false;
		// 	}
		// }
		return $dataListByCurrentMonth;
		// $this->view('passenger_flow',array('jquery_ui'=>true,'startDate'=>$startDate,'endDate'=>$endDate,'dateRang'=>$_dateRang,'dataListByCurrentMonth'=>$dataListByCurrentMonth));

    }


     /**
     * 导入客流数据
     */

    function passenger_flow_import_data($goods_arr){
        if(!is_array($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE; 
        }

        if(!count($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE;             
        }

        $day = '';
        foreach ($goods_arr as $key => $value) {
            $day = $value['created'];
            if($value['in'] < 100|| $value['out'] <100){
                $this->tips = "数据数量不正确,请检查{$key}号数据";
                return FALSE;
            }
            
            $_new_import_data[] = array(
                                        'created'=>$value['created'],
                                        'in'=> $value['in'],
                                        'out'=>$value['out'],
                                        'num'=>$value['num']
                                       );

        }
        $day_count = date('t',strtotime($day));
        $count_new_import_data = count($_new_import_data);
        if($day_count != $count_new_import_data){
            $this->tips = "本月应导入数据: {$day_count}条，当前只有 {$count_new_import_data} 条";
            return FALSE;
        }

        $this->CI->Passenger_flow_model->delete("date_format(created,'%Y-%m') = '".date("Y-m",strtotime($day))."'");
        foreach ($_new_import_data as $key => $value) {
            $this->CI->Passenger_flow_model->insert($value);
        }            
        
        return TRUE;
    }


    /**
     * 上传客流数据
     */
    function passenger_flow_excel($admin_id){
        $config['upload_path'] = FCPATH . '/uploadfile/excel/passengerflow/';
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
                    $this->tips = '<p>The filetype you are attempting to upload is not allowed.</p>';
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
            $objPHPExcel ->setReadDataOnly(true);//读取数据,会智能忽略所有空白行,这点很重要！！！
            $PHPExcel = $objPHPExcel->load($file_path); // 载入excel文件
            $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumm = $sheet->getHighestColumn(); // 取得总列数
            $highestColumm = PHPExcel_Cell::columnIndexFromString($highestColumm);
 
            /** 循环读取每个单元格的数据 */
            $default_arr = array();
            for ($row = 3; $row <= $highestRow; $row++) {//行数是以第2行开始
                $cols = array();

                for ($column = 0; $column < $highestColumm; $column++) {//列数是以A列开始

                    $colString = PHPExcel_Cell::stringFromColumnIndex($column);#得到列名
                    $colStringNext = PHPExcel_Cell::stringFromColumnIndex($column + 1);#得到下一列名

                    $col = str_replace(",", "，", trim($sheet->getCell($colString . $row)->getCalculatedValue()));#得到第一列第二行值
                    $colNext = str_replace(",", "，", trim($sheet->getCell($colStringNext . $row)->getCalculatedValue()));#得到第二列第二行值

                    
                    if(!$col && !$colNext && $column < 5){
                        break;
                    }

                    $col = str_replace(PHP_EOL, '', $col);
                    $col = str_replace('"', '', $col);
                    if($column == 0){
                        $col = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($col));
                    }

                    $cols[] = $col;
                }

                //第一行不可以为空
                if(!$cols && $row == 3){
                    $this->tips = '上传失败，请检查上传EXCEL文件(第三行不能为空)';
                    return FALSE;
                }
                //行为空则终止
                if(!$cols && $row >= 3){
                    break;
                }


                foreach ($cols as $key => $value) {
                    if ($value == '' ) {
                        $this->tips = sprintf('导入失败，请检查第%s行%s列，数据不能为空！',$row,$key+1);
                        return FALSE;
                    }
                }

                $_cols = array(
                                'created'=> $cols[0],
                                'in'=> $cols[2],
                                'out'=> $cols[3],
                                'num'=> $cols[4]
                );                        

                $default_arr[] = $_cols;
            }

            if(count($default_arr) > 150){
                $this->tips = '最大上传数据条数只能为150条';
                return FALSE;
                
            }
            return $default_arr;
            
        }
    }



}    