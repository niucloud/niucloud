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
namespace addon\system\Wechat\sitehome\controller;

use addon\system\Message\common\model\SiteMessage;
use addon\system\Wechat\common\model\Message as MessageModel;
use addon\system\Wechat\common\model\WechatMessage;

/**
 * 微信公众号模板消息
 */
class Message extends Base
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'NS_WECHAT_MESSAGE_IMG' => __ROOT__ . '/addon/system/WechatMessage/sitehome/view/public/image',
		];
	}
	
	public function config()
	{
		$message_model = new MessageModel();
		$site_id = $this->siteId;
		if (IS_AJAX) {
			$status = input("status", "");
			$data = array(
				'site_id' => $site_id,
				'status' => $status
			);
			$result = $message_model->setWechatMessageConfig($data);
			return $result;
		} else {
			$config = $message_model->getWechatMessageConfig($site_id);
			$this->assign("config", $config["data"]);
			return $this->fetch('message/config', [], $this->replace);
		}
	}
	
	/**
	 * 编辑模板消息
	 * @return array|mixed|string
	 */
	public function edit()
	{
		$message_model = new SiteMessage();
		$wechat_message_model = new MessageModel();
		$site_id = $this->siteId;
		if (IS_AJAX) {
			$keyword = input("keyword", "");
			$bottomtext = input("bottomtext", "");
			$headtext = input("headtext", "");
			$status = input("status", 0);
			
			$data = array(
				'site_id' => $site_id,
				'bottomtext' => $bottomtext,
				'headtext' => $headtext
			);
			$res = $wechat_message_model->editSiteWechatMessage($data, [ 'site_id' => $site_id, 'keyword' => $keyword ]);
			
			$message_model->editSiteMessageType([ "wechat_is_open" => $status ], [ "site_keyword" => $keyword, "site_id" => $site_id ]);
			return $res;
		} else {
			$keyword = input("keyword", "");
			if (empty($keyword)) {
				$this->redirect(addon_url('Message://sitehome/Index/index'));
			}
			$this->assign("keyword", $keyword);
			$message_type_info = $message_model->getSiteMessageTypeViewInfo([ 'site_id' => $site_id, 'keyword' => $keyword ]);
			$this->assign("message_type_info", $message_type_info['data']);
			$wechat_message_info = $wechat_message_model->getWechatMessageInfo([ 'keyword' => $keyword ]);
			$this->assign("wechat_message_info", $wechat_message_info["data"]);
			$site_wechat_message_info = $wechat_message_model->getSiteWechatMessageInfo([ 'site_id' => $site_id, 'keyword' => $keyword ]);
			$this->assign("site_wechat_message_info", $site_wechat_message_info["data"]);
			return $this->fetch('message/edit', [], $this->replace);
		}
	}
	
	/**
	 * 重置模板消息
	 */
	public function resetMessage()
	{
		$site_id = $this->siteId;
		$wechat_message_model = new WechatMessage();

		$res = $wechat_message_model->resetMessage([ "site_id" => $site_id ]);
		return $res;
	}

    /**
     * 获取微信消息模板
     * @return array|\multitype
     */
	public function getMessageTemolateId(){
	    if(IS_AJAX){
            $keyword = input("keyword", "");
            $wechat_message_model = new WechatMessage();
            $res = $wechat_message_model->getMessageTemolateId(["keyword" => $keyword, "site_id" => $this->siteId]);
            return $res;
        }

    }
}