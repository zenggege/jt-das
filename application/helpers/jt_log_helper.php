<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("log_data")) {
    /**
    * LOG 数据
    * 比自带的那个log_message要好用，自带的$data不能用数组，对象
    */

    function log_data($data, $level = 'ERROR') {

        if(is_array($data)) {
            $data = addslashes(var_export($data, TRUE));
        }

        log_message($level, $data);
    }
}