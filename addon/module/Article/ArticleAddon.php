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
namespace addon\module\Article;

use addon\module\Article\common\model\Comment;
use app\common\controller\BaseAddon;
use addon\module\Article\common\model\Article;

/**
 * cms管理
 */
class ArticleAddon extends BaseAddon
{
	public $replace;
	
	public $info = array(
		'name' => 'Article',
		'title' => '文章管理',
		'description' => '文章管理',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_MODULE',
		'category' => 'OTHER',
		'content' => '',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => 'NcApplet',
		'support_app_type' => 'wap,weapp'
	);
	public $config = [];
	
	public $upload_path = __UPLOAD__;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
		$this->replace = [
		];
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		
		$this->executeSql('install');
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		$this->executeSql('uninstall');
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$article = new Article();
		$article->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
	/**
	 * 发送消息
	 * @param $param
	 */
	public function messageTemplate($param = [])
	{
		if ($param["addon"] == "Article") {
			if ($param["keyword"] == 'COMMENT_SUCCESS') {
				$comment_model = new Comment();
				$join = [
					[ 'nc_article nca', 'nca.article_id = ncc.article_id' ],
					[ 'nc_member nm', 'nm.member_id = ncc.member_id' ]
				];
				$field = 'nca.title, ncc.create_time, ncc.nick_name, nm.mobile, nm.email';
				
				$comment_info = $comment_model->getCommentInfo([ 'id' => $param['id'] ], $field, 'ncc', $join);
				if (!empty($comment_info['data'])) {
					$param["var_parse"] = [
						"username" => $comment_info['data']['nick_name'],
						"artiletitle" => $comment_info['data']['title'],
						"time" => date('Y-m-d H:i:s', $comment_info['data']['create_time'])
					];
					$param["account"] = $comment_info['data']['mobile'];
					hook("smsMessage", $param);
					$param["account"] = $comment_info['data']['email'];
					hook("emailMessage", $param);
					
					$param["account"] = $comment_info['data']['email'];
					
					//發送模板消息
					$member_info = model("nc_member")->getInfo([ "site_id" => $param["site_id"], "member_id" => $comment_info["data"]["member_id"] ]);
					$open_id = $member_info["wx_openid"];
					$keyword_json = array(
						"keyword1" => $comment_info['data']['nick_name'],
						"keyword2" => date('Y-m-d H:i:s', $comment_info['data']['create_time'])
					);
					$wechar_param = array(
						"open_id" => $open_id,
						"keyword_json" => $keyword_json,
						"site_id" => $param["site_id"],
						"keyword" => $param["keyword"]
					);
					hook("wechatMessage", $wechar_param);
				}
			}
		}
	}
	
}