<?php
namespace app\index\controller;
use think\Db;  
require_once __DIR__."/ProductList.php";
require_once __DIR__."/Login.php";
class Index
{
    public function index()
    {
        echo '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }
    //查看产品列表
    public function product_list_get(){
        $param = $_POST;
        $caller = new ProductList();
        echo json_encode($caller->GetProductList($param['page_idx'], $param['page_size']), JSON_UNESCAPED_UNICODE);
    }
    //购买一个产品
    public function buy_product(){
        $param = $_POST;
        $caller = new ProductList();
        echo json_encode($caller->BuyProduct($param['product_id'], $param['count'], $param['uid']), JSON_UNESCAPED_UNICODE);
    }
    public function get_order_list(){

    }
    public function get_self_wallet(){

    }
    public function send_sms(){
        $param = $_POST;
        $caller = new LoginStatu();
        echo json_encode($caller->SendSMS($_POST['tel']), JSON_UNESCAPED_UNICODE);
    }
    public function check_last_sms_code(){
        $param = $_POST;
        $caller = new LoginStatu();
        echo json_encode($caller->CheckLatestSMSCode($param['tel'], $param['code']), JSON_UNESCAPED_UNICODE);
    }
    public function login(){
        $param = $_POST;
        $caller = new LoginStatu();
        echo json_encode($caller->Login($param['tel'], $param['code'], $param['uid']), JSON_UNESCAPED_UNICODE);
    }
    public function check_state(){
        $param = $_POST;
        $caller = new LoginStatu();
        echo json_encode($caller->ChackState($param['uid']), JSON_UNESCAPED_UNICODE);
    }
    public function login_out(){
        $param = $_POST;
        $caller = new LoginStatu();
        echo json_encode($caller->LoginOut(), JSON_UNESCAPED_UNICODE);
    }
    
}