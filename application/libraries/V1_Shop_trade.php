<?php
/**
 * 会员管理
 * 
 */
class V1_Shop_trade  {
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Vip_model','Shop_model','Shop_trade_model'));
    }

    function shop_trade_excel($admin_id){
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

                $allColumn = 'D';//$currentSheet->getHighestColumn();   
                 
                //取得一共有多少行
                $allRow = $currentSheet->getHighestRow();  
                 
                //循环读取数据，默认编码是utf8，这里转换成gbk输出
                for($currentRow = 1;$currentRow<=$allRow;$currentRow++)
                {
                    if($i==5&&$currentRow==1)continue;
                    
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
                        }
                        $arr[$currentColumn] = trim( $currentSheet->getCell($address)->getValue());
                    }
                    if(trim($currentValue)=="")continue;

                    if($i==5&&$currentRow>1)
                    {
                        $_temp_c = $arr['C'];
                        $arr['C'] = $arr['D'];
                        $arr['D'] = $_temp_c;
                    }
                    $_arr['areano'] = $arr['B'];
                    $_arr['tangchi'] = $arr['C'];
                    $_arr['waimai'] = $arr['D'];

                    $excel_arr[] = $_arr;
                    
                }
            }
            return $excel_arr;
        }
            
    }

    /**
     * 导入店铺销售额数据
     */
    function shop_trade_import_data($goods_arr){
        if(!is_array($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE; 
        }

        if(!count($goods_arr)){
            $this->tips = '未导入任何数据';
            return FALSE;             
        }
        $importDataArr = $goods_arr;

        foreach($importDataArr as $k=>$v)
        {
            // print_r($v);
            // die();
            $brandName = $v["areano"];
            $subtotal = str_replace(",","",$v["tangchi"]);
            
            
            if(trim($brandName)=="")continue;
            $shopinfo = $this->CI->Shop_model->one("brandname like '%{$brandName}%' ");
            
            if(!$shopinfo)
            {
                //如果没有这个品牌
                $shopinfo['shopid']= $this->CI->Shop_model->insert(array('brandname'=>$brandName));
            }
            
            $shopinfo = $this->CI->Shop_model->one("brandname like '%{$brandName}%' ");
            $key = $shopinfo['shopid'];
            


            
            //$shopid = $shopinfo['shopid'];
            if(!isset($dataList[$key]))$dataList[$key] = array('shop_id'=>$shopinfo['shopid'],'sub_qty'=>0,'sub_total'=>0);
            $dataList[$key]['sub_qty'] = intval($v["waimai"]);//如果有多台收银机
            $dataList[$key]['sub_total'] = intval($subtotal) ;//如果有多台收银机


            if($brandName=="芝士说")
            {
                // print_r($dataList[$key]);
                // die("here");
            }
            
        }

        
        
        $current_unix_time = time();
        //自动汇总
        foreach($dataList as $k=>$v)
        {
            
            if($v['sub_qty']>10000) die("成龙影院数据不正确，有可能人次和票房写反");
            $tradeinfo = $this->CI->Shop_trade_model->one(array('shopid'=>$v['shop_id'],'created'=>date("Y-m-d",$current_unix_time)));
            if($tradeinfo)//update
            {
                $this->CI->Shop_trade_model->update(array('subqty2'=>$v['sub_qty'],'subtotal2'=>$v['sub_total'],'updated'=>date('Y-m-d H:i:s')),array('tradeid'=>$tradeinfo['tradeid']));
            }
            else
            {
                $this->CI->Shop_trade_model->insert(array('shopid'=>$v['shop_id'],'contractno'=>$k,'subqty2'=>$v['sub_qty'],'subtotal2'=>$v['sub_total'],'created'=>date("Y-m-d",$current_unix_time),'updated'=>date('Y-m-d H:i:s')));
            }
            
            if($v['shop_id']==99){
                
                
            }
        }
        return TRUE;        
    }



}    