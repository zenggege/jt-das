<?php
    if(! function_exists('safe_replace'))
    {
        function _safe_replace($string) {
            
            $string = str_replace('%20','',$string);
            
            $string = str_replace('%27','',$string);
            $string = str_replace('%2527','',$string);
            $string = str_replace('*','',$string);
            $string = str_replace('"','&quot;',$string);
            $string = str_replace("'",'',$string);
            $string = str_replace('"','',$string);
            $string = str_replace(';','',$string);
            $string = str_replace('<','&lt;',$string);
            $string = str_replace('>','&gt;',$string);
            $string = str_replace("{",'',$string);
            $string = str_replace('}','',$string);
            return $string;
        }
        
        /**
         * 安全过滤函数
         *
         * @param $string
         * @return string
         */
        function safe_replace($string) {
            
            if(!is_array($string)) return _safe_replace($string);
            foreach($string as $key => $val) $string[$key] = _safe_replace($val);
            return $string;
        }
       
    }

    if(!function_exists('is_date'))
    {
        /** 
         * 验证日期格式是否正确 
         * @param string $date 
         * @param string $format 
         * @return boolean 
         */  
        function is_date($date,$format='Y-m-d'){  
            $t=date_parse_from_format($format,$date);  
            if(empty($t['errors'])){  
                return true;  
            }else{  
                return false;  
            }  
        } 
        
    }

    if(!function_exists('is_month'))
    {
        /** 
         * 验证月格式是否正确 
         * @param string $date 
         * @param string $format 
         * @return boolean 
         */  
        function is_month($date,$format='Y-m'){  
            $t=date_parse_from_format($format,$date);  
            if(empty($t['errors'])){  
                return true;  
            }else{  
                return false;  
            }  
        } 
        
    }

    if(!function_exists('is_year'))
    {
        /** 
         * 验证年格式是否正确 
         * @param string $date 
         * @param string $format 
         * @return boolean 
         */  
        function is_year($date,$format='Y'){  
            $t=date_parse_from_format($format,$date);  
            if(empty($t['errors'])){  
                return true;  
            }else{  
                return false;  
            }  
        } 
        
    }



    /** 
     * 是否为整数 
     * @param int $number 
     * @return boolean 
     */ 
    if(!function_exists('is_number'))
    {
        function is_number($number){
            
            if(preg_match('/^[-\+]?\d+$/',$number)){  
                return true;  
            }else{  
                return false;  
            }  
        }
        
    }

    if(!function_exists('is_english'))
    {
        /** 
         * 是否为英文 
         * @param string $str 
         * @return boolean 
         */  
        function is_english($str){  
            if(ctype_alpha($str))  
                return true;  
            else  
                return false;  
        }  
    }
    
    if(!function_exists('is_chinese'))
    {
        /** 
         * 是否为中文 
         * @param string $str 
         * @return boolean 
         */  
        function is_chinese($str){  
            if(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $str))
                return true;
            else
                return false;  
        } 
    }


     /** 
     * 是否西欧文字
     *   
     * @param $txt   文字 
     * return bool
     */ 
    if(!function_exists('is_eur_charset')){

        function is_eur_charset($txt){
            return (preg_match("/^['.AmpèrestraatabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@äüöüëëéèôà áßÄÖÜËÉÈÔÀÁ\s\-]+$/",$txt));
        }

        function is_include_chinese($str){
            if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)>0){
                return true;
            }elseif(preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)>0){
                return true;
            }

            return false;
        }
        
    }

    if(!function_exists('str_exists'))
    {
        /**
         * 查询字符是否存在于某字符串
         * 
         * @param $haystack 字符串
         * @param $needle 要查找的字符
         * @return bool
         */
        function str_exists($haystack, $needle)
        {
            if($needle==="") {
                return false;
            }
            return !(strpos($haystack, $needle) === FALSE);
        }
    }

    if(!function_exists('str2bin')){
        function str2bin($str)
        {

            $bin = '';
            for($i=0,$len=strlen($str);$i<$len;$i++){
               $bin .= str_pad(base_convert($str{$i}, 16, 2),4,'0', STR_PAD_LEFT);

            }
            return $bin;

        }
    }

     /*
     *验证邮编
     */
    if(!function_exists('check_code'))
    {
        function check_code($code){
            return (preg_match("/^[0-9]\d{5}(?!\d)$/",$code))?true:false;
        }
    }

    if(!function_exists('is_email'))
    {
        function is_email($email=''){
            
            $regex = '/^[0-9a-z][0-9a-z-._]+@{1}[0-9a-z.-]+[a-z]{2,4}$/i';
            if (preg_match($regex, $email,$match)){
                
                return true;
            }
         
            return false;
        }
        
    }

    if(!function_exists('is_mobile'))
    {
        function is_mobile($mobile=''){

            $mobile = str_replace("-", "", $mobile);
            $mobile = str_replace(" ", "", $mobile);
            $mobile = str_replace(" ", "", $mobile);//特殊空格，iOS的不要去掉

            /**
             * 手机号码:
             * 13[0-9], 14[5,7, 9], 15[0, 1, 2, 3, 5, 6, 7, 8, 9], 17[0-9], 18[0-9]
             * 移动号段: 134,135,136,137,138,139,147,150,151,152,157,158,159,170,178,182,183,184,187,188
             * 联通号段: 130,131,132,145,155,156,170,171,175,176,185,186
             * 电信号段: 133,149,153,170,173,177,180,181,189,199
             */
            $mobile_reg = '/^1(3[0-9]|4[579]|5[0-35-9]|6[0-9]|7[0-9]|8[0-9]|9[0-9])\d{8}$/';
            //$mobile_reg ='/^1[2|3|4|5|8|6|7|8|9][0-9]\d{4,8}$/';

            ##'/^1[2|3|4|5|8|6|7|8|9][0-9]\d{4,8}$/'##
              ##上面注释的之前的朋友写的正则,满足不了现在的要求了##
            if (strlen ( $mobile ) != 11 || ! preg_match ( $mobile_reg, $mobile )) {
                log_message("test", "22手机号格式不正常 ：{$mobile}");
                return false;
            } else {
                return true;
            }
            
        }
        
    }
