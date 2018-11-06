<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'core/REST_Controller.php');

class JT_Controller extends REST_Controller
{
    public $read_db = null;
    public $write_db = null;
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 兼容之前的做了一个别名
     * 
     */
    function respond($data, $http_code = HTTP_OK){
        $this->response($data, $http_code);
        exit;
    }

    // ------------------------------------------------------------------------

    /**
    * 设置公开方法
    *
    * 设置了以后，这些方法可以不需要登录
    * 
    *
    * @return  void
    */

    final function set_public_method($method_name){

        if(is_array($method_name)){
            foreach ($method_name as $key => $value) {
                $this->set_public_method($value);
            }
        }else
            if(trim($method_name)!="") $this->public_method[] = $method_name;
    }

    // ------------------------------------------------------------------------

    /**
    * 判断需不需要登录
    *
    * 判断URL中有没有含有secure的字样
    * if not, return FALSE
    *
    * @return  bool
    */

    final function _is_public_access() {

        if(in_array(trim($this->router->method),$this->public_method)) return true;
        return false;
    }


}


/**
* 后台管理
*/
class Admin_Controller extends JT_Controller
{   

    protected $public_method = [];
    public $current_member_info = [];
    public $admin_id;
    
    function __construct($method_name='')
    {
        parent::__construct();
        $this->load->model(array('User_model'));
        if($method_name) $this->set_public_method($method_name);
        //如果是开放性的，就不用登录
        if(!$this->_is_public_access()) {
            $this->_check_token();
        }
    }


    // ------------------------------------------------------------------------

    /**
    * 检查header中有没有token
    *
    * 先判断一下是否登录
    * if not, return FALSE
    *
    * @return  bool
    */

    final function _check_token() {

        $requestHeaders = apache_request_headers();
        if (!isset($requestHeaders['Authorization']) && !isset($requestHeaders['authorization'])) {
            $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
        }

        $authorizationHeader = isset($requestHeaders['Authorization'])?$requestHeaders['Authorization']:$requestHeaders['authorization'];

        if ($authorizationHeader == null) {
            $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
        }

        $authorizationHeader = str_replace('Bearer ', '', $authorizationHeader);
        try{
            $decoded = \Firebase\JWT\JWT::decode( $authorizationHeader, RSA_PUBLIC_KEY, array('RS256'));
            $decoded = (array) $decoded;
            //如果可以解出来
            if(is_array($decoded)){
                $this->_check_and_set_user_info($decoded);
            }else{
                $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
            }

        }catch(UnexpectedValueException $ex){
            $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
        }catch(DomainException $ex){
            $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
        }catch(ExpiredException $ex){//token过期
            $this->respond(array('status'=>false,'tips'=>'请重新登录'), self::HTTP_UNAUTHORIZED);
        }

    }

    // ------------------------------------------------------------------------

    /**
    * 检查和设置用户信息
    *
    * 这里有点点多余，实际上能破解JWT就机率很低，再到这里，再判断
    *
    * @return  bool
    */
    function _check_and_set_user_info($decode_token_arr)
    {
        if(count($decode_token_arr)<5){;
            $this->respond(array('status'=>false,'tips'=>'未认证'), HTTP_UNAUTHORIZED);
        }

        $this->admin_id = $decode_token_arr['admin_id'];
        $admin_password = $decode_token_arr['password'];
        $this->user_name = $decode_token_arr['user_name'];
        $this->group_id =  $decode_token_arr['group_id'];
        $this->login_time = $decode_token_arr['iat'];

        $admin_info = $this->User_model->one(array('username'=>$this->user_name,'password'=>$admin_password));
        if(!$admin_info){
            $this->respond(array('status'=>false,'tips'=>'用户不存在'), self::HTTP_UNAUTHORIZED);
        }

        if($admin_info['is_lock']){
        
            $this->respond(array('status'=>false,'tips'=>'您的帐号被锁定，无法操作'), self::HTTP_UNAUTHORIZED);
        }

        unset($admin_info['password']);//密码就不要存了
        $this->current_member_info = $admin_info;

    }
}


/**
 * 主要是对外对接用的
 */
class Partner_Controller  extends JT_Controller {


    var $language;
    
    public function __construct($method_name='') {
        parent::__construct();
        $this->load->model("Partner_app_model");
        if($method_name) $this->set_public_method($method_name);
        //如果是开放性的，就不用登录
        if(!$this->_is_public_access()) {
            $this->_check_token();
        }
    }


    // ------------------------------------------------------------------------

    /**
    * 检查和设置用户信息
    *
    * 这里有点点多余，实际上能破解JWT就机率很低，再到这里，再判断
    *
    * @return  bool
    */
    function _check_token(){

        $sign = trim($this->input->post_get("sign",true));
        $app_key = trim($this->input->post_get("app_key",true));
        $timestamp = intval($this->input->post_get("timestamp",true));
        $language = trim($this->input->post_get("language",true));



        if($app_key == "") {
            $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('app_key不能为空')), 401);
        }
        $post_data = isset($_POST['sign'])?$_POST:$_GET;
        
        unset($post_data['sign']);
        ksort($post_data);


        if($timestamp <= 0) {
            $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('timestamp不能为空')), 401);
        }

        $max_expire = 120; // 秒
        if (ENVIRONMENT!='production')
            $max_expire *=100; //开发环境20分钟过期
        if(abs(SYS_TIME - $timestamp) > $max_expire) {
            if("debug"== trim($this->router->method) && ENVIRONMENT != 'production' ) {
                $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('timestamp过期'). lang("服务器时间戳").":".SYS_TIME), 401);
            } else {
                $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('timestamp过期')), 401);
            }
        }

        //在这里要查询一下KEY的是否正常
        $info = $this->Partner_app_model->get_one(array('app_key'=>$app_key));

        if(!$info){
            $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('app_key无效')), 401);
        }

        

        // 不知道为啥要把%2C替换为, 先去掉了
        $_sign= $info['app_secret'].http_build_query($post_data);
        $sign_str = md5($_sign);

        //如果签名不一样
        if($sign_str!=$sign) {

            if("debug"== trim($this->router->method) && ENVIRONMENT != 'production' ) {
                $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('签名不一致').lang('你的是').$sign.'; '.lang('md5前的串应该是').$_sign.' , '.lang('服务器生成的md5是').':'.$sign_str."; ".lang('timestamp应是').":".SYS_TIME), 200);
            } else {
                $this->respond(array('status'=>false,'tips'=>lang('非法请求'). lang('签名不一致')), 200);
            }
            
        }

    }

}
