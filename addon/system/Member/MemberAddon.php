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
namespace addon\system\Member;

use addon\system\Member\common\model\Member;
use app\common\controller\BaseAddon;
use app\common\model\Login;

/**
 * 商城会员插件
 */
class MemberAddon extends BaseAddon
{
	
	public $info = array(
		'name' => 'Member',
		'title' => '会员管理',
		'description' => '会员组件',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '会员管理插件',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp'
	);
	
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		$member = new Member();
		$member->addDefaultMemberLevel($site_id);
		$data = array(
			"value" => json_encode([ 'type' => 1 ]),
			"site_id" => $site_id
		);
		$member->setMemberGroupConfig($data);
		
		$login_model = new Login();
		$value = '{"is_allow_register":"1","register_type_common":"1","random_name_prefix":"","name_keyword":"","pwd_length":"6","is_automatic":"1"}';
		$data = [
		    'site_id' => $site_id,
		    'value' => $value,
		    'title' => '注册与访问设置',
		    'remark' => '注册与访问设置',
		];
		$res = $login_model->setRegisterConfig($data);
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$member = new Member();
		$member->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 *
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
	/**
	 * 计算兑换金额
	 *
	 * @param unknown $param
	 */
	public function calculateAccountMoney($param = [])
	{
		$member_model = new Member();
		$res = $member_model->getMemberAccountMoney($param);
		return $res['data'];
	}
	
	/**
	 * 获取会员账户设置
	 *
	 * @param array $param
	 */
	public function getMemberAccountConfig($param = [])
	{
		$member = new Member();
		$account_config_list = $member->getMemberAccountConfig($param['site_id']);
		
		// 查询会员账户信息
		$member_account_info = $member->getMemberInfo([
			'member_id' => $param['member_id']
		], "credit1,credit2,credit3,credit4,credit5,credit6,credit7");
		
		$res = array();
		if (!empty($account_config_list['data'])) {
			
			foreach ($member_account_info['data'] as $k => $v) {
				
				foreach ($account_config_list['data']['value'] as $k_config => $v_config) {
					if ($v_config['is_use'] == 1 && $v_config['is_exchange']) {
						if ($account_config_list['data']['value'][ $k_config ]['key'] == $k) {
							$account_config_list['data']['value'][ $k_config ]['num'] = $v;
							array_push($res, $account_config_list['data']['value'][ $k_config ]);
						}
					}
				}
			}
			return success($res);
		}
	}
	
	/**
	 * 获取站点相关账户配置
	 * @param array $param
	 */
	public function getSiteAccountConfig($param = [])
	{
	
	}
	
	/**
	 * 发送消息
	 * @param $param
	 */
	public function messageTemplate($param = [])
	{

	    if ($param["addon"] == "Member") {
	        if ($param["keyword"] == "REGISTER") {
	            $param["var_parse"] = [
	                "code" => $param['code'],
	            ];
	            if (!empty($param['email'])) {
	                $param["account"] = $param['email'];
	                $res = hook("emailMessage", $param);
	                $res = $res[0];
	            } elseif (!empty($param['mobile'])) {
	                $param["account"] = $param['mobile'];
	                $res = hook("smsMessage", $param);
	                $res = $res[0];
	            }
	        }
	        if(isset($res)){
                return $res;
            }
	    }

	    
	}
	
}