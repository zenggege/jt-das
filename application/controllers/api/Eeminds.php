<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 提醒类 用于设置邮件提醒
 */
class Eeminds extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(['V1_Analysis']);
    }


    function set_post(){#设置提醒的邮件内容、接收人、日期
    	$type = intval($this->post('type',true));#1:租金 2:营业额
    	$content = trim($this->post('content',true));#邮件内容
    	$sendee = trim($this->post('sendee',true));#接收邮件(多个邮件逗号隔开)
    	$date = trim($this->post('date',true));#每月提醒日/到期提醒日

    	$this->respond(['status'=>true,'tips'=>'数据获取成功','data'=>[]]);
    }

}    