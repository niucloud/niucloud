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

namespace addon\system\SmsAliyun\admin\controller;

use addon\system\Message\common\model\SiteMessage;
use addon\system\SmsAliyun\common\model\SmsAliyunConfig;
use addon\system\Sms\common\model\Message as SmsMessage;
use app\common\controller\BaseAdmin;

class Index extends BaseAdmin
{
	
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->siteId = 0;
		$this->replace = [
			'ADDON_NS_SMS_ALIYUN_IMG' => __ROOT__ . '/addon/system/SmsAliyun/admin/view/public/img',
			'ADDON_NS_SMS_ALIYUN_JS' => __ROOT__ . '/addon/system/SmsAliyun/admin/view/public/js',
		];
	}
	
	/**
	 * 阿里大于短信配置
	 */
	public function config()
	{
		$site_id = $this->siteId;
		$aliyun_config = new SmsAliyunConfig();
		if (IS_AJAX) {
			
			$app_key = input('app_key', '');
			$secret_key = input('secret_key', '');
			$signature = input('signature', '');
			$status = input('status', 0);
			$value = array(
				"app_key" => $app_key,
				"secret_key" => $secret_key,
				"signature" => $signature,
			);
			$value_json = json_encode($value);
			$data = array(
				"value" => $value_json,
				"site_id" => $site_id,
				"status" => $status,
				"update_time" => time(),
			);
			$res = $aliyun_config->setSmsAliyunConfig($data);
			return $res;
		} else {
			$config = $aliyun_config->getSmsAliyunConfig($site_id);
			
			$this->assign('status', $config['data']['status']);
			$this->assign('list', $config['data']['value']);
			return $this->fetch('index/config', [], $this->replace);
		}
		
	}

    /**
     * 短信模板编辑
     */
	public function template(){
        $site_id = 0;
        $sms_message_model = new SmsMessage();
        $site_message_model = new SiteMessage();
        $keyword = input("keyword", "");
        $sms_addon = "SmsAliyun";
        if (IS_AJAX) {
            $keyword = input("keyword", "");
            $code = input("code", "");
            $content = input("content", "");
            $var_parse = input("var_parse", "");
            $status = input("status", 0);
            $data = array(
                "code" => $code,
                "content" => $content,
                "var_parse" => $var_parse,
                "sms_addon" => $sms_addon,
                "keyword" => $keyword,
                "site_id" => $site_id
            );
            $res = $sms_message_model->editSmsMessage($data, ["keyword" => $keyword, "sms_addon" => $sms_addon]);
            //开启或关闭本消息类型邮箱的启用状态
            $type_data = array(
                "sms_is_open" => $status
            );
            $site_message_model->editSiteMessageType($type_data, ["site_keyword" => $keyword, "site_id" => $site_id]);
            return $res;
        } else {
            $type_info = $site_message_model->getSiteMessageTypeViewInfo(["keyword" => $keyword, "site_id" => $site_id]);
            $this->assign("type_info", $type_info["data"]);
            $this->assign("keyword", $keyword);
            $info = $sms_message_model->getSmsMessageInfo(["keyword" => $keyword, "sms_addon" => $sms_addon, "site_id" => $site_id]);
            $this->assign("info", $info["data"]);
            return $this->fetch('index/template', [], $this->replace);
        }
    }
}