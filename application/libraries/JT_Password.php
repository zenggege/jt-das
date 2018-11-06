<?php
/**
 *  
 * 
 */
class JT_Password  {

    const OP_VERIFY = 2;
    const OP_GENERATE_FE = 4;//前端
    const OP_GENERATE_BE = 8;//后端
    const OP_GENERATE_BE_BY_FE = 16;//根据前端生成后端密码

    var $privateKey = RSA_PRIVATE_KEY;
    var $publicKey = RSA_PUBLIC_KEY;
    var $error;
    var $CI;
    /**
     * 初始化
     */
    function __construct()
    {
        extension_loaded('openssl') or die('php需要openssl扩展支持');  

        $this->CI = & get_instance();
    }


    /**
     * 私钥解密 (可以用登录)
     * apiParam{string} [password] 当前RSA公钥加密的密文
     * apiParam{string} [encrypt_field] 原密文加密用的特殊字符
     * apiParam{string} [decrypt] 判断当前操作是加密还是解密， true为解密，false为加密，默认为加密
     * apiParam{string} [verify_password] 如果当前操作是解密，此字段为非空，加密则默认为空
     * apiParam{boolean}[other] 兼容第三方接口问题
     * @return [string] [description]
     */
    function work($op,$args) {
        
        $pi_key =  openssl_pkey_get_private($this->privateKey);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id  
        $pu_key = openssl_pkey_get_public($this->publicKey);//这个函数可用来判断公钥是否是可用的  
        if(!$pu_key || !$pi_key){
            $this->error = '系统错误(1)';
            return false;
        }
        

        //如果是验证
        if($op === self::OP_VERIFY||$op===self::OP_GENERATE_BE_BY_FE) {

            $username = $args['username'];
            $username = strtolower($username);
            $password_fe = $args['password_fe'];
            $encrypt_be = $args['encrypt_be'];

            
            #1. 前端传过一的参数需要是 username#密码加密文#timestamp#nonce
            #1 解密前端传过来的base64位的加密密文
            $decode64_password = base64_decode($password_fe);
            $decrypted = "";

            // log_message("error", "decode64_password = {$decode64_password}");
            // log_message("error", "decrypted = {$decrypted}");
            // log_message("error", "password_fe = {$password_fe}");

            // log_message("error", "privateKey = {$this->privateKey}");

            #2 私钥解密
            openssl_private_decrypt($decode64_password, $decrypted, $this->privateKey);

            

            if(!$decrypted){
                $this->error = '系统错误(2)';
                return false;
            }

            $decrypted_arr = explode("#", strtolower($decrypted));

            if(count($decrypted_arr)!=4) {
                $this->error = '系统错误(3)';
                return false;
            }

            $password = $decrypted_arr[1];

            if($username != $decrypted_arr[0]) {
                $this->error = '系统错误(4)';
                return false;
            }

            if ($op === self::OP_GENERATE_BE_BY_FE) {
                $args['password_origin'] = $password;
                $args['encrypt'] = $args['encrypt_be'];
                return $this->work(self::OP_GENERATE_BE, $args);
            } else {
                $password_be = $args['password_be'];
            }

            //-------- 要前端传一个当前时间 和 一个随机数过来

            //$timestamp = $args['timestamp'];
            //还可以变态一点，过了今天就不能用了如 判断 timestamp
            // if(SYS_TIME - $decrypted_arr[2] > 86400 ) {
            //     $this->error = '系统错误(5)';
            //     return false;
            // }

            //------------------------


            //两次md5加密，基本上没有人知道原文是哪个了
            $md5_password = md5(md5($password . $encrypt_be));

            //前后端对比
            if (password_verify($md5_password , $password_be)){
                return true;
            }else{  
                $this->error = '系统错误(7)';
                return false;
            }
        } elseif($op === self::OP_GENERATE_FE) {
            //生成前端密码，一般是不需要用到，除了调试的时候！！！
            $username = $args['username'];
            $password = $args['password_origin'];
            $timestamp = strtotime(date("Y-m-d H:i:s"));

            $nonce = random_string('alnum', 8);
            //username#密码加密文#timestamp#nonce
            $password =  $username."#".$password."#".$timestamp."#".$nonce;

            openssl_public_encrypt($password, $encrypted, $this->publicKey);

            return base64_encode($encrypted);  
        }  elseif($op === self::OP_GENERATE_BE) {

            $password = $args['password_origin'];
            $encrypt = $args['encrypt'];

            //两次md5加密，基本上没有人知道原文是哪个了
            $md5_password = md5(md5($password . $encrypt));
            //再加密一次，鬼知道原文是什么了
            $password_be = password_hash($md5_password, PASSWORD_DEFAULT);
            return $password_be;  
        }
        
    }


}