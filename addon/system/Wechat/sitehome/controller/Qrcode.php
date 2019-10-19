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

/**
 * 微信推广二维码控制器
 */
class Qrcode extends Base
{
	/**
	 * 推广二维码模板
	 */
	public function index()
	{
		//$weixin = new Weixin();
		//$template_list = $weixin->getWeixinQrcodeTemplate($this->instance_id);
		$template_list = array(
			'0' => [
				"id" => 2,
				"instance_id" => 0,
				"background" => "http://apitest.niuteam.cn/upload/common/1529468326.jpg",
				"nick_font_color" => "#2b2b2b",
				"nick_font_size" => 23,
				"is_logo_show" => 1,
				"header_left" => "141px",
				"header_top" => "95px",
				"name_left" => "139px",
				"name_top" => "159px",
				"logo_left" => "89px",
				"logo_top" => "229px",
				"code_left" => "89px",
				"code_top" => "269px",
				"is_check" => 1,
				"is_remove" => 0,
				"template_url" => "http://apitest.niuteam.cn/upload/qrcode/promote_qrcode_template/qrcode_template_2_0.png"
			]
		);
		$this->assign("template_list", $template_list);
		return $this->fetch('Qrcode/index', [], $this->replace);
	}
}