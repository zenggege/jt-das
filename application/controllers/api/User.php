<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 员工管理
 */
class User extends Admin_Controller
{
    function __construct()
    {       

        parent::__construct(['login','generate_password']);
        $this->load->library(['V1_User']);

    }
    ##方便测试用
    function generate_password_post(){
        //生成一个前端密码
        echo $password_fe = $this->jt_password->work(JT_Password::OP_GENERATE_FE , array('username'=>'111','password_origin'=>'123456'));#
        echo "\n";
        echo "\n";

        //生成一个后端密码
        echo $password_be = $this->jt_password->work(JT_Password::OP_GENERATE_BE, array('username'=>'testtest@qq.com','password_origin'=>'123456', 'encrypt'=>'123b1'));

        echo "\n";
        echo "\n";

        //前后端的对比
        echo $this->jt_password->work(JT_Password::OP_VERIFY, array('username'=>'testtest@qq.com','password_fe'=>$password_fe, 'password_be'=>$password_be, 'encrypt_be'=>'123b1'))?"匹配":"不匹配:".$this->jt_password->error;
        echo "\n";
        echo "\n";

        //根据前端密码生成后端密码
        //123b1为随机生成
        echo $password_fe = $this->jt_password->work(JT_Password::OP_GENERATE_BE_BY_FE , array('username'=>'18173615961','password_fe'=>$password_fe, 'encrypt_be'=>'123b1'));
        echo "\n";
        echo "\n";

        die();
    }
    // function login_get(){
    //     echo "here";
    // }

    function login_post(){#登录
        $user_name = trim($this->post('user_name',true));#用户名
        $password = trim($this->post('password',true));#密码
        $ip = $this->input->ip_address();

        $result = $this->v1_user->login($user_name,$password,$ip);
        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_user->tips,'data'=>$this->v1_user->data],HTTP_OK);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_user->tips,'data'=>[]],HTTP_BAD_REQUEST);
        }
    }

    function password_put(){#修改登录员工密码
        $old_password = trim($this->put('old_password',true));#原密码
        $new_password = trim($this->put('new_password',true));#新密码
        $rep_password = trim($this->put('rep_password',true));#确认新密码

        $result = $this->v1_user->edit_long_user_password($this->admin_id,$old_password,$new_password,$rep_password);

        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_user->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_user->tips],HTTP_BAD_REQUEST);
        }
    }

    function index_get(){#员工列表
    	$page = intval($this->get('page',true));#当前页码
        $per_page = intval($this->get('per_page',true));#每页数量
        $group_id = intval($this->get('group_id',true));#管理员组
        $keyword = safe_replace(trim($this->get('keyword',true)));#关键词

        if(!$per_page) $per_page = 10;
        $this->v1_user->set_page($page,$per_page);
        $this->v1_user->set_filter(['group_id'=>$group_id,'keyword'=>$keyword]);
        $data = $this->v1_user->user_list();
        
        $this->respond(['status'=>true,
                        'tips'=>'数据获取成功',
                        'data'=>$data,
                        'data_count'=>intval($this->User_model->data_count),
                        'page_count'=>intval($this->User_model->page_count),
                        'page_index'=>$this->User_model->page_index,
                        'page_size'=>$this->User_model->page_size],HTTP_OK);
    }

    function info_get($user_id=0){
    	$user_id = intval($user_id);#员工信息ID
        if ($user_id==0){
            $user_id = $this->user_id;
        }
        $data = $this->v1_user->get_user_info($user_id);
        if($data){
            $this->respond(['status'=>true,'tips'=>$this->v1_user->tips,'data'=>$data],HTTP_OK);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_user->tips,'data'=>$data],HTTP_BAD_REQUEST);
        }
    }

    function info_post(){#新增员工
        $user_name = trim($this->post('user_name',true));#用户名
        $new_password = trim($this->post('new_password',true));#新密码
        $rep_password = trim($this->post('rep_password',true));#确认密码
        $group_id = intval($this->post('group_id',true));#管理角色
        $fullname = trim($this->post('fullname',true));#姓名
        $email = trim($this->post('email',true));#邮箱
        $sex = intval($this->post('sex',true));#性别[1:男2:女0:其他]
        $mobile = trim($this->post('mobile',true));#联系电话
        $avatar = trim($this->post('avatar',true));#头像

        $arr = compact('user_name','new_password','rep_password','group_id','fullname','email','sex','mobile','avatar');
        $result = $this->v1_user->add($arr);

        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_user->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_user->tips],HTTP_BAD_REQUEST);
        }
    }

    function info_put(){#修改员工
        $user_id = trim($this->put('user_id',true));#员工ID
        $user_name = trim($this->put('user_name',true));#用户名
        $new_password = trim($this->put('new_password',true));#新密码
        $rep_password = trim($this->put('rep_password',true));#确认密码
        $group_id = intval($this->put('group_id',true));#管理角色
        $fullname = trim($this->put('fullname',true));#姓名
        $email = trim($this->put('email',true));#邮箱
        $sex = intval($this->put('sex',true));#性别[1:男2:女0:其他]
        $mobile = trim($this->put('mobile',true));#联系电话
        $avatar = trim($this->put('avatar',true));#头像

        $arr = compact('user_id','user_name','new_password','rep_password','group_id','fullname','email','sex','mobile','avatar');
        $result = $this->v1_user->update($arr);
        if($result){
            $this->respond(['status'=>true,'tips'=>$this->v1_user->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->v1_user->tips],HTTP_BAD_REQUEST);
        }
    }

    function info_delete($ids=''){#删除员工
        $result = $this->v1_user->delete($ids,$this->user_id);
        if($result){
            $this->respond(['status'=>true,'tips'=>$this->lghz_admin->tips],HTTP_CREATED);
        }else{
            $this->respond(['status'=>false,'tips'=>$this->lghz_admin->tips],HTTP_BAD_REQUEST);
        }
    }



}    