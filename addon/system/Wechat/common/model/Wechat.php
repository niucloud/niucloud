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

namespace addon\system\Wechat\common\model;

use app\common\model\Config;
use think\Cache;
use util\weixin\Weixin;
use app\common\model\Site;

/**
 * 微信公众号配置
 */
class Wechat
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/*******************************************************************************公众号基础设置开始*****************************************************/
	
	/**
	 * 删除微信公众号配置
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteWechatConfig($site_id)
	{
		$res = $this->site_model->deleteSiteConfig([ 'site_id' => $site_id, 'name' => 'NC_WECHAT_CONFIG' ]);
		return $res;
	}
	
	/**
	 * 设置微信公众号配置
	 * @param number $site_id
	 * @param string $appid
	 * @param string $appsecret
	 * @param string $token
	 * @param number $status
	 * @return multitype:string mixed
	 */
	public function setWechatConfig($data)
	{
        $data["name"] = 'NC_WECHAT_CONFIG';
//		$value = array(
//			"appid" => trim($appid),
//			"appsecret" => trim($appsecret),
//			"token" => trim($token),
//		);
//		$data = array(
//			"site_id" => $site_id,
//			'name' => 'NC_WECHAT_CONFIG',
//			"value" => json_encode($value),
//			"status" => $status
//		);
		$res = $this->site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 获取微信公众号配置信息
	 * @param number $site_id
	 * @return multitype:string mixed
	 */
	public function getWechatConfigInfo($site_id)
	{
		$info = $this->site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'NC_WECHAT_CONFIG' ]);
		$value = [];
		if (!empty($info["data"]["value"])) {
			$value = json_decode($info["data"]["value"], true);
		}
		$info["data"]["value"] = $value;
		return $info;
	}
	
	/**
	 * 微信公众号信息添加修改
	 * @param unknown $site_id
	 * @param unknown $name
	 * @param unknown $value
	 */
	public function setWechatInfoConfig($data)
	{
		$data["name"] = 'WECHAT_INGO_CONFIG';
		$res = $this->site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 微信公众号数据查询数据
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getWechatInfoConfig($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ 'name' => 'WECHAT_INGO_CONFIG', 'site_id' => $site_id ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
	
	/*******************************************************************************公众号基础设置结束*****************************************************/
	/*******************************************************************************微信接口连接开始*****************************************************/
	/**
	 * 获取微信token
	 * @param string $appid
	 * @param string $appsecret
	 */
	public function getAccessToken($appid, $appsecret)
	{
		
		$wechat_api = new Weixin('public');
		$wechat_api->initWechatPublicAccount($appid, $appsecret);
		$get_token = $wechat_api->getAccessToken();
		
		$access_token = $wechat_api->accessToken;
		
		if (empty($access_token)) {
			return error('', json_encode($get_token));
		} else {
			return success($access_token);
		}
		
	}
	
	/**
	 * 微信api
	 * @param number $site_id
	 */
	public function weixinApi($site_id)
	{
	    $wechat_api = new Weixin('public');
		$wechat_config_info = $this->getWechatConfigInfo($site_id);
		$config_info = $wechat_config_info['data']['value'];
		if (!empty($config_info['appid']) && !empty($config_info['appsecret'])) {
            $wechat_api->initWechatPublicAccount($config_info['appid'], $config_info['appsecret']);
		}
        //微信授权
        $config_model = new Config();
        $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
        if(!empty($wechat_info_result["data"]["value"])){
            $wechat_info = json_decode($wechat_info_result['data']['value'], true);
            $auth_info_result = $this->getWechatAuthInfo($site_id);
            if(!empty($auth_info_result["data"]) && !empty($auth_info_result["data"]["value"])){
                $auth_info = json_decode($auth_info_result["data"]["value"], true);
                $wechat_api = new Weixin('platform');
                $wechat_api->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
                $wechat_api->setWechatPublicAccount($auth_info["authorizer_appid"], $auth_info["authorizer_refresh_token"]);
                return $wechat_api;
            }
        }
        return $wechat_api;
	}
	/*******************************************************************************微信接口连接结束*****************************************************/
	/*******************************************************************************微信自定义菜单开始*****************************************************/
	
	/**
	 * 设置微信自定义菜单
	 * @param number $site_id
	 * @param string $menu_value 菜单数据
	 * @param number $status
	 * @return multitype:string mixed
	 */
	public function setWechatMenu($site_id, $menu_value, $status = 1)
	{
		//修改
		$data = array(
			"site_id" => $site_id,
			"name" => 'NC_WECHAT_MENU',
			'status' => $status,
			'value' => $menu_value,
			'update_time' => time()
		);
		$result = $this->site_model->setSiteConfig($data);
		return $result;
		
	}
	
	/**
	 * 获取微信自定义菜单信息
	 * @param number $site_id
	 */
	public function getWechatMenuInfo($site_id)
	{
		$info = $this->site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'NC_WECHAT_MENU' ]);
		return $info;
	}
	
	/**
	 * 设置微信自定义菜单
	 * @param number $site_id
	 */
	public function sendWechatMenu($site_id, $menu_json)
	{
		$wechat_api = $this->weixinApi($site_id);
		if (is_array($wechat_api)) {
			$res = $wechat_api;
		} else {
			$res = $wechat_api->createMenu($menu_json);
		}
		return $res;
	}
	
	/*******************************************************************************微信自定义菜单结束*****************************************************/
	/*******************************************************************************微信素材开始*****************************************************/
	
	/**
	 * 添加微信素材
	 * @param array $data
	 */
	public function addMaterial($data)
	{
		$res = model('nc_wechat_media')->add($data);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/**
	 * 删除微信素材
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteMaterial($condition)
	{
		$res = model('nc_wechat_media')->delete($condition);
		if ($res === false) {
			return error($res, 'DELETE_FAIL');
		}
		return success($res, 'DELETE_SUCCESS');
	}
	
	/**
	 * 修改微信素材
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editMaterial($data, $condition)
	{
		$res = model('nc_wechat_media')->update($data, $condition);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/**
	 * 获取微信素材信息
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getMaterialInfo($condition, $field = '*')
	{
		$res = model('nc_wechat_media')->getInfo($condition, $field);
		return success($res);
	}
	
	/**
	 * 获取微信素材信息通过素材ID
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getMaterialInfoById($id)
	{
		$res = model('nc_wechat_media')->getInfo([ 'id' => $id ], '*');
		return success($res);
	}
	
	/**
	 * 获取微信素材列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMaterialList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$res = model('nc_wechat_media')->getList($condition, $field, $order, '', '', '', $limit);
		return success($res);
	}
	
	/**
	 * 获取微信素材分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getMaterialPageList($condition = [], $page = 1, $list_rows = PAGE_LIST_ROWS, $order = 'update_time desc', $field = '*')
	{
		$res = model('nc_wechat_media')->pageList($condition, $field, $order, $page, $list_rows);
		return success($res);
	}
	
	/*******************************************************************************微信素材结束*****************************************************/
	/*******************************************************************************微信模板消息开始*****************************************************/
	/**
	 * 发送微信模板消息
	 * @param number $site_id
	 * @param array $param
	 * @return mixed
	 */
	public function sendTemplateMsg($site_id, $param = [])
	{
		$wechat_api = $this->weixinApi($site_id);
		if (is_array($wechat_api)) {
			$res = $wechat_api;
		} else {
			$res = $wechat_api->templateMessageSend($param['openid'], $param['template_id'], $param['url'], $param['first'], $param['keyword1'], $param['keyword2'], $param['keyword3'], $param['keyword4'], $param['remark']);
		}
		
		return $res;
	}
	/*******************************************************************************微信模板消息结束*****************************************************/
	/*******************************************************************************微信回复开始*****************************************************/
	
	/**
	 * 获取回复列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getReplayPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_wechat_replay_rule')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 添加微信关键词回复
	 * @param array $data
	 */
	public function addRule($data)
	{
		$res = model('nc_wechat_replay_rule')->add($data);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/**
	 * 修改微信关键词回复
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editRule($data, $condition)
	{
		$res = model('nc_wechat_replay_rule')->update($data, $condition);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/**
	 * 删除微信关键词回复
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteRule($condition)
	{
		$res = model('nc_wechat_replay_rule')->delete($condition);
		if ($res === false) {
			return error($res, 'DELETE_FAIL');
		}
		return success($res, 'DELETE_SUCCESS');
	}
	
	/**
	 * 获取微信关键词回复信息
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getRuleInfo($condition, $field = '*')
	{
		$info = model('nc_wechat_replay_rule')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 获取站点关键字回复
	 * @param unknown $site_id
	 * @param unknown $keyWords
	 */
	public function getSiteWechatKeywordsReplay($from_user, $to_user, $site_id, $keywords)
	{
		$list = model('nc_wechat_replay_rule')->getList([ 'site_id' => $site_id, 'rule_type' => 'KEYWORDS' ]);
		$rule_list = array();
		$text = '';
		foreach ($list as $k => $v) {
			$kewords_array = json_decode($v['keywords_json'], true);
			$replay_array = json_decode($v['replay_json'], true);
			
			if (!empty($kewords_array) && !empty($replay_array)) {
				foreach ($kewords_array as $k_key => $v_key) {
					$text = $v_key['keywords_name'] . ',' . $text;
					if (($v_key['keywords_type'] == 0 && $v_key['keywords_name'] == $keywords) || ($v_key['keywords_type'] == 1 && (strpos($keywords, $v_key['keywords_name']) !== false))) {
						$num = rand(0, count($replay_array) - 1);
						$rule_list[] = $replay_array[ $num ];
					}
				}
			}
		}
		
		if (!empty($rule_list)) {
			$rule = $rule_list[0];
			$weixin_api = $this->weixinApi($site_id);
			if ($rule['type'] == 'text') {
				$replay = $weixin_api->replayTextXml($from_user, $to_user, $rule['reply_content']);
			} else if ($rule['type'] == '') {
			
			}
		} else {
		    $res = hook('wechatReplay', ['from_user' => $from_user, 'to_user' => $to_user, 'site_id' => $site_id, 'keywords' => $keywords]);
		    if(!empty($res))
		    {
		        //随机返回回复列表
		        $num = rand(0, count($res) - 1);
		        return $res[ $num ];
		    }
			$weixin_api = $this->weixinApi($site_id);
			$replay = $weixin_api->replayTextXml($from_user, $to_user, "欢迎登陆牛云框架,当前站点id是" . $site_id . '关键字：' . $text, 0);
		}
		
		return success($replay);
	}
	
	/**
	 * 获取微信关注回复
	 * @param unknown $from_user
	 * @param unknown $to_user
	 * @param unknown $site_id
	 * @param unknown $keywords
	 */
	function getWechatFollowReplay($from_user, $to_user, $site_id)
	{
		$follow_info = model('nc_wechat_replay_rule')->getInfo([ 'site_id' => $site_id, 'rule_type' => 'AFTER' ]);
		$weixin_api = $this->weixinApi($site_id);
		$replay_content = '';
		if (!empty($follow_info['replay_json'])) {
			$replay_info = json_decode($follow_info['replay_json'], true);
			
			switch ($replay_info[0]['type']) {
				case 'text' :
// 				    $replay_content = $weixin_api->replayTextXml($from_user, $to_user, $replay_info[0]['reply_content']);
					$replay_content = $replay_info[0]['reply_content'];
					break;
				case 'articles' :
					$replay_content = '';
					break;
			}
		}
		return success($replay_content);
	}
	
	/*******************************************************************************微信回复结束*****************************************************/
	/*******************************************************************************微信粉丝开始*****************************************************/
	
	/**
	 * 获取粉丝列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getWechatFansList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'subscribe_time desc', $field = '*')
	{
		$list = model('nc_wechat_fans')->pageList($condition, $field, $order, $page, $page_size);
		
		if (!empty($list)) {
			foreach ($list['list'] as $key => $info) {
				$list['list'][ $key ]['nickname'] = base64_decode($info['nickname']);
				$province = base64_decode($info['province']);
				$city = base64_decode($info['city']);
				$district = base64_decode($info['district']);
				$list['list'][ $key ]['province'] = $province;
				$list['list'][ $key ]['city'] = $city;
				$list['list'][ $key ]['district'] = $district;
				$list['list'][ $key ]['address'] = $info['country'] . $province . $city . $district;
				$list['list'][ $key ]['address_info'] = $province . $city . $district;
				if (!empty($info['tagid_list'])) {
					$tagid_list = explode(',', $info['tagid_list']);
					$list['list'][ $key ]['tagid_list_arr'] = model('nc_wechat_fans_tag')->getList([ 'site_id' => $info['site_id'], 'tag_id' => [ 'in', $tagid_list ] ], 'tag_id,tag_name');
				}
			}
		}
		
		return success($list);
	}
	
	/**
	 * 获取微信粉丝数量
	 * @param $condition
	 */
	public function getWechatFansCount($condition)
	{
		if (empty($condition['site_id'])) {
			return 0;
		}
		$res = model('nc_wechat_fans')->getCount($condition);
		
		return $res;
		
	}
	
	/**
	 * 保存微信粉丝
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function saveWechatFans($data)
	{
		
		$wechat_fans_model = model('nc_wechat_fans');
		$fans_info = $wechat_fans_model->getInfo([ 'openid' => $data['openid'] ]);
		
		if (!empty($fans_info['fans_id'])) {
			if ($fans_info['is_subscribe'] == 0) {
				$data['subscribe_time'] = time();
			}
			$res = model('nc_wechat_fans')->update($data, [ 'openid' => $data['openid'] ]);
		} else {
			$data['subscribe_time'] = time();
			$res = model('nc_wechat_fans')->add($data);
		}
		if (!$res) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCESS');
	}
	
	/**
	 * 取消关注
	 * @param unknown $site_id
	 * @param unknown $openid
	 */
	public function unfollowWechat($site_id, $openid)
	{
		$data = array(
			'is_subscribe' => 0,
			'update_date' => time(),
			'unsubscribe_time' => time()
		);
		$wechat_fans_model = model('nc_wechat_fans');
		$res = $wechat_fans_model->update($data, [ 'site_id' => $site_id, 'openid' => $openid ]);
		if (!$res) {
			return error('', $res);
		}
		return success($res);
	}
	
	/**
	 * 获取微信粉丝列表
	 * @return multitype:string multitype: Ambigous <> |multitype:number string unknown Ambigous <> |multitype:string number
	 */
	public function getWechatOpenidList($site_id)
	{
		$wechat_api = $this->weixinApi($site_id);
		if (is_array($wechat_api)) {
			$res = $wechat_api;
		} else {
			$res = $wechat_api->getWechatFansAllList("");
		}
		
		if ($res['code'] != 0) {
			return $res;
		}
		
		if (empty($res['data']['data'])) {
			return error($res, 'WECHAT_UPDATE_FANS_FAIL');
		}
		
		$openid_list = $res['data']['data']['openid'];
		while ($res['data']['next_openid']) {
			$res = $wechat_api->getWechatFansAllList($res['data']['next_openid']);
			if (!empty($res['data']['data'])) {
				$openid_list = array_merge($openid_list, $res['data']['data']['openid']);
			}
			
		}
		$data = array(
			'total' => $res['data']['total'],
			'openid_list' => $openid_list,
		);
		
		return success($data);
	}
	
	public function updateWechatFansList($site_id, $openids)
	{
		$wechat_api = $this->weixinApi($site_id);
		if (is_array($wechat_api)) {
			$res = $wechat_api;
		} else {
			$res = $wechat_api->getWechatFansQuery($openids);
		}
		//获取微信粉丝列表
		if ($res['code'] != 0) {
			return $res;
		}
		
		foreach ($res['data']['user_info_list'] as $info) {
			$info["unionid"] = empty($info["unionid"]) ? '' : $info["unionid"];
			$province = base64_encode($info["province"]);
			$city = base64_encode($info["city"]);
			$nickname = base64_encode($info['nickname']);
			$nickname_decode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $info['nickname']);
			$data = array(
				'site_id' => $site_id,
				'nickname' => $nickname,
				'nickname_decode' => $nickname_decode,
				'headimgurl' => $info['headimgurl'],
				'sex' => $info["sex"],
				'language' => $info["language"],
				'country' => $info["country"],
				'province' => $province,
				'city' => $city,
				'district' => '',
				'openid' => $info["openid"],
				'unionid' => $info["unionid"],
				'groupid' => $info["groupid"],
				'is_subscribe' => $info["subscribe"],
				'memo' => $info["remark"],
				'update_date' => time(),
				'tagid_list' => empty($info["tagid_list"]) ? '' : implode(',', $info["tagid_list"])
			);
			$this->saveWechatFans($data);
		}
		
		return success();
	}
	
	/**
	 * 获取微信粉丝标签列表
	 */
	public function getWechatFansTagPageList($where = [], $page = 1, $list_rows = PAGE_LIST_ROWS, $order = '', $field = true)
	{
		$list = model('nc_wechat_fans_tag')->pageList($where, $field, $order, $page, $list_rows);
		return success($list);
	}
	
	/**
	 * 添加粉丝标签
	 * @param unknown $data
	 */
	public function addFansTag($data)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);

        $param = [ 'tag' => [ 'name' => $data['tag_name'] ]];
		$result = $wechat_api->createWechatFansTag($param);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data['tag_id'] = $result['tag']['id'];
			$data['tags'] = json_encode($result, JSON_UNESCAPED_UNICODE);
			$res = model('nc_wechat_fans_tag')->add($data);
			return success($res);
		}
	}
	
	/**
	 * 编辑粉丝标签
	 * @param unknown $data
	 */
	public function editFansTag($data, $where)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);
		
		$update_data = [
			'tag' => [ 'id' => $data['tag_id'], 'name' => $data['tag_name'] ]
		];
		$result = $wechat_api->updateWechatFansTag($update_data);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data['tags'] = json_encode($update_data, JSON_UNESCAPED_UNICODE);
			$res = model('nc_wechat_fans_tag')->update($data, $where);
			return success($res);
		}
	}
	
	/**
	 * 删除标签
	 * @param unknown $data
	 */
	public function deleteFansTag($data)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);
		
		$result = $wechat_api->deleteWechatFansTag([
			'tag' => [ 'id' => $data['tag_id'] ]
		]);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$res = model('nc_wechat_fans_tag')->delete([ 'id' => $data['id'], 'site_id' => $data['site_id'] ]);
			return success($res);
		}
	}
	
	/**
	 * 为微信粉丝批量打标签
	 * @param unknown $data
	 */
	public function fansBatchTagging($data)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);
		
		if (!empty($data['tag_id_list'])) {
			foreach ($data['tag_id_list'] as $vo) {
				$result = $wechat_api->batchtagging([
					'openid_list' => $data['openid_list'],
					'tagid' => $vo
				]);
				if (isset($result["errcode"]) && $result["errcode"] < 0) {
					return error($result, $result['errmsg']);
				}
			}
		}
		$res = model('nc_wechat_fans')->update([ 'tagid_list' => implode(',', $data['tag_id_list']) ], [ 'openid' => [ 'in', $data['openid_list'] ] ]);
		return success($res);
	}
	
	/**
	 * 为微信粉丝批量取消标签
	 * @param unknown $data
	 */
	public function fansBatchUnTagging($data)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);
		
		if (!empty($data['tag_id_list'])) {
			foreach ($data['tag_id_list'] as $vo) {
				$result = $wechat_api->batchuntagging([
					'openid_list' => $data['openid_list'],
					'tagid' => $vo
				]);
			}
		}
	}
	
	/**
	 * 同步标签
	 * @param unknown $data
	 */
	public function syncFansTag($data)
	{
        $wechat_api = $this->weixinApi($data["site_id"]);
		$result = $wechat_api->getTagList();
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			if (!empty($result['tags'])) {
				model('nc_wechat_fans_tag')->startTrans();
				try {
					foreach ($result['tags'] as $item) {
						$data = [
							'tag_id' => $item['id'],
							'tag_name' => $item['name'],
							'site_id' => $data['site_id']
						];
						$data['tags'] = json_encode([ 'tag' => $data ], JSON_UNESCAPED_UNICODE);
						$condition = [
							'site_id' => $data['site_id'],
							'tag_id' => $item['id']
						];
						$count = model('nc_wechat_fans_tag')->getCount($condition);
						if ($count) {
							model('nc_wechat_fans_tag')->update($data, $condition);
						} else {
							model('nc_wechat_fans_tag')->add($data);
						}
					}
					model('nc_wechat_fans_tag')->commit();
					return success();
				} catch (\Exception $e) {
					model('nc_wechat_fans_tag')->rollback();
					return error([], $e->getMessage());
				}
			}
		}
	}
	
	/*******************************************************************************微信粉丝结束*****************************************************/
	/*******************************************************************************微信开放平台授权开始*************************************************/
	
	/**
	 * 通过微信公众号appid获取站点id
	 * @param string $appid
	 */
	public function getSiteAuthByAppid($appid)
	{
        $cofig_model = model('nc_site_config');


        $condition = array(
            'name' => 'NC_WECHAT_AUTH',
            'value' => ['like','%"authorizer_appid":"'.$appid.'"%']
        );
        $info = $cofig_model->getInfo($condition, '*');
        if(empty($info)){
            return error();
        }else{
            return success($info);
        }
		$authinfo = model('nc_wechat_auth')->getInfo([ 'authorizer_appid' => $appid ], '*');
		if (empty($authinfo) || $authinfo == null) {
			return error();
		} else {
			return success($authinfo);
		}
	}
	
	/**
	 * 设置公众号授权配置
	 * @param number $site_id
	 * @param string $authorizer_appid
	 * @param string $authorizer_refresh_token
	 * @param string $authorizer_access_token
	 * @param string $func_info
	 * @param string $nick_name
	 * @param string $head_img
	 * @param string $user_name
	 * @param string $alias
	 * @param string $qrcode_url
	 * @param number $status
	 * @return multitype:string mixed
	 */
	public function setWechatAuth($site_id, $authorizer_appid, $authorizer_refresh_token, $authorizer_access_token, $func_info, $nick_name, $head_img, $user_name, $alias, $qrcode_url, $status = 1)
	{
		$cofig_model = model('nc_site_config');
		$condition = array(
			'site_id' => $site_id,
			'name' => 'NC_WECHAT_AUTH'
		);
		$authinfo = $cofig_model->getInfo($condition, '*');
		
		$info = array(
			'authorizer_appid' => $authorizer_appid,
			'authorizer_refresh_token' => $authorizer_refresh_token,
			'authorizer_access_token' => $authorizer_access_token,
			'func_info' => $func_info,
			'nick_name' => $nick_name,
			'head_img' => $head_img,
			'user_name' => $user_name,
			'alias' => $alias,
			'qrcode_url' => $qrcode_url,
		);
		
		if (empty($authinfo)) {
			$data = array(
				'site_id' => $site_id,
				'name' => 'NC_WECHAT_AUTH',
				'title' => '微信公众号授权',
				'remark' => '微信公众号授权',
				'type' => 1,
				'create_time' => time(),
				'status' => 1,
				'value' => json_encode($info),
			);
			$res = $cofig_model->add($data);
		} else {
			$data = array(
				'value' => json_encode($info),
				'status' => $status,
				'update_time' => time()
			);
			$res = $cofig_model->update($data, $condition);
		}
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}

    /**
     * 微信授权信息
     * @param $site_id
     * @return \multitype
     */
	public function getWechatAuthInfo($site_id)
	{
		$info = model('nc_site_config')->getInfo([ 'site_id' => $site_id, 'name' => 'NC_WECHAT_AUTH' ], '*');
		return success($info);
	}
	
	/**
	 * 根据解绑类型修改状态
	 * @param number $site_id
	 * @param string $unbind_type
	 * @return multitype:string mixed
	 */
	public function unbindStatus($site_id, $unbind_type)
	{
		
		if ($unbind_type == 'weixin') {
			$condition = [ 'site_id' => $site_id, 'name' => 'NC_WECHAT_CONFIG' ];
		} else {
			$condition = [ 'site_id' => $site_id, 'name' => 'NC_WECHAT_AUTH' ];
		}
		$res = model('nc_site_config')->update([ 'status' => 0 ], $condition);
		
		return $res === false ? error($res, 'SAVE_FAIL') : success($res, 'SAVE_SUCCESS');
	}
	/*******************************************************************************微信开放平台授权结束*************************************************/
	/**
	 * ************************************************************* 微信客服开始  ************************************************************************
	 */
	/**
	 * 获取微信客服人员列表
	 * @return
	 */
	public function getWechatCustomservicesList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'subscribe_time desc', $field = '*')
	{
		$list = model('nc_wechat_customservice')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 添加微信客服人员
	 * @param $data
	 */
	public function addWechatCustomservices($data)
	{
		$res = model('nc_wechat_customservice')->add($data);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/*
	 * 添加微信客服账号
	 */
	public function saveWechatCustomservices($nick_name, $site_id)
	{
        $wechat_api = $this->weixinApi($site_id);
		$kf_account = "cs8@xiaoxiaojun_654321";
		$result = $wechat_api->addWechatCustomservices($kf_account, $nick_name);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data = array(
				"nickname" => $nick_name,
				"site_id" => $site_id,
				"account" => $kf_account
			);
			$res = $this->addWechatCustomservices($data);
			if ($res === false) {
				return error($res, 'SAVE_FAIL');
			}
			return success($res, 'SAVE_SUCCESS');
		}
	}
	
	/**
	 * 修改微信客服人员
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function updateWechatCustomservices($data, $condition)
	{
		$res = model('nc_wechat_customservice')->update($data, $condition);
		if ($res === false) {
			return error($res, 'SAVE_FAIL');
		}
		return success($res, 'SAVE_SUCCESS');
	}
	
	/**
	 * 更新微信客服人员列表
	 * @param $site_id
	 * @param $openids
	 * @return array|mixed
	 */
	public function updateWechatCustomservicesList($site_id)
	{
        $wechat_api = $this->weixinApi($site_id);
		$list = $wechat_api->getWechatCustomservicesAllList();
		//        $this->deleteWechatCustomservices(["site_id" => $site_id]);//删除原有的客服人员列表
		//获取微信客服列表
		if (isset($list["errcode"]) && $list["errcode"] < 0) {
			return error($list, $list['errmsg']);
		} else {
			foreach ($list['kf_list'] as $info) {
				$data = array(
					"site_id" => $site_id,
					"account" => $info["kf_account"],
					"headimgurl" => $info["kf_headimgurl"],
					"wx_id" => $info["kf_id"],
					"nickname" => $info["kf_nick"]
				);
				if (empty($info["invite_status"])) {
					if (empty($info["kf_wx"])) {
						$data["invite_status"] = "binding";
					} else {
						$data["wx_account"] = $info["kf_wx"];
					}
				} else {
					$data["wx_account"] = $info["invite_wx"];
					$data["invite_status"] = $info["invite_status"];
				}
				$this->addWechatCustomservices($data);//拉去客服添加到本地
			}
		}
		return success();
	}
	
	/**
	 * 删除微信公众号配置
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteWechatCustomservices($condition)
	{
		$res = model('nc_wechat_customservice')->delete($condition);
		if ($res === false) {
			return error($res, 'DELETE_FAIL');
		}
		return success($res, 'DELETE_SUCCESS');
	}
	
	/**
	 * 删除客服
	 * @param $kf_account
	 * @param $id
	 * @param $site_id
	 * @return multitype|array
	 */
	public function deleteCustomservices($kf_account, $id, $site_id)
	{
        $wechat_api = $this->weixinApi($site_id);
		$result = $wechat_api->deleteWechatCustomservices($kf_account);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$condition = array(
				"site_id" => $site_id,
				"account" => $kf_account,
				"id" => $id
			);
			$res = $this->deleteWechatCustomservices($condition);
			return $res;
		}
	}
	
	/**
	 * 邀请绑定客服账号
	 * @param $kf_account
	 * @param $wx_account
	 * @param $id
	 * @param $site_id
	 */
	public function bindingWechatCustomservices($kf_account, $wx_account, $id, $site_id)
	{
        $wechat_api = $this->weixinApi($site_id);
		$result = $wechat_api->bingingWechatCustomservices($kf_account, $wx_account);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data = array(
				"wx_account" => $wx_account
			);
			$condition = array(
				"site_id" => $site_id,
				"account" => $kf_account,
				"services_id" => $id
			);
			$res = $this->updateWechatCustomservices($data, $condition);
			return $res;
		}
	}
	
	/**
	 * 修改微信客服昵称与头像
	 */
	public function updatCustomservicesNicknameOrHeadimg($kf_account, $media, $nickname, $id, $site_id)
	{
        $wechat_api = $this->weixinApi($site_id);
		//修改客服昵称
		$result = $wechat_api->uploadWechatCustomservicesNickname($kf_account, $nickname);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data = array(
				"nickname" => $nickname
			);
			$condition = array(
				"site_id" => $site_id,
				"kf_account" => $kf_account,
				"services_id" => $id
			);
			$res = $this->updateWechatCustomservices($data);
		}
		//修改客服头像
		$result = $wechat_api->uploadWechatCustomservicesHeadimg($kf_account, $media);
		if (isset($result["errcode"]) && $result["errcode"] < 0) {
			return error($result, $result['errmsg']);
		} else {
			$data = array(
				"headimgurl" => $nickname
			);
			$condition = array(
				"site_id" => $site_id,
				"kf_account" => $kf_account,
				"services_id" => $id
			);
			$res = $this->updateWechatCustomservices($data, $condition);
		}
		
		return $result;
	}
	
	/**
	 * 获取用户的授权AccessToken信息
	 */
	public function getOAuthAccessToken($param)
	{
        $wechat_api = $this->weixinApi($param["site_id"]);
		$access_token = $wechat_api->getOAuthAccessToken();
		if (empty($access_token)) {
			return error('', $access_token);
		} else {
			return success($access_token);
		}
	}
	/**
	 * ************************************************************* 微信客服结束  ************************************************************************
	 */
	/*****************************************************************  微信公众号 统计 start *****************************************************************************************/
	
	
	/**
	 * 获取微信粉丝统计(以天为单位)
	 * @param unknown $param
	 */
	public function getFansStatistics($param)
	{
		$info = Cache::tag("wechat_user_info" . $param["site_id"])->get("wechat_user_info_day_" . $param["site_id"] . "_" . $param["begin_date"]);
		if ($info !== false) {
			return success($info);
		}

        $wechat_api = $this->weixinApi($param["site_id"]);
		
		$data = [ 'begin_date' => $param['begin_date'], 'end_date' => $param['end_date'] ];
		$user_summary = $wechat_api->getUserSummary($data);
		$user_cumulate = $wechat_api->getUserCumulate($data);
		
		if (empty($user_summary) || $user_summary["errcode"] != 0) {
			return error([], $user_summary["errmsg"]);
		}
		if (empty($user_cumulate) || $user_cumulate["errcode"] != 0) {
			return error([], $user_cumulate["errmsg"]);
		}
		
		
		$list = [];
		foreach ($user_cumulate["list"] as $cumulate_k => $cumulate_v) {
			$temp_item = $cumulate_v;
			$temp_item['cumulate_user'] = empty($cumulate_v['cumulate_user']) ? 0 : $cumulate_v['cumulate_user'];
			$new_user = 0;
			$cancel_user = 0;
			$net_growth_user = 0;
			foreach ($user_summary['list'] as $key => $item) {
				if ($item["ref_date"] == $cumulate_v["ref_date"]) {
					$new_user += $item['new_user'];
					$cancel_user += $item['cancel_user'];
					$net_growth_user += $item["new_user"] - $item["cancel_user"];
				}
			}
			$temp_item['new_user'] = $new_user;
			$temp_item['cancel_user'] = $cancel_user;
			$temp_item['net_growth_user'] = $net_growth_user;
			$list[] = $temp_item;
		}
		
		
		Cache::tag("wechat_user_info" . $param["site_id"])->set("wechat_user_info_day_" . $param["site_id"] . "_" . $param["begin_date"], $list);
		return success($list);
	}
	
	/**
	 * 获取微信接口调用数据
	 * @param unknown $param
	 */
	public function getInterfaceSummary($param)
	{
		$info = Cache::tag("wechat_interface_info" . $param["site_id"])->get("wechat_interface_info_day_" . $param["site_id"] . "_" . $param["begin_date"]);
		if ($info !== false) {
			return success($info);
		}

        $wechat_api = $this->weixinApi($param["site_id"]);
		$data = [ 'begin_date' => $param['begin_date'], 'end_date' => $param['end_date'] ];
		$result = $wechat_api->getInterfaceSummary($data);
		if (empty($result) || $result["errcode"] != 0) {
			return error([], $result["errmsg"]);
		}
		
		Cache::tag("wechat_interface_info" . $param["site_id"])->set("wechat_interface_info_day_" . $param["site_id"] . "_" . $param["begin_date"], $result['list']);
		return success($result['list']);
	}
	/*****************************************************************  微信公众号 统计 end *****************************************************************************************/
	/**
	 * 删除站点
	 * @param unknown $site_id
	 */
	public function deleteSite($site_id)
	{
		model('nc_wechat_customservice')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_fans')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_fans_tag')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_information_list')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_information_recording')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_media')->delete([ 'site_id' => $site_id ]);
		model('nc_wechat_replay_rule')->delete([ 'site_id' => $site_id ]);
		return success();
	}
}