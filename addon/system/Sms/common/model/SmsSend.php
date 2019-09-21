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

namespace addon\system\Sms\common\model;

use addon\system\Sms\common\model\Message as SmsMessage;

/**
 * 发送短信
 * @author Administrator
 *
 */

class SmsSend
{
    
    /**
     * 站点发送短信
     * @param unknown $params
     */
    public function send($params){
        // 获取模板信息
        $sms_message_model = new SmsMessage();
        $message_info = $sms_message_model->getSmsMessageInfo(["keyword" => $params["keyword"], "sms_addon" => $params["addon_name"], "site_id" => $params["site_id"]]);
        if(empty($message_info["data"]))
            return error('', '站点当前启用短信发送方式没有配置当前消息类型的模板');
            
        if(empty($message_info["data"]["code"]))
            return error();
                
//                 if(empty($message_info["data"]["var_parse"]))
//                     return error();
        //将用户自由设置的变量名解析完整
//         $var_parse = $message_info["data"]["var_parse"];
        $var_array = [];
        if(!empty($message_info["data"]["var_parse"])){
            foreach($params["var_parse"] as $k => $v){
                $key = $message_info["data"]["var_parse"][$k];
                if(empty($key)){ 
                    return error('', $message_info["data"]["title"]."变量解析未配置完善");
                }
                $var_array[$key] = $v;
            }
        }
        
        $params["var_parse"] = json_encode($var_array);//变量解析
        $params["addon_name"] = $params["addon_name"];//默认开启的短信方式
        $params["code"] = $message_info["data"]["code"];
        $result = hook("sendSmsMessage", $params);
        if(empty($result[0]))
            return error();

        return $result[0];
    }

    
    /**
     * 获取现在启用的消息发送方式
     * @param unknown $param
     */
    public function getSmsAddon($param = []){
        $result = hook("getSmsConfig", ["site_id" => $param["site_id"]]);
        if(empty($result[0])) return error();
        //查询默认的短信
        $addon_name = "";
        foreach($result as $k => $v){
            if(!empty($v)){
                if($v['config']['status'] == 1){
                    $addon_name = $v["info"]["name"];
                }
            }
        }
        if(empty($addon_name))
            return error();
        
        return success($addon_name);
    }

    /**
     * 短信控制发送
     *
     * @param array $param
     * @return \multitype
     */
    public function sendSms($param = []){
        $site_id = $param["site_id"];
        $id = $param["id"];
        $message_records_model = new MessageRecords();
        $records_info_result = $message_records_model->getSmsMessageReocrdsInfo(["site_id" => $site_id, "id" => $id]);
        $records_info = $records_info_result["data"];

        $sms_send = new SmsSend();
        $addon_result = $sms_send->getSmsAddon(["site_id" => $site_id]);//获取可用的短信
        if ($addon_result["code"] != 0)
            return error('', '当前站点未安装短信插件或者未启用');

        $addon_name = $addon_result["data"];//正启用的短信方式

        if(empty($records_info))
            return error();

        $params = array(
            "site_id" => $site_id,
            "keyword" => $records_info["keyword"],
            "var_parse" => $records_info["var_parse"],
            "account" => $records_info["account"],
            "addon_name" => $addon_name
        );
        $result = $sms_send->send($params);
        $data = array();
        $condition = array(
            "id" => $records_info["id"],
            "site_id" => $this->siteId
        );
        if ($result["code"] == 0) {
            $data["send_time"] = time();
            $data["stauts"] = 1;
        } else {
            $data["stauts"] = -1;
            $data["result"] = $result["message"];
        }

        $message_records_model->editSmsMessageReocrds($data, $condition);

        return $result;
    }
}