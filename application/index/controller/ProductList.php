<?php 
namespace app\index\controller;
use think\Db;  
use Think\Controller;
require_once __DIR__."/Login.php";
class ProductList{
    //添加一个商品信息
    public function GetProductList($page_idx, $page_size){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        try{
            $sql_str="select * from product_list";

            $page_start = $page_idx*$page_size;
            $sql_str .= " limit $page_start, $page_size";
            $res = Db::query($sql_str);
            $rtn['data']=$res;
            $res = Db::query("select found_rows() as `count`");
            $rtn['all_count']=$res[0]['count'];
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }

    public function BuyProduct($product_id, $count, $uid){
        $caller = new LoginStatu();
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        if($caller->ChackState($uid) != true){
            $rtn['result']="权限错误";
            return $rtn;
        }
      
        //权限正确
        try{
            //锁表
            Db::execute("lock tables user_list write, product_list write");
            //计算用户资金
            $res = Db::query("select * from user_list where `user_name`='{$_SESSION['tel']}'");
            if(sizeof($res) != 1){
                $rtn['result'] != "未找到用户";
                return $rtn;
            }
            $wallet = $res[0]['wallet'];
            $user_id = $res[0]['index'];
            //计算产品价格
            $res = Db::query("select * from product_list where `index`=$product_id");
            if(sizeof($res) != 1){
                $rtn['result'] != "未找到用户";
                return $rtn;
            }
            $product_id = $res[0]['index'];
            $price = $res[0]['price']*$count;
            $product_count = $res[0]['count'];
            

            //判断产品数目是否够
            if($product_count <=$count ){
                $rtn["result"] = "剩余产品不足";
                return $rtn;
            }
            //判断钱
            if($wallet < $price){
                //钱不够
                $rtn["result"] = "账户余额不足";
                return $rtn;
            }
            
            //开始购买
            Db::startTrans();
            $wallet_now = $wallet-$price;
            //更新钱包
            Db::execute("update user_list set `wallet`='$wallet_now' where `user_name`='{$_SESSION['tel']}'");
            //更新货物
            $product_count = $product_count-$count;
            Db::execute("update product_list set `count`='$product_count' where `index`='$product_id'");
            //为用户添加订单
            $time = date("Ymd-His");
            Db::execute("insert into order_list(`product_id`,`user_id`, `product_count`,`buy_time`, `is_history`, `is_paid`)
            values($product_id,$user_id,'$count','$time', '0', '1')");
            Db::commit();
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }

    public function GetSelfWallet($uid){
        $caller = new LoginStatu();
        $rtn['result'] = 'ok';
        $rtn['data'] = array();
        if($caller->ChackState($uid) != true){
            $rtn['result']="权限错误";
            return $rtn;
        }
        try{
            //计算用户资金
            $res = Db::query("select * from user_list where `user_name`='{$_SESSION['tel']}'");
            if(sizeof($res) !=1){
                $rtn['result']="用户不存在";
                return $rtn;
            }
            $wallet_count = $res[0]['wallet'];
            //计算所持有产品的总价值
            $res = Db::query("select * from user_order_list where `user_name`='{$_SESSION['tel']}' and `is_history`='0'");
            $product_value = 0;
            $last_day_value = 0;
            $all_income = 0;
            for($i = 0; $i < sizeof($res); $i++){
                //产品价格
                $product_value += $res[$i]['price']*$res[$i]['product_count'];
                
                //计算相差天数
                
                $days = floor((strtotime("now")-strtotime(substr($res[$i]['buy_time'], 0, 8))-1)/(60*60*24));
                
                if($days <= 0){
                    $days=0;
                }else{
                    //次日开始计算利润

                    $product_value += $res[$i]['price']*((float)$res[$i]['income']/100.00)/365*$days*$res[$i]['product_count'];
                    $all_income += $res[$i]['price']*((float)$res[$i]['income']/100.00)/365*$days*$res[$i]['product_count'];
                    $last_day_value += $res[$i]['price']*((float)$res[$i]['income']/100.00)/365*$res[$i]['product_count'];
                }
            }
            
            //计算已经到期的利润
            $res = Db::query("select * from user_order_list where `user_name`='{$_SESSION['tel']}' and `is_history`='1'");
            for($i = 0; $i < sizeof($res); $i++){
                $all_income+=$res[$i]['price']*((float)$res[$i]['income']/100.00)/365*$res[$i]['time']*$res[$i]['product_count'];
            }
            
            $rtn['data']['wallet'] = sprintf("%.2f",$wallet_count) ;
            $rtn['data']['income_last_day'] =  sprintf("%.2f",$last_day_value);
            $rtn['data']['income_all'] =  sprintf("%.2f",$all_income) ;
            $rtn['data']['product_value'] =  sprintf("%.2f",$product_value);
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
    public function GetOrderList($uid){
        $caller = new LoginStatu();
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        if($caller->ChackState($uid) != true){
            $rtn['result']="权限错误";
            return $rtn;
        }
        try{
            //计算用户资金
            $res = Db::query("select * from user_list where `user_name`='{$_SESSION['tel']}'");
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
    public function AddProduct($barcode, $name, $unspsc, $brand_name, $model, $origin_country, $product_place, $product_country, $type_list){
        $rtn['result'] = 'ok';
        $barcode=trim($barcode);
        $name=trim($name);
        $unspsc=trim($unspsc);
        $brand_name =trim($brand_name);
        $model=trim($model);
        $origin_country=trim($origin_country);
        $product_place=trim($product_place);
        $product_country=trim($product_country);
        if($barcode == ''){
            $rtn['result'] = 'barcode不能为空';
            return $rtn;
        }
        if(sizeof($type_list) == 0){
            $rtn['result'] = '必须设置所属分类';
            return $rtn;
        }
        for($i = 0; $i < 10; $i++){
            if($i < sizeof($type_list)){
                $type_list[$i] = trim($type_list[$i]);
            }else{
                $type_list[$i]="";
            }
        }
        $txid = "0";
        $timestamp=date("Y/m/d H:m:s");
        {
            $block_chain=new BlockChain();
            $key = array("product_info", $barcode);
            $value['name']=$name;
            $value[`unspsc`]=$unspsc;
            $value[`brand_name`]=$brand_name;
            $value[`model`]=$model;
            $value[`origin_country`]=$origin_country;
            $value[`product_place`]=$product_place;
            $value[`product_country`]=$product_country;
            $value[`typeid1`]=$type_list[0];
            $value[`typeid2`]=$type_list[1];
            $value[`typeid3`]=$type_list[2];
            $value[`typeid4`]=$type_list[3];
            $value[`typeid5`]=$type_list[4];
            $value[`typeid6`]=$type_list[5];
            $value[`typeid7`]=$type_list[6];
            $value[`typeid8`]=$type_list[7];
            $value[`typeid9`]=$type_list[8];
            $value[`typeid10`]=$type_list[9];
            $block_res=$block_chain->setCompositeKeyValueSelf("product_info", $barcode, $value);
            if(array_key_exists('success', $block_res) && $block_res['success']=='true'){
                $timestamp=date("Y/m/d H:m:s",strtotime($block_res['message']['timestamp']));
                $txid=$block_res['message']['txid']; 
                $rtn['data']['txid']=$txid;
                $rtn['data']['timestamp']=$timestamp;
            }
        }
        try{
            $sql_str = "insert into product_info(`barcode`, `name`, `unspsc`, `brand_name`, `model`, `origin_country`, `product_place`, `product_country`,
            `typeid1`,`typeid2`,`typeid3`,`typeid4`,`typeid5`,`typeid6`,`typeid7`,`typeid8`,`typeid9`,`typeid10`,`modify_time`,`txid`)
            values('$barcode', '$name', '$unspsc', '$brand_name', '$model', '$origin_country', '$product_place', '$product_country', 
            '{$type_list[0]}','{$type_list[1]}','{$type_list[2]}','{$type_list[3]}','{$type_list[4]}',
            '{$type_list[5]}','{$type_list[6]}','{$type_list[7]}','{$type_list[8]}','{$type_list[9]}','$timestamp','$txid')";
            $res = Db::execute($sql_str);
            
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
    public function QueryProduct($type_list, $barcode, $seller_name, $page_idx, $page_size){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        try{
            $sql_str = "
            select sql_calc_found_rows product_info.*,";
            if($seller_name != ''){
                $sql_str.="case when (select count(*) from seller_product_info where `seller_name` ='$seller_name' and product_barcode = product_info.barcode)>0 then 1 else 0 end as is_uped,";
            }
            $sql_str.="
            ifnull(p1.`name`,'') as `name1`,
            ifnull(p2.`name`,'') as `name2`,
            ifnull(p3.`name`,'') as `name3`,
            ifnull(p4.`name`,'') as `name4`,
            ifnull(p5.`name`,'') as `name5`,
            ifnull(p6.`name`,'') as `name6`,
            ifnull(p7.`name`,'') as `name7`,
            ifnull(p8.`name`,'') as `name8`,
            ifnull(p9.`name`,'') as `name9`,
            ifnull(p10.`name`,'') as `name10`
            from product_info 
            
            left join product_type as p1
            on p1.id=product_info.typeid1
            left join product_type as p2
            on p2.id=product_info.typeid2
            left join product_type as p3
            on p3.id=product_info.typeid3
            left join product_type as p4
            on p4.id=product_info.typeid4
            left join product_type as p5
            on p5.id=product_info.typeid5
            left join product_type as p6
            on p6.id=product_info.typeid6
            left join product_type as p7
            on p7.id=product_info.typeid7
            left join product_type as p8
            on p8.id=product_info.typeid8
            left join product_type as p9
            on p9.id=product_info.typeid9
            left join product_type as p10
            on p10.id=product_info.typeid10
            where `barcode` like '%$barcode%' ";
            for($i=0; $i < sizeof($type_list); $i++){
                $sql_str.=" and `typeid".($i+1)."`='{$type_list[$i]}'";
            }
            $page_start = $page_idx*$page_size;
            $sql_str .= " limit $page_start, $page_size";
            $res = Db::query($sql_str);
            $rtn['data']=$res;
            $res = Db::query("select found_rows() as `count`");
            $rtn['all_count']=$res[0]['count'];
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
    public function EditProduct($barcode, $name, $unspsc, $brand_name, $model, $origin_country, $product_place, $product_country, $type_list){
        $rtn['result'] = 'ok';
        $barcode=trim($barcode);
        $name=trim($name);
        $unspsc=trim($unspsc);
        $brand_name =trim($brand_name);
        $model=trim($model);
        $origin_country=trim($origin_country);
        $product_place=trim($product_place);
        $product_country=trim($product_country);
        if($barcode == ''){
            $rtn['result'] = 'barcode不能为空';
            return $rtn;
        }
        if(sizeof($type_list) == 0){
            $rtn['result'] = '必须设置所属分类';
            return $rtn;
        }
        for($i = 0; $i < 10; $i++){
            if($i < sizeof($type_list)){
                $type_list[$i] = trim($type_list[$i]);
            }else{
                $type_list[$i]="";
            }
        }
        $txid = "0";
        $timestamp=date("Y/m/d H:m:s");
        {
            $block_chain=new BlockChain();
            $key = array("product_info", $barcode);
            $value['name']=$name;
            $value[`unspsc`]=$unspsc;
            $value[`brand_name`]=$brand_name;
            $value[`model`]=$model;
            $value[`origin_country`]=$origin_country;
            $value[`product_place`]=$product_place;
            $value[`product_country`]=$product_country;
            $value[`typeid1`]=$type_list[0];
            $value[`typeid2`]=$type_list[1];
            $value[`typeid3`]=$type_list[2];
            $value[`typeid4`]=$type_list[3];
            $value[`typeid5`]=$type_list[4];
            $value[`typeid6`]=$type_list[5];
            $value[`typeid7`]=$type_list[6];
            $value[`typeid8`]=$type_list[7];
            $value[`typeid9`]=$type_list[8];
            $value[`typeid10`]=$type_list[9];
            $block_res=$block_chain->setCompositeKeyValueSelf("product_info", $barcode, $value);
            if(array_key_exists('success', $block_res) && $block_res['success']=='true'){
                $timestamp=date("Y/m/d H:m:s",strtotime($block_res['message']['timestamp']));
                $txid=$block_res['message']['txid']; 
                $rtn['data']['txid']=$txid;
                $rtn['data']['timestamp']=$timestamp;
            }
        }
        try{
            $sql_str = "update  product_info set `name`='$name', `unspsc`='$unspsc', `brand_name`='$brand_name', `model`='$model', 
            `origin_country`='$origin_country', `product_place`='$product_place', `product_country`='$product_country',
            `typeid1`='{$type_list[0]}',`typeid2`='{$type_list[1]}',`typeid3`='{$type_list[2]}',`typeid4`='{$type_list[3]}',`typeid5`='{$type_list[4]}',
            `typeid6`='{$type_list[5]}',`typeid7`='{$type_list[6]}',`typeid8`='{$type_list[7]}',`typeid9`='{$type_list[8]}',`typeid10` ='{$type_list[9]}',
            `modify_time`='$timestamp',`txid`='$txid'
            where `barcode`='$barcode'";
            $res = Db::execute($sql_str);
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
    public function DeleteProduct($barcode){
        $rtn['result'] = 'ok';
        if($barcode == ''){
            $rtn['result'] = 'barcode不能为空';
            return $rtn;
        }
        try{
            $sql_str = "delete from product_info where `barcode`='$barcode'";
            $res = Db::execute($sql_str);
            if($res){
                $block_chain=new BlockChain();
                $key = array("product_info", $barcode);
                $block_chain->deleteCompositeKeyValue($key);
            }
        }catch(\Exception $e){
            $rtn['result'] = $e->getMessage();
        }
        return $rtn;
    }
};