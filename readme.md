  
core
    REST_Controller.php
    JT_Controller.php
        Admin_Controller
        Partner_Controller

models
    Mall_parking_model
    Mall_vip_model
    Mall_vip_report_model
    Attachemnt_model
    Mall_vip_point_model
    Passerenger_flow_model
    Shop_model
    Shop_trade_model
    Traffic_flow_model

//尽量不要在controller操作model
controllers
    Cli
        Message
        Test
    Open

    Api
        Shop
            index_get     店铺列表
            info_get      单个店铺详细
            info_post     新增店铺
            info_put      修改店铺
            info_delete   删除店铺
        Shop_trade
            index_get     店铺数据列表
            info_get      单个店铺数据详细
            info_post     新增店铺数据
            info_put      修改店铺数据
            info_delete   删除店铺数据

        User
            login_post    登录
            forget_post   找回密码
            index_get     用户列表
            info_get      单个用户详细
            info_post     新增用户
            info_put      修改用户
            info_delete   删除用户

        Vip_point
            index_get     会员积分列表
            info_get      单个店铺积分月份积分详细
            info_post     新增店铺积分月份数据
            info_put      修改店铺积分月份数据
            info_delete   删除店铺积分月份数据

        Parking_report
            index_get     停车场积分列表
            info_get      单个停车场积分月份积分详细
            info_post     新增停车场积分月份数据
            info_put      修改停车场积分月份数据
            info_delete   删除停车场积分月份数据
        
        Traffic_flow
            index_get     车流列表
            info_get      单个车流日期详细
            info_post     新增车流日期详细
            info_put      修改车流日期详细
            info_delete   删除车流日期详细

        Passenger_flow
            index_get     人流列表
            info_get      单个人流日期详细
            info_post     新增人流日期详细
            info_put      修改人流日期详细
            info_delete   删除人流日期详细



libries
    V1_Shop.php            店铺管理
        add
        update
        delete
        search
        filter
    V1_Shop_trade.php      交易管理
    V1_User.php            用户管理
    V1_Vip_point.php       订单管理
    V1_Parking_report.php  停车场积分报告
    V1_Traffic_flow.php    车流管理
    V1_Passenger_flow.php  客流管理

