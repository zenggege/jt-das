<?php
/**
 * 员工类
 * 
 */
class V1_User  {
    // var $tips;
    var $CI;

    /**
     * 初始化
     */
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('User_model','Times_model'));
        $this->CI->load->library(['JT_Password']);
    }


    /**
     * 登录
    */
    function login($user_name,$password,$ip){
        if($user_name == ''){
            $this->tips = '用户名不能为空';
            return FALSE;
        }

        if($password == ''){
            $this->tips = '密码不能为空';
            return FALSE;
        }

        //密码错误剩余重试次数
        $rtime = $this->CI->Times_model->one(['user_name' =>$user_name,'is_admin'=>1]);
        $maxloginfailedtimes = 5;
        if ($rtime) {
            if ($rtime['failure_times'] >= $maxloginfailedtimes) {
                if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime('+ 0 day +1 hour +0 minute', strtotime($rtime['login_time'])))) {
                    $this->CI->Times_model->delete(
                            ['user_name' => $user_name,
                             'is_admin' => 1]);
                    $this->CI->Times_model->insert(
                            ['user_name' => $user_name,
                             'login_ip' => $ip,
                             'is_admin' => 1,
                             'login_time' => date("Y-m-d H:i:s"),
                             'failure_times' => 1]);
                } else {
                    $this->tips = '密码尝试次数过多，被锁定一个小时';
                    return FALSE;
                }

            }

        }
        //查询帐号，默认组1为超级管理员
        $r = $this->CI->User_model->one(array('username' => $user_name));
        if (!$r) {
            $this->tips = '用户名或密码不正确';
            return FALSE;
        }

        $check_password = $this->CI->jt_password->work(JT_Password::OP_VERIFY, ['username'=>$r['username'],'password_fe'=>$password, 'password_be'=>$r['password'], 'encrypt_be'=>$r['encrypt']]);

        if (!$check_password) {
            if ($rtime && $rtime['failure_times'] < $maxloginfailedtimes) {
                $times = $maxloginfailedtimes - intval($rtime['failure_times']);
                $this->CI->Times_model->update(
                        ['login_ip' => $ip,
                         'is_admin' => 1,
                         'failure_times' => $rtime['failure_times'] + 1,
                         'login_time' => date('Y-m-d H:i:s')],
                        ['user_name' => $user_name]);
                
            } else {
                $this->CI->Times_model->delete(
                        ['user_name' => $user_name,
                         'is_admin' => 1]);
                $this->CI->Times_model->insert(
                        ['user_name' => $user_name,
                         'login_ip' => $ip,
                         'is_admin' => 1,
                         'login_time' => date('Y-m-d H:i:s'),
                         'failure_times' => 1]);
                $times = $maxloginfailedtimes;
            }

            $this->tips = sprintf('用户名或密码不正确,您还有 %s 机会', $times);
            return FALSE;

        }

        $this->CI->Times_model->delete(['user_name' => $user_name]);
        if ($r['is_lock'] ) {
            $this->tips = '您的帐号已被锁定，请与工作人员联系';
            return FALSE;
        }

        $token = array(
            "admin_id" => $r['staff_id'],
            "password" => $r['password'],
            "user_name" => $r['username'],
            "group_id"=>$r['group_id'],
            "iat" => SYS_TIME,#jwt的签发时间
            // "exp" => SYS_TIME + EXPIRED_HOURS * 3600#jwt的过期时间，这个过期时间必须要大于签发时间
        );
        // $token = \Firebase\JWT\JWT::encode([$r['admin_id'], $r['password'], $email, $r['role_id'], SYS_TIME], RSA_PRIVATE_KEY, 'RS256');
        $jwt = \Firebase\JWT\JWT::encode($token, RSA_PRIVATE_KEY, 'RS256');

        $this->CI->User_model->update(['last_login_time'=>time()],['staff_id'=>$r['staff_id']]);
        $this->tips = '登录成功';
        $this->data = array('token' => $jwt,
                            'email' => $r['email'],
                            'fullname' => $r['fullname'],
                            // 'role_name'=>$this->CI->Role_model->field_value('role_name',['role_id'=>$r['role_id']]),
                            'avatar'=>$r['avatar']
                            );
        return TRUE;
    }


    /**
    * 修改登录用户密码
    *@param $user_id int 登录管理员ID
    *@param $old_password string 原密码
    *@param $new_password string 新密码
    *@param $rep_password string 确认新密码
    */
    function edit_long_user_password($user_id,$old_password,$new_password,$rep_password){
        $admin_info = $this->CI->User_model->one(['staff_id'=>$user_id]);
        if(!$admin_info){
            $this->tips = '信息不存在';
            return FALSE;
        }

        if($old_password == ''){
            $this->tips = '原密码不能为空';
            return FALSE;
        }
       
        $check_status = $this->CI->jt_password->work(JT_Password::OP_VERIFY, array('username'=>$admin_info['username'],'password_fe'=>$old_password, 'password_be'=>$admin_info['password'], 'encrypt_be'=>$admin_info['encrypt']));

        if(!$check_status){
            $this->tips = '原密码不正确';
            return false;
        }


        if($new_password == ''){
            $this->tips = '新密码不能为空';
            return FALSE;
        }

        if($rep_password == ''){
            $this->tips = '确认新密码不能为空';
            return FALSE;
        }

        #### 生成后端密码
        $encrypt = random_string('alnum', 5);
        $new_encrypt_password = $this->CI->jt_password->work(JT_Password::OP_GENERATE_BE_BY_FE , array('username'=>$admin_info['username'],'password_fe'=>$new_password, 'encrypt_be'=>$encrypt));
        if(!$new_encrypt_password){
            $this->tips = $this->CI->jt_password->error;
            return false;
        }


        $check_password = $this->CI->jt_password->work(JT_Password::OP_VERIFY, ['username'=>$admin_info['username'],'password_fe'=>$rep_password, 'password_be'=>$new_encrypt_password, 'encrypt_be'=>$encrypt]);
        if (!$check_password) {
            $this->tips = '新密码和确认新密码不一致';
            return FALSE;
        }

        $status = $this->CI->User_model->update(['password'=>$new_encrypt_password,
                                                  'encrypt'=>$encrypt,
                                                  'update_time'=>time()
                                                 ],['staff_id'=>$user_id]);
        if($status){
            $this->tips = '修改成功';
            return TRUE;
        }else{
            $this->tips = '修改失败';
            return FALSE;
        }

    }


    /**
    * 设置分页
    *@param $page int 当前页码
    *@param $per_page int 每页数量
    */
    function set_page($page,$per_page){
        $this->CI->User_model->page_index = max(intval($page),1);
        $this->CI->User_model->page_size = min(intval($per_page),100);        
    }

    /**
    * 设置分页
    *@param $filter_arr array 
    */
    function set_filter($filter_arr=[]){
        $where_arr = NULL;
        if(isset($filter_arr['group_id']) && $filter_arr['group_id'] >0){
            $where_arr[] = "group_id = {$filter_arr['group_id']}";
        }

        if(isset($filter_arr['keyword']) && $filter_arr['keyword'] != ''){
            $where_arr[] = "((email like '%{$filter_arr['keyword']}%') or (fullname like '%{$filter_arr['keyword']}%') or (mobile like '%{$filter_arr['keyword']}%'))";
        }
        $this->where = $where_arr?implode(" and ",$where_arr):NULL;
    }

    /**
    * 管理员列表
    */
    function user_list($fields = 'staff_id,username,email,mobile,sex,avatar,birthday,group_id,last_login_time,created,is_lock,last_login_ip,fullname,update_time',$order_by = 'staff_id asc'){
        $list_info = $this->CI->User_model->select($this->where,$fields,$order_by);
       
        if($list_info){
            foreach ($list_info as $key => $value) {
                $list_info[$key]['last_login_time'] = intval($value['last_login_time'])?date('Y-m-d H:i:s',$value['last_login_time']):'';
            }
        }

        return $list_info;
    }

    /**
    * 单条管理员信息
    *@param $user_id int 管理员信息ID
    */
    function get_user_info($user_id){
        $info = $this->CI->User_model->one(['staff_id'=>$user_id]);
        if(!$info){
            $this->tips = '信息不存在';
            return NULL;
        }else{
            unset($info['password']);
            unset($info['encrypt']);
            $this->tips = '数据获取成功';
            return $info;
        }
    }

    /**
    * 新增用户
    *@param $user_name string 用户名
    */
    function add($arr){
        extract($arr);
        if($user_name == ''){
            $this->tips = '用户名不能为空';
            return FALSE;
        }

        if($this->CI->User_model->count(['username'=>$user_name])){
            $this->tips = '用户名已存在';
            return FALSE;
        }

        if($new_password == ''){
            $this->tips = '新密码不能为空';
            return FALSE;
        }


        if($rep_password == ''){
            $this->tips = '确认密码不能为空';
            return FALSE;
        }

        #### 生成后端密码
        $encrypt = random_string('alnum', 5);
        $encrypt_password = $this->CI->jt_password->work(JT_Password::OP_GENERATE_BE_BY_FE , array('username'=>$user_name,'password_fe'=>$new_password, 'encrypt_be'=>$encrypt));

        if(!$encrypt_password){
            $this->tips = $this->CI->jt_password->error;
            return false;
        }


        $check_password = $this->CI->jt_password->work(JT_Password::OP_VERIFY, ['username'=>$user_name,'password_fe'=>$rep_password, 'password_be'=>$encrypt_password, 'encrypt_be'=>$encrypt]);
        if (!$check_password) {
            $this->tips = '新密码和确认新密码不一致';
            return FALSE;
        }

        if(!$group_id){
            $this->tips = '管理角色不能为空';
            return FALSE;
        }

        switch ($sex) {
            case 1:
                $sex = '男';
                break; 
            case 2:
                $sex = '女';
                break;                           
            default:
                $sex = '其他';
                break;
        }

        $new_id = $this->CI->User_model->insert( ['username'=>$user_name,
                                                  'password'=>$encrypt_password,
                                                  'email'=>$email,
                                                  'mobile'=>$mobile,
                                                  'sex'=>$sex,
                                                  'avatar'=>$avatar,
                                                  'group_id'=>$group_id,
                                                  'created'=>time(),
                                                  'fullname'=>$fullname,
                                                  'encrypt'=>$encrypt,
                                                 ]);
        if($new_id){
            $this->tips = '新增成功';
            return TRUE;
        }else{
            $this->tips = '新增失败';
            return FALSE;
        }        
    }

    /**
    * 修改用户
    *@param $user_name string 用户名
    */
    function update($arr){
        extract($arr);
        $info = $this->CI->User_model->one(['staff_id'=>$user_id]);
        if(!$info){
            $this->tips = '信息不存在';
            return FALSE;
        }

        if($user_name == ''){
            $this->tips = '用户名不能为空';
            return FALSE;
        }

        if($info['username'] != $user_name){
            if($this->CI->User_model->count(['username'=>$user_name])){
                $this->tips = '用户名已存在';
                return FALSE;
            }            
        }

        $encrypt = $info['encrypt'];
        $encrypt_password = $info['password'];
        if(($new_password != '') || ($rep_password != '')){
            
            $encrypt = random_string('alnum', 5);
            $encrypt_password = $this->CI->jt_password->work(JT_Password::OP_GENERATE_BE_BY_FE , array('username'=>$email,'password_fe'=>$new_password, 'encrypt_be'=>$encrypt,"timestamp"=>SYS_TIME,"alnum"=>$encrypt));
            if(!$encrypt_password){
                $this->tips = $this->CI->jt_password->error;
                return false;
            }


            $check_password = $this->CI->jt_password->work(JT_Password::OP_VERIFY, ['username'=>$email,'password_fe'=>$rep_password, 'password_be'=>$encrypt_password, 'encrypt_be'=>$encrypt]);
            if (!$check_password) {
                $this->tips = '两次重复密码不一致';
                return FALSE;
            }

        }

        if(!$group_id){
            $this->tips = '管理角色不能为空';
            return FALSE;
        }

        switch ($sex) {
            case 1:
                $sex = '男';
                break; 
            case 2:
                $sex = '女';
                break;                           
            default:
                $sex = '其他';
                break;
        }

        $status = $this->CI->User_model->update(['username'=>$user_name,
                                                  'password'=>$encrypt_password,
                                                  'email'=>$email,
                                                  'mobile'=>$mobile,
                                                  'sex'=>$sex,
                                                  'avatar'=>$avatar,
                                                  'birthday'=>$birthday,
                                                  'group_id'=>$group_id,
                                                  'fullname'=>$fullname,
                                                  'encrypt'=>$encrypt,
                                                  'update_time'=>time(),
                                                 ],['staff_id'=>$user_id]);
        if($status){
            $this->tips = '修改成功';
            return TRUE;
        }else{
            $this->tips = '修改失败';
            return FALSE;
        }

    }

    /**
    * 删除员工
    *@param $user_name string 用户名
    */
    function delete($ids,$current_user_id){
        $ids = trim($ids,',');
        if($ids == ''){
            $this->tips = '未选择任何数据';
            return FALSE;              
        }        

        $ids = explode(',',$ids);

        $count = count($ids);
        foreach ($ids as $key => $value) {
            $info = $this->CI->User_model->one(['staff_id'=>$value]);
            if(!$info){
                if($count > 1){
                    $this->tips = sprintf('第%s条信息不存在',$key+1);
                    return FALSE;                     
                }else{
                    $this->tips = '信息不存在';
                    return FALSE; 
                }
         
            }

            if($info['staff_id'] == $current_user_id){

                if($count >1){
                    $this->tips = sprintf('第%s条信息,不能删除自己',$key+1);
                    return FALSE;
                }else{
                    $this->tips = '不能删除自己';
                    return FALSE;                     
                }
            }
        }
        $where = $this->CI->User_model->spell_in($ids,'','staff_id');
        $result = $this->CI->User_model->delete($where);
        if($result){
            $this->tips = '删除成功';
            return TRUE;             
        }else{
            $this->tips = '删除失败';
            return FALSE;   
        }
    }



}    