<?php 
namespace app\index\controller;
use think\Db;  
use Think\Controller;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

// Download：https://github.com/aliyun/openapi-sdk-php-client
// Usage：https://github.com/aliyun/openapi-sdk-php-client/blob/master/README-CN.md

//'LTAIG1qut1ilueJM', 'MYkKVva5W7sDSIX1sswnVrStjqNiuY'
class LoginStatu{
    //添加一个商品信息
    public function SendSMS($tel){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        AlibabaCloud::accessKeyClient('LTAIG1qut1ilueJM', 'MYkKVva5W7sDSIX1sswnVrStjqNiuY')
            ->regionId('cn-hangzhou')
            ->asGlobalClient();
        srand(time()); 
        $code = rand(0,999999);
        $code_str = sprintf('%06s', $code);
        try {
            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                                'query' => [
                                'RegionId' => 'cn-hangzhou',
                                'PhoneNumbers' => "$tel",
                                'SignName' => '杭州增信',
                                'TemplateCode' => 'SMS_158948248',
                                'TemplateParam' => "{\"code\":\"$code_str\"}",
                                ],
                            ])
                ->request();
        } catch (ClientException $e) {
            $rtn['result'] = 'faild';
        } catch (ServerException $e) {
            $rtn['result'] = 'faild';
        }
        return $rtn;
    }
    //检测code是否是tel得到的最后一个验证码
    public function CheckLatestSMSCode($tel,$code){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        AlibabaCloud::accessKeyClient('LTAIG1qut1ilueJM', 'MYkKVva5W7sDSIX1sswnVrStjqNiuY')
            ->regionId('cn-hangzhou') // replace regionId as you need
            ->asGlobalClient();

        try {
            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('QuerySendDetails')
                ->method('POST')
                ->options([
                    'query' => [
                    'PhoneNumber' => "$tel",
                    'SendDate' => date("Ymd"),
                    'PageSize' => '1',
                    'CurrentPage' => '0',
                    ],
                ])
                ->request();
            $result = $result->toArray();
            if(!array_key_exists("TotalCount", $result) || $result["TotalCount"] != 1){
                $rtn['result']="faild1";
            }else{
                
                if(!array_key_exists("SmsSendDetailDTOs", $result) ||   
                !array_key_exists("SmsSendDetailDTO", $result["SmsSendDetailDTOs"]) ||
                sizeof($result["SmsSendDetailDTOs"]["SmsSendDetailDTO"]) != 1
                ){
                    $rtn['result']="faild2";
                }else{
                    
                    if(!array_key_exists("Content", $result["SmsSendDetailDTOs"]["SmsSendDetailDTO"][0])){
                        $rtn['result']="faild3";
                    }else{
                        if($code != mb_substr($result["SmsSendDetailDTOs"]["SmsSendDetailDTO"][0]["Content"],11,6)){
                            $rtn['result']="faild4";
                        }
                    }
                }
            }
        } catch (ClientException $e) {
            $rtn['result']="faild";
        } catch (ServerException $e) {
            $rtn['result']="faild";
        }
        return $rtn;
    }
    
    public function Login($tel, $code, $uid){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        if($this->CheckLatestSMSCode($tel, $code)["result"] == "ok"){
            //登录成功
            $_SESSION["tel"] = $tel;
            $_SESSION["uid"] = $uid;
        }else{
            $rtn['result']="faild";
        }
        return $rtn;
    }
    public function LoginOut(){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        if(isset($_SESSION)){
            session_destroy();
        }
        
        return $rtn;
    }
    public function ChackState($uid){
        $rtn['result'] = 'ok';
        $rtn['data'] = '';
        if(array_key_exists("tel", $_SESSION)&&
            $_SESSION["uid"] == $uid){
            return true;
        }
        $this->LoginOut();
        return false;
    }
};