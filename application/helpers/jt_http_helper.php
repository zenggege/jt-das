<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("curl_post")) {
    function curl_post($url, $data, $headers = [], $timeout = 30) {
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                              
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        }
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
        }

        $result = curl_exec($ch);

        if ($result === false) {
            $errorNo = curl_errno($ch);
            $errorMsg = curl_error($ch);
            log_message("error", "curl error no:" . $errorNo . ",error msg:" . $errorMsg);
        }
        curl_close($ch);
        return $result;
    }
}

    if(!function_exists('http_post_data'))
    {
        function http_post_data($url, $data,$is_xml=false){

            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
           @curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if($is_xml){
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }else{
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
            }
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            curl_setopt($curl, CURLOPT_TIMEOUT, 6);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            
            $tmpInfo = curl_exec($curl); // 执行操作
            if (curl_errno($curl)) {
               // echo 'Errno'.curl_error($curl);//捕抓异常
            }
            curl_close($curl); // 关闭CURL会话

            return $tmpInfo; // 返回数据
        }
    }


    if (!function_exists('curl_get')) {
        // 发送http get请求
        function curl_get($url,$headers=[],$timeout=30){
            $SSL = substr($url, 0, 8) == "https://" ? true : false;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if (!empty($headers) && is_array($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            if ($SSL) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $result = curl_exec($ch);
            if ($result === false) {
                $errorNo = curl_errno($ch);
                $errorMsg = curl_error($ch);
                log_message("error", "curl error no:" . $errorNo . ",error msg:" . $errorMsg);
            }
            curl_close($ch);
            return $result;

        }
    }


    if(!function_exists('short_url')){
        function short_url($url)
        {

            $md5_str = md5($url);
            $md5_arr = str_split($md5_str,16);
            $ret = array();
            $seed_arr = array_merge(range('a','z'),range(0,9),range('A','Z'));
            $seed_len = count($seed_arr);
            foreach($md5_arr as $str){
                $bin = str2bin($str);
                $bin_len = strlen($bin);
                $avg_len = ceil($bin_len/6);
                $bin_arr = str_split($bin,$avg_len);
                $tmp_str = '';
                foreach($bin_arr as $bin){
                    $index = bindec($bin)%$seed_len;
                    $tmp_str .= $seed_arr[$index];
                }
                $ret[] = $tmp_str;
            }
            
            return implode("_",$ret);
        }
    }



    if( !function_exists('apache_request_headers') ) {

        function apache_request_headers() {
          $arh = array();
          $rx_http = '/\AHTTP_/';
          foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
              $arh_key = preg_replace($rx_http, '', $key);
              $rx_matches = array();
              // do some nasty string manipulations to restore the original letter case
              // this should work in most cases
              $rx_matches = explode('_', $arh_key);
              if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                $arh_key = implode('-', $rx_matches);
              }
              $arh[ucfirst(strtolower($arh_key))] = $val;
            }
          }
          return( $arh );
        }
   }


   /**
    * 自己给自己站点POST数据，主要是用于队列方面
    */
   if (!function_exists('resque_post_data')) {

        function resque_post_data( $url, $post_data = NULL , $header = NULL) {

            $o = "";
            $post_data_str = "";
            $post_data['format'] = 'json';
            $app_secret = "";
            if(!isset($post_data['app_secret'])) $app_secret = DEFAULT_API_SECRET;
            if(!isset($post_data['app_key']))  $post_data['app_key']= DEFAULT_API_KEY;
            $post_data['timestamp'] = time();
            if(is_array($post_data)) {
                ksort($post_data);

                foreach ($post_data as $k=>$v)
                {
                    if(!is_array($v) && !is_object($v)) {
                        $o.= "$k=".urlencode($v)."&";   //默认UTF-8编码格式
                    }
                }

                $post_data_str = substr($o,0,-1);
            }
            $sign_str = md5($app_secret.$post_data_str);
            $post_data['sign'] = $sign_str;
            $post_data_str=$post_data_str."&sign=".$sign_str;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if ($header) {
                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            }
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $result_code =  curl_exec($ch);

            $result = json_decode($result_code,true);

            return $result;

        }

   }

    /**
     *  @判断是否是有效url
     *  @param url
     *  @return bool
     */
   if (!function_exists('is_url')){

       function is_url($url){
           $array = get_headers($url,1);
           if(preg_match('/200/',$array[0])){
               return true;
           }else{
               return false;
           }

       }
   }

    /**
     *  gzip解压数据
     *  @param data
     *  @return str
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


    /**
     * 拍拍信对接专用
     * @param $appsecret
     * @param $url
     * @param null $post_data
     * @param null $sign_data
     * @param null $header
     * @return mixed
     */
    if (!function_exists('ppx_post')) {   

        function ppx_post($appsecret,$url, $post_data = NULL ,$sign_data= NULL, $header = NULL){
            $o = "";
            $_o ="";
            $p= '';
            $post_data_str = "";
            $sign_data_str = "";
            if(is_array($sign_data)) {
                ksort($sign_data);

                foreach ($sign_data as $k=>$v)
                {
                    $o.= "$k".urlencode($v);   //默认UTF-8编码格式
                    $_o.="$k=".urlencode($v)."&";
                }
                $sign_data_str = $o;
                $_sign_data_str =substr($_o,0,-1);

            }
            if(is_array($post_data)) {
                foreach ($post_data as $k => $v) {
                    $p .= "$k=" . urlencode($v) . "&";
                }
                $post_data_str = substr($p,0,-1);;

            }

            $sign_str =md5($appsecret.$sign_data_str.$appsecret);

            log_message('test','MD5参数==>'.$appsecret.$sign_data_str.$appsecret);

            $post_data['sign'] = $sign_str;
            log_message('test','MD5==>'.$sign_str);
            $post_data_str=$_sign_data_str."&sign=".$sign_str.'&'.$post_data_str;
            log_message('test','usrl==>'.$url.'?'.$post_data_str);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if ($header) {
                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            }
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $result_code =  curl_exec($ch);
            $result = json_decode($result_code,true);

            if(is_cli()) {
                // fwrite(STDOUT, "ppx_post: ->请求网址:".addslashes(var_export($url, TRUE)). PHP_EOL);
                // fwrite(STDOUT, "ppx_post: ->sign_str:".config_item('encryption_key').$post_data_str. PHP_EOL);
                // fwrite(STDOUT, "ppx_post: ->请求数据:".addslashes(var_export($post_data, TRUE)). PHP_EOL);
                // fwrite(STDOUT, "ppx_post: ->返回数据:".addslashes(var_export($result_code, TRUE)). PHP_EOL);
            }

            return $result;
        }
    }