//------------------------safeMathFunction-------------------------------------------
    /**
     * 加法
     * @param $a
     * @param $b
     * @return string
     */
if(!function_exists('safe_add')){
    function safe_add($a, $b)
    {
        bcscale(2);

        $c = bcadd($a, $b);
        assert($c >= $a);
        return $c;
    }
}

/**
 * 减法
 * @param $a
 * @param $b
 * @return string
 */
if (!function_exists('safe_sub')){
    function safe_sub($a, $b)
    {
        bcscale(2);
        $c = bcsub($a, $b);
        assert($b <= $a);
        return $c;
    }
}

/**
 * 乘法
 */
if (!function_exists('safe_mul')){ 

    function safe_mul($a, $b)
    {
        bcscale(2);
        $c = bcmul($a, $b);
       // assert($a == 0 || round(($c /$a),2) == $b);
        return $c;
    }
}

/**
 * 除法
 * @param $a
 * @param $b
 * @return string
 */
if (!function_exists('safe_div')){
    function safe_div($a, $b)
    {
        assert($b > 0);
        $c = bcdiv($a, $b);
        return $c;
    }
}
//------------------------------------------------------------------------------------

/**
 * 解压gzip数据
 */
if (!function_exists('gzdecode')) {
    function gzdecode ($data) {
        $flags = ord(substr($data, 3, 1));
        $headerlen = 10;
        $extralen = 0;
        $filenamelen = 0;
        if ($flags & 4) {
            $extralen = unpack('v' ,substr($data, 10, 2));
            $extralen = $extralen[1];
            $headerlen += 2 + $extralen;
        }
        if ($flags & 8) // Filename
            $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 16) // Comment
            $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 2) // CRC at end of file
            $headerlen += 2;
        $unpacked = @gzinflate(substr($data, $headerlen));
        if ($unpacked === FALSE)
            $unpacked = $data;
        return $unpacked;
    }
}

