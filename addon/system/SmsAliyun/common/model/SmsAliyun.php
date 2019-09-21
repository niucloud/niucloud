<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\system\SmsAliyun\common\model;

use addon\system\SmsAliyun\common\sdkNew\Profile\DefaultProfile;
use addon\system\SmsAliyun\common\sdkNew\DefaultAcsClient;
use addon\system\SmsAliyun\common\sdkNew\SendSmsRequest;
use app\common\model\Site;

/**
 * 阿里云短信
 * @author wyc
 */
class SmsAliyun
{
    
    /**
     * 阿里云短信发送
     * @param array $params
     */
    public function aliyunSmsSend($params = []){
        $sms_param = array(
            "code" => $params["code"],
            "account" => $params["account"],
            "site_id" => $params["site_id"],
            "var_parse" => $params["var_parse"]
        );
        $result = $this->send($sms_param);
        return $result;
    }
    
    
    /**
     * 短信发送
     * @param unknown $param
     */
    function send($param){
        $smsaliyun_model = new SmsAliyunConfig();
        $sms_config = $smsaliyun_model->getSmsAliyunConfig(0);
        if(empty($sms_config["data"])) return error('', '站点尚未配置阿里云短信');
        if(empty($sms_config["data"]['status'])) return error('', '站点尚未开启阿里云短信');
        $config = $sms_config["data"]['value'];//阿里云短信配置
        if(empty($config)) return error('', '站点尚未配置完善阿里云短信');
        
        $sign_name = $config["signature"];//短信签名
        $site_model = new Site();
        $site_config_result = $site_model->getSiteConfigInfo(["site_id" => $param["site_id"], "name" => "NC_SITE_SIGNATURE"]);
        $site_config = $site_config_result["data"];
        //如果当前站点配置了签名 就用当前站点的签名, 如果当前站点没有配置就使用总平台配置的
        if(!empty($site_config["value"])){
            $sign_name = $site_config["value"];//短信签名
        }
        // 发送短信
        $send_res = $this->aliSmsSendNew($config['app_key'], $config['secret_key'], $sign_name, $param["var_parse"], $param['account'], $param["code"]);
        $status = $send_res->Message == 'OK' ? 1 : -1;
        
        if($status  == 1){
            return success(["code" => $param["code"], "sign" => $sign_name, "type_name" => "阿里云短信"]);
        }else{
            return error('', $send_res->Message);
        }
    }
    
    
    /**
     * 阿里大于新用户发送短信
     * @param unknown $appkey
     * @param unknown $secret
     * @param unknown $signName
     * @param unknown $smsParam
     * @param unknown $send_mobile
     * @param unknown $template_code
     */
    function aliSmsSendNew($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
    {
        require("./addon/system/SmsAliyun/common/sdkNew/Config.php");
//        require("addon/system/SmsAliyun/common/sdkNew/SendSmsRequest.php ");

        // 短信API产品名
        $product = "Dysmsapi";
        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        
        $profile = DefaultProfile::getProfile($region, $appkey, $secret);
        
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient = new DefaultAcsClient($profile);
        
        $request = new SendSmsRequest();
        // 必填-短信接收号码
        $request->setPhoneNumbers($send_mobile);
        // 必填-短信签名
        $request->setSignName($signName);
        // 必填-短信模板Code
        $request->setTemplateCode($template_code);
        // 选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam($smsParam);
        // 选填-发送短信流水号
        $request->setOutId("0");
        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        return $acsResponse;
    }
    
}