/**
 *   商汤对接专用
 */
    if(!function_exists('st_post')){
        function st_post($url,$post_data,$Authorization)
        {
            $ch = curl_init();
            $headr = array();
            $header[] = 'Authorization: ' . $Authorization;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //打开SSL验证时，需要安装openssl库。也可以选择关闭，关闭会有风险。
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $output = curl_exec($ch);
            curl_close($ch);

            return $output;
        }

    }

    /**
     * 今枝对接专用
     * @param $appsecret
     * @param $url
     * @param null $post_data
     * @param null $header
     * @return mixed
     */
    if (!function_exists('jz_post')) {   

        function jz_post($appsecret,$url, $post_data = NULL){
     
            $p= '';
            $post_data_str = "";
            ksort($post_data);
            if(is_array($post_data)) {
                foreach ($post_data as $k => $v) {
                    $p .= "$k" . $v ;
                    // $post_data_str .= "$k=" . urlencode($v)."&" ;
                }
                // $post_data_str = substr($post_data_str,0,-1);;

            }


            $sign_str = strtoupper(md5($appsecret.$p.$appsecret));

            $post_data['sign'] = $sign_str;
            // $post_data_str="sign=".$sign_str.'&'.$post_data_str;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $result_code =  curl_exec($ch);
            $result = json_decode($result_code,true);

            if(is_cli()) {
                fwrite(STDOUT, "resque_post_data: ->请求网址:".addslashes(var_export($url, TRUE)). PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->sign_str:".$appsecret.$p.$appsecret. PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->请求数据:".addslashes(var_export($post_data, TRUE)). PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->返回数据:".addslashes(var_export($result_code, TRUE)). PHP_EOL);

            }

            return $result;
        }
    }

    /**
     * 芝麻对接专用
     * @param $appsecret
     * @param $url
     * @param null $post_data
     * @param null $header
     * @return mixed
     */
    if (!function_exists('zm_post')) {   

        function zm_post($appsecret,$url, $post_data = NULL){
     
            $p= '';
            $post_data_str = "";
            ksort($post_data);
            if(is_array($post_data)) {
                foreach ($post_data as $k => $v) {
                    if(!is_array($v) && trim($v)=="") continue;
                    $p .= "$k=" . $v ."&" ;
                    // $post_data_str .= "$k=" . urlencode($v)."&" ;
                }
                $p = substr($p,0,-1);;

            }


            $sign_str = strtoupper(md5($p."&appSecret=".$appsecret));

            $new_post_data['sign'] = $sign_str;

            $new_post_data['params'] = $post_data;
            // $post_data_str="sign=".$sign_str.'&'.$post_data_str;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($new_post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $result_code =  curl_exec($ch);
            $result = json_decode($result_code,true);

            if(is_cli()) {
                fwrite(STDOUT, "resque_post_data: ->请求网址:".addslashes(var_export($url, TRUE)). PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->sign_str:".$appsecret.$p.$appsecret. PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->请求数据:".addslashes(var_export($post_data, TRUE)). PHP_EOL);
                fwrite(STDOUT, "resque_post_data: ->返回数据:".addslashes(var_export($result_code, TRUE)). PHP_EOL);

            }

            return $result;
        }

        function zm_get_url($appsecret,$url, $post_data = NULL){
     
            $p= '';
            $get_url_str = "";
            ksort($post_data);
            if(is_array($post_data)) {
                foreach ($post_data as $k => $v) {
                    if(trim($v)=="") continue;
                    $p .= "$k=" . $v ."&" ;
                    $get_url_str .= "{$k}=" . urlencode($v)."&" ;
                }
                $p = substr($p,0,-1);;

            }


            $sign_str = strtoupper(md5($p."&appSecret=".$appsecret));

            $get_url_str= $url."?".$get_url_str."sign=".$sign_str;
           

            return $get_url_str;
        }
    }
    /**
     *param msg 消息
     *param alias 别名
     */
    if (!function_exists('JPush')){
        function JPush($msg='',$alias='')
        {
            if(trim($msg)=="") die("msg不能为空");
            $client = new \JPush\Client(JPUSH_KEY, JPUSH_SECRET);
            $pusher = $client->push();
            $pusher->setPlatform('all');
            $pusher->setNotificationAlert($msg);
            if ($alias == 'ALL'){
                $pusher->addAllAudience();
            }else{
                $pusher->addAlias([$alias]);   //要推送的用户，可以是数组 。一个设备只能绑定一个别名，但多个设备可以绑定同一个别名。一次推送最多 1000 个。
            }


            try {
                $response = $pusher->send();
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                // try something here
                log_message("test", "JPUSH {$alias}异常:".$e);
            } catch (\JPush\Exceptions\APIRequestException $e) {
                // try something here
                log_message("test", "JPUSH {$alias}异常:".$e);
            }
        }

    }
