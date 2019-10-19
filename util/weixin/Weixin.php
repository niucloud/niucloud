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
namespace util\weixin;

use think\Cache;
use think;
use util\weixin\wechatplatform\WxBizMsgCrypt;
use think\Log;

/**
 * 微信接口类
 */
class Weixin
{
	/**
	 * 账户类型  'public','platform'
	 * @var string
	 */
	public $accountType = 'public';
	
	/**
	 * 公众号的AppID
	 * @var string
	 */
	protected $appid = "";
	
	/**
	 * 公众号密钥
	 * @var string
	 */
	protected $appsecret = "";
	
	/**
	 * 微信accessToken
	 * @var string
	 */
	public $accessToken = "";
	
	/**
	 * 微信公众平台刷新token
	 * @var string
	 */
	public $refreshToken;
	
	/**
	 * 微信开放平台appid
	 * @var string
	 */
	public $platformAppid;
	
	/**
	 * 微信开放平台appsecret
	 * @var string
	 */
	public $platformAppsecret;
	
	/**
	 * 微信开放平台加密秘钥
	 * @var string
	 */
	public $platformEncodingaeskey;
	
	/**
	 * 微信开放平台token
	 * @var string
	 */
	public $platformToken;
	
	
	/**
	 * 微信开放平台componentAccessToken
	 * @var string
	 */
	public $componentAccessToken;
	
	/**
	 * 微信开放平台ticket
	 * @var string
	 */
	public $ticket;
	
	/**
	 * 微信开放平台预授权码
	 * @var string
	 */
	public $preAuthCode;
	
	/**
	 * 初始化 传入账户类型
	 */
	public function __construct($accountType)
	{
		$this->accountType = $accountType;
	}
	
	/**
	 * 初始化微信公众平台账户
	 * @param string $appid
	 * @param string $appsecret
	 */
	public function initWechatPublicAccount($appid, $appsecret)
	{
		$this->appid = $appid;
		$this->appsecret = $appsecret;
	}
	
	/**
	 * 初始化开放平台账户
	 * @param string $appid
	 * @param string $appsecret
	 * @param string $encodingaeskey
	 * @param string $token
	 */
	public function initWechatPlatformAccount($appid, $appsecret, $encodingaeskey, $token)
	{
		
		$this->platformAppid = $appid;
		$this->platformAppsecret = $appsecret;
		$this->platformEncodingaeskey = $encodingaeskey;
		$this->platformToken = $token;
//		$this->platformAppid = 'wx65cce08cf8a72f02';
//		$this->platformAppsecret = 'a5c8758cdf1af4446999d82cbe1d84f4';
//		$this->platformEncodingaeskey = 'shinianmoyijian1234shijianmoyijianniukukeji';
//		$this->platformToken = 'niuku123';
		$this->ticket = \file_get_contents(DS.'./ticket.txt');
		if (cache::get('component_access_token') == false) {
			$this->getCommonAccessToken();
		} else {
			$this->componentAccessToken = cache::get('component_access_token');
		}
		if (cache::get('pre_auth_code') == false) {
			$this->getPreAuthCode();
		} else {
			$this->preAuthCode = cache::get('pre_auth_code');
		}
		
	}
	
	/**
	 * 设置微信公众号（通过微信开放平台）
	 * @param unknown $appid
	 * @param string $refresh_token
	 */
	public function setWechatPublicAccount($appid, $refresh_token = '')
	{
		$this->appid = $appid;
		$this->refreshToken = $refresh_token;
	}
	/*************************************************************微信开放平台接口******************************************************************/
	/**
	 * 获取第三方token,需要第三方的appid，密码，ticket     $component_token
	 */
	private function getCommonAccessToken()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
		
		$data = array( 'component_appid' => $this->platformAppid, 'component_appsecret' => $this->platformAppsecret, 'component_verify_ticket' => $this->ticket );
		
		$data = json_encode($data);
		
		$curl = curl_init();  //创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$AjaxReturn = curl_exec($curl);
		$token_arr = json_decode($AjaxReturn);
		
		if (!empty($token_arr->component_access_token)) {
			
			cache::set('component_access_token', $token_arr->component_access_token, 5400);
			$this->componentAccessToken = cache::get('component_access_token');
		}
	}
	
	/**
	 * 获取第三方平台的预授权码    需要第三方token，以及第三方appid
	 */
	private function getPreAuthCode()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=" . $this->componentAccessToken;
		$data = array( 'component_appid' => $this->platformAppid );
		$data = json_encode($data);
		$curl = curl_init();  //创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$AjaxReturn = curl_exec($curl);
		$code_arr = json_decode($AjaxReturn);
		if (!empty($code_arr->pre_auth_code)) {
			cache::set('pre_auth_code', $code_arr->pre_auth_code, 500);
			$this->preAuthCode = cache::get('pre_auth_code');
		}
	}
	
	/**
	 * 获取ticket
	 * @param unknown $msg_sign
	 * @param unknown $timeStamp
	 * @param unknown $nonce
	 * @param unknown $from_xml
	 * @return string
	 */
	public function getTicket($msg_sign, $timeStamp, $nonce, $from_xml)
	{
		// 第三方发送消息给公众平台
		$encodingAesKey = $this->platformEncodingaeskey;
		$token = $this->platformToken;
		$appId = $this->platformAppid;
		$pc = new WxBizMsgCrypt($token, $encodingAesKey, $appId);
		// 第三方收到公众号平台发送的消息
		$msg = '';
		$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
		return $msg;
	}
	
	/**
	 * 通过此方法入口获取ComponentVerifyTicket
	 * 微信授权使用
	 */
	public function getComponentVerifyTicket($signature, $timestamp, $nonce, $from_xml)
	{
		$ticket_xml = $this->getTicket($signature, $timestamp, $nonce, $from_xml);
		Log::write("接受ticket——xml" . $ticket_xml);
		$postObj = \simplexml_load_string($ticket_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		switch ($postObj->InfoType) {
			case "component_verify_ticket":
				\file_put_contents('./ticket.txt', $postObj->ComponentVerifyTicket);
				echo "success";
				break;
			case "unauthorized":
				//当用户取消授权的时候，微信服务器也会向这个页面发送信息
				break;
			case "authorized":
				//当用户取消授权的时候，微信服务器也会向这个页面发送信息
				break;
			default:
				break;
		}
	}
	
	/**
	 * 微信开放平台数据加密
	 * @param unknown $msg_sign
	 * @param unknown $timeStamp
	 * @param unknown $nonce
	 * @param unknown $from_xml
	 * @return string
	 */
	public function encMsg($timeStamp, $nonce, $from_xml)
	{
		// 第三方发送消息给公众平台
		$encodingAesKey = $this->platformEncodingaeskey;
		$token = $this->platformToken;
		$appId = $this->platformAppid;
		
		$pc = new WxBizMsgCrypt($token, $encodingAesKey, $appId);
		// 第三方收到公众号平台发送的消息
		$msg = '';
		$errCode = $pc->encryptMsg($from_xml, $timeStamp, $nonce, $msg);
		
		return $msg;
	}
	
	/**
	 * 授权入口，注意授权之后的auth_code要存入库中 （用预授权码以及开放平台的appid，授权成功后会给回调网址发送授权的auth_code，用于获取授权公众号基本信息）
	 */
	public function authUrl($url)
	{
		$redurl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=' . $this->platformAppid . '&pre_auth_code=' . $this->preAuthCode . '&redirect_uri=' . $url;
		return $redurl;
		
	}
	
	/**
	 * 使用授权码换取公众号的接口调用凭据和授权信息,得到之后要存入数据库中，尤其是authorizer_appid,authorizer_refresh_token  只提供一次
	 */
	public function getQueryAuth($author_code)
	{
		//此页面可以是授权的回调地址通过get方法获取到authorization_code
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=" . $this->componentAccessToken;
		$data = array( 'component_appid' => $this->platformAppid, 'authorization_code' => $author_code );
		$data = json_encode($data);
		$curl = curl_init();  //创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$AjaxReturn = curl_exec($curl);
		$code_arr = json_decode($AjaxReturn);
		return $code_arr;
	}
	
	/**
	 * 使用授权码换取公众号的接口调用凭据和授权信息,得到之后要存入数据库中，尤其是authorizer_appid,authorizer_refresh_token  只提供一次
	 */
	public function getAuthorizerInfo($appid)
	{
		//此页面可以是授权的回调地址通过get方法获取到authorization_code
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=" . $this->componentAccessToken;
		$data = array( 'component_appid' => $this->platformAppid, 'authorizer_appid' => $appid );
		$data = json_encode($data);
		$curl = curl_init();  //创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$AjaxReturn = curl_exec($curl);
		$code_arr = json_decode($AjaxReturn);
		return $code_arr;
	}
	
	
	/**
	 * 通过上述方法获取的公众号access_token可能会过期，因此需要定时获取access_token
	 */
	public function getAccessTokenByWechatPlatform()
	{
		//获取公众号token
		$url = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=" . $this->componentAccessToken;
		$data = array( 'component_appid' => $this->platformAppid, 'authorizer_appid' => $this->appid, 'authorizer_refresh_token' => $this->refreshToken );
		$data = json_encode($data);
		$curl = curl_init();  //创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$AjaxReturn = curl_exec($curl);
		$strjson = json_decode($AjaxReturn);
		if ($strjson == false || empty($strjson)) {
			return '';
		} else {
			$token = '';
			if (isset($strjson->authorizer_access_token)) {
				$token = $strjson->authorizer_access_token;
			}
			
			if (empty($token)) {
			} else {
				
				cache::set('accessToken-' . $this->appid, $token, 3600);
				$this->accessToken = $token;
			}
			return $strjson;
		}
	}
	/***************************************************************微信公众平台获取用户token***********************************************************/
	/**
	 * 公众平台账户获取token
	 */
	public function getAccessToken()
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$a = curl_exec($ch);
		$strjson = json_decode($a);
		
		if ($strjson == false || empty($strjson)) {
			return '';
		} else {
			$token = '';
			if (isset($strjson->access_token)) {
				$token = $strjson->access_token;
			}
			
			if (empty($token)) {
			} else {
				
				cache::set('accessToken-' . $this->appid, $token, 3600);
				$this->accessToken = $token;
			}
			return $strjson;
		}
	}
	
	/****************************************************************微信获取用户授权登录-start********************************************************/
	/**
	 * 获取微信粉丝的access_token
	 * @return mixed
	 */
	public function getOAuthAccessToken()
	{
		//如果是微信浏览器
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
			// 通过code获得openid
			if (empty($_GET['code'])) {
				// 触发微信返回code码
				$baseUrl = request()->url(true);
				$url = $this->getOAuthFansAuthorize($baseUrl, "123");
				Header("Location: $url");
				exit();
			} else {
				// 获取code码，以获取openid
				$code = $_GET['code'];
				$data = $this->getOAuthAccessTokenByCode($code);
				return $data;
			}
		}
	}
	
	/**
	 * 获取OAuth2授权access_token(微信公众平台模式)  获取用户的信息
	 *
	 * @param string $code
	 *            通过get_authorize_url获取到的code
	 */
	public function getOAuthAccessTokenByCode($code = '')
	{
		$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code';
		$data = $this->sendWeixinRequest($token_url);
		$token_data = json_decode($data, true);
		return $token_data;
	}
	
	/**
	 * 通过粉丝的access_token 查询粉丝的信息
	 * @param unknown $appid
	 */
	public function getOAuthUserInfo($token)
	{
		$token_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token['access_token'] . "&openid=" . $token['openid'] . "&lang=zh_CN";
		$data = $this->sendWeixinRequest($token_url);
		return $data;
	}
	
	/**
	 * 获取微信OAuth2授权链接snsapi_base  获取code
	 *
	 * @param string $redirect_uri
	 *            跳转地址
	 * @param mixed $state
	 *            参数
	 *            不弹出授权页面，直接跳转，只能获取用户openid
	 */
	private function getOAuthFansAuthorize($redirect_url = '', $state = '')
	{
		$redirect_url = urlencode($redirect_url);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appid . "&redirect_uri=" . $redirect_url . "&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
		return $url;
	}
	
	
	
	/****************************************************************微信OAuth2授权登录-end*******************************************************************/
	
	
	/****************************************************************粉丝处理-start*******************************************************************/
	/**
	 * 拉取微信公众号的粉丝信息（用于更新公众号粉丝信息）
	 * @param string $start_openid
	 */
	public function getWechatFansAllList($start_openid = "")
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=" . $start_openid;
		$fansList = $this->sendWeixinRequest($request_url);
		$fansList = json_decode($fansList, true);
		if (isset($fansList['errcode']) && $fansList['errcode'] < 0) {
			return error($fansList, 'WECHAT_UPDATE_FANS_FAIL');
		}
		return success($fansList);
	}
	
	/**
	 * 通过openids获取粉丝信息的列表
	 * @return mixed
	 */
	public function getWechatFansQuery($openids)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=%s";
		$fansList = $this->sendWeixinRequest($request_url, $openids);
		$fansList = json_decode($fansList, true);
		if (isset($fansList['errcode']) && $fansList['errcode'] < 0) {
			return error($fansList, 'WECHAT_UPDATE_FANS_FAIL');
		}
		return success($fansList);
	}
	
	/**
	 * 微信公众号通过openid获取粉丝信息，针对关注公众号用户获取
	 *
	 * @param unknown $openid
	 * @return Ambigous <string, \data\extend\unknown, mixed>
	 */
	public function getWechatFansInfo($openid)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid={$openid}";
		$fansinfo = $this->sendWeixinRequest($request_url);
		$fansinfo = json_decode($fansinfo);
		return $fansinfo;
	}
	
	/****************************************************************粉丝处理-end*******************************************************************/
	
	
	/****************************************************************菜单处理-start*******************************************************************/
	/**
	 * 微信公众号自定义菜单更新，传入json
	 * @param unknown $menu_json
	 */
	public function createMenu($menu_json)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
		$menuinfo = $this->sendWeixinRequest($request_url, $menu_json);
		$res = json_decode($menuinfo, true);
		if (!isset($res['errcode'])) {
			return error('', json_encode($res, JSON_UNESCAPED_UNICODE));
		}
		
		if ($res['errcode'] != 0) {
			return error($res, json_encode($res, JSON_UNESCAPED_UNICODE));
		}
		
		return success($res);
	}
	
	/****************************************************************菜单处理-end*******************************************************************/
	
	
	/****************************************************************消息处理-start*******************************************************************/
	/**
	 * 消息发送  给指定用户
	 * @param unknown $openid
	 * @param unknown $msgtype
	 * @param unknown $content
	 */
	public function messageSend($openid, $msgtype, $content)
	{
		$json = '{"touser":"%s","msgtype":"%s","text":{"content":"%s"}}';
		$json_data = sprintf($json, $openid, $msgtype, $content);
		$request_url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
		$messageinfo = $this->sendWeixinRequest($request_url, $json_data);
		$messageinfo = json_decode($messageinfo, true);
		return $messageinfo;
	}
	
	/**
	 * 群发消息
	 * @param string $json_data
	 * @return mixed
	 */
	public function messageGroupSend($json_data)
	{
		//$request_url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=%s";
		$request_url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=%s";
		$messageinfo = $this->sendWeixinRequest($request_url, $json_data);
		$messageinfo = json_decode($messageinfo, true);
		return $messageinfo;
	}
	
	/****************************************************************消息处理-end*******************************************************************/
	
	
	/****************************************************************基础支持多媒体-start*******************************************************************/
	
	/**
	 * 基础支持： 上传永久图文消息素材接口 /media/post
	 */
	public function mediaNewsUpload($json_data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s";
		$mediaResult = $this->sendWeixinRequest($request_url, $json_data);
		$res = json_decode($mediaResult, true);
		
		if (empty($res['media_id'])) {
			return error($res, 'UPLOAD_FAIL');
		}
		return success($res);
	}
	
	/**
	 * 基础支持: 素材上传，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
	 * form-data中媒体文件标识，有filename、filelength、content-type等信息
	 */
	public function mediaUpload($type, $data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s&type=" . $type;
		
		$mediaResult = $this->sendWeixinRequest($request_url, $data);
		$res = json_decode($mediaResult, true);
		
		if (empty($res['media_id'])) {
			return error($res, 'UPLOAD_FAIL');
		}
		return success($res);
	}
	
	/**
	 * 基础支持: 下载永久素材接口 /media/get
	 * @param unknown $media_id
	 */
	public function mediaGet($media_id)
	{
		$request_url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=" . $media_id;
		$mediaResult = $this->sendWeixinRequest($request_url);
		$mediaResult = json_decode($mediaResult, true);
		return $mediaResult;
	}
	
	public function mediaCount()
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=%s";
		$mediaResult = $this->sendWeixinRequest($request_url);
		$mediaResult = json_decode($mediaResult, true);
		
		if (!empty($mediaResult['image_count'])) {
			$result = [
				"code" => 1,
				"message" => "",
				"data" => $mediaResult
			];
		} else {
			$result = [
				"code" => $mediaResult['errcode'],
				"message" => $mediaResult['errmsg'],
			];
		}
		return $result;
		return $mediaResult;
	}
	
	/**
	 * 基础支持:获取永久素材列表 /media/batchget
	 */
	public function mediaBatchget($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=%s";
		//$request_url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=" . $type;
		$mediaResult = $this->sendWeixinRequest($request_url, $data);
		$mediaResult = json_decode($mediaResult, true);
		dump($mediaResult);
		exit;
		if (!empty($mediaResult['media_id'])) {
			$result = [
				"code" => 1,
				"message" => "",
				"data" => $mediaResult
			];
		} else {
			$result = [
				"code" => $mediaResult['errcode'],
				"message" => $mediaResult['errmsg'],
			];
		}
		return $result;
	}
	
	/**
	 * 基础支持: 删除多媒体文件接口/media/del
	 */
	public function mediaDel($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=%s";
		
		$mediaResult = $this->sendWeixinRequest($request_url, $data);
		$mediaResult = json_decode($mediaResult, true);
		if ($mediaResult['errcode'] != 0) {
			return error($mediaResult, 'DELETE_FAIL');
		}
		
		return success('', 'DELETE_SUCCESS');
	}
	
	/****************************************************************基础支持多媒体-end*******************************************************************/
	
	
	/**
	 * **********************************************************************************微信推广二维码 开始*********************************************
	 */
	/**
	 * 生成永久二维码图片地址
	 * @param unknown $data_id
	 */
	public function qrcodeWeiXin($data_id)
	{
		if (empty($data_id))
			return "";
		$data_array = array(
			'action_name' => 'QR_LIMIT_STR_SCENE',
			'action_info' => array(
				'scene' => array(
					'scene_str' => $data_id
				)
			)
		);
		$qrcode_json = json_encode($data_array);
		return $this->qrcodeCreate($qrcode_json);
	}
	
	/**
	 * 推广支持: 创建二维码ticket接口 /qrcode/create && 换取二维码 /showqrcode
	 * 生成二维码基类函数
	 * @return src [二维码图片地址]
	 */
	public function qrcodeCreate($qrcode_json)
	{
		// 临时二维码请求说明POST-json：{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
		// 永久二维码请求说明POST-json：POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
		// action_name 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
		$jsonReturn = $this->sendWeixinRequest($url, $qrcode_json);
		$jsonReturn = json_decode($jsonReturn);
		if (!empty($jsonReturn->ticket)) {
			$ticket = $jsonReturn->ticket;
			// $QrCode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
			$QrCode = $jsonReturn->url;
		} else {
			$QrCode = '';
		}
		return $QrCode;
	}
	
	/**
	 * 把微信生成的图片存入本地
	 *
	 * @param [type] $username
	 *            [用户名]
	 * @param [string] $LocalPath
	 *            [要存入的本地图片地址]
	 * @param [type] $weixinPath
	 *            [微信图片地址]
	 *
	 * @return [string] [$LocalPath]失败时返回 FALSE
	 */
	public function imgWeiXinSaveLocal($local_path, $weixin_path)
	{
		$weixin_path_a = str_replace("https://", "http://", $weixin_path);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $weixin_path_a);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
		curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$r = curl_exec($ch);
		curl_close($ch);
		if (!empty($local_path) && !empty($weixin_path_a)) {
			$msg = file_put_contents($local_path, $r);
		}
		// 执行图片压缩
		$image = think\Image::open($local_path);
		$image->thumb(120, 120, \think\Image::THUMB_CENTER)->save($local_path);
		return $local_path;
	}
	
	/**
	 * **********************************************************************************微信推广二维码 结束*********************************************
	 */
	
	
	/**
	 * ***********************************************************************************分享接口***************************************************
	 */
	/**
	 * 微信分享  jsapi 分享票据
	 * @return mixed
	 */
	public function shareTicketJsapi()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
		$jsonReturn = $this->sendWeixinRequest($url);
		$jsonReturn = json_decode($jsonReturn);
		return $jsonReturn;
	}
	
	
	
	/**
	 * ******************************************************************模板消息接口*********************************************************************************************
	 */
	/**
	 * 获取微信模版id
	 * @param unknown $template_no
	 * @return mixed
	 */
	public function getWechatTemplateId($template_no)
	{
		$templateno_array = array(
			"template_id_short" => $template_no
		);
		$json = json_encode($templateno_array);
		$url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s";
		$jsonReturn = $this->sendWeixinRequest($url, $json);
		$jsonReturn = json_decode($jsonReturn);
		return $jsonReturn;
	}
	
	/**
	 * 获取微信模版列表
	 * @return mixed
	 */
	public function getWechatTemplateList()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=%s";
		$jsonReturn = $this->sendWeixinRequest($url);
		$jsonReturn = json_decode($jsonReturn);
		return $jsonReturn;
	}
	
	/**
	 * 给特定用户发送消息模版
	 * @param unknown $openid
	 * @param unknown $templateId
	 * @param unknown $url
	 * @param unknown $first
	 * @param unknown $keyword1
	 * @param unknown $keyword2
	 * @param unknown $keyword3
	 * @param unknown $keyword4
	 * @param unknown $remark
	 */
	public function templateMessageSend($openid, $templateId, $url, $first, $keyword1, $keyword2, $keyword3, $keyword4, $remark)
	{
		$array = array(
			'touser' => $openid,
			'template_id' => $templateId,
			'url' => $url,
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $first,
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $keyword1,
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => $keyword2,
					'color' => '#173177'
				),
				'keyword3' => array(
					'value' => $keyword3,
					'color' => '#173177'
				),
				'keyword4' => array(
					'value' => $keyword4,
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $remark,
					'color' => '#173177'
				)
			)
		);
		$json = json_encode($array);
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s";
		$jsonReturn = $this->sendWeixinRequest($url);
		$jsonReturn = json_decode($jsonReturn);
		return $jsonReturn;
	}
	
	
	/**
	 * 模板消息发送
	 * @param $param
	 */
	public function tmplmsgSend($param)
	{
		$openid = $param["open_id"];
		$template_id = $param["template_id"];
		$url = $param["url"];
		$first = $param["first"];//主内容
		$keyword = $param["keyword"];//关键字
		$remark = $param["remark"];//底部备注
		$data = [];
		$data["first"] = [ "value" => $first, "color" => '#173177' ];
		foreach ($keyword as $k => $v) {
			$data[ $k ] = [ "value" => $v, "color" => '#173177' ];
		}
		$data["remark"] = [ "value" => $remark, "color" => '#173177' ];
		$array = array(
			'touser' => $openid,
			'template_id' => $template_id,
			'url' => $url,
			'topcolor' => '#FF0000',
			'data' => $data
		);
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s";
		$jsonReturn = $this->sendWeixinRequest($url, json_encode($array));
		$jsonReturn = json_decode($jsonReturn);
		return $jsonReturn;
	}
	
	
	/**
	 * 删除模板消息
	 * @param unknown $template_id
	 */
	public function delPrivateTemplate($template_id)
	{
		$array = array( "template_id" => $template_id );
		$json = json_encode($array);
		$url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=%s";
		return $this->get_url_return($url, $json);
	}
	
	/**
	 * *************************************************微信回复消息部分 开始*****************************************************************************
	 */
	/**
	 * 回复文本消息
	 * @param string $from_user
	 * @param string $to_user
	 * @param string $content
	 * @param number $flag
	 * @return string
	 */
	public function replayTextXml($from_user, $to_user, $content, $flag = 0)
	{
		
		if (!empty($content)) {
			$xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
			
			
			$resultStr = sprintf($xml, $from_user, $to_user, time(), $content, $flag);
			return $resultStr;
		} else {
			return '';
		}
	}
	
	/**
	 * 回复图文消息
	 * @param string $from_user
	 * @param string $to_user
	 * @param array $items_array
	 * @param number $flag
	 * @return void|string
	 */
	public function replayNewsXml($from_user, $to_user, $items_array, $flag = 0)
	{
		// 首条标题28字，其他标题39字
		if (!is_array($items_array['value'])) {
			return;
		}
		$itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";
		$item_str = "";
		foreach ($items_array['value'] as $key => $item) {
			$item['url'] = addon_url('wechat://sitehome/material/previewgraphicmessage', [ 'id' => $items_array['id'], 'i' => $key ]);
			$item_str .= sprintf($itemTpl, $item['title'], $item['digest'], $item['cover']['path'], $item['url']);
		}
		$news_xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>$item_str</Articles>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
		$resultStr = sprintf($news_xml, $from_user, $to_user, time(), count($items_array['value']), $flag);
		return $resultStr;
	}
	
	/**
	 * 回复图片消息
	 * @param string $from_user
	 * @param string $to_user
	 * @param string $media_id
	 * @param number $flag
	 * @return string
	 */
	public function replayPictureXml($from_user, $to_user, $media_id, $flag = 0)
	{
		if (!empty($media_id)) {
			$xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image><MediaId><![CDATA[%s]]></MediaId></Image>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
			$resultStr = sprintf($xml, $from_user, $to_user, time(), $media_id, $flag);
			return $resultStr;
		} else {
			return '';
		}
	}
	
	/**
	 * 回复音频消息
	 * @param string $from_user
	 * @param string $to_user
	 * @param string $media_id
	 * @param number $flag
	 * @return string
	 */
	public function replayAudioXml($from_user, $to_user, $media_id, $flag = 0)
	{
		if (!empty($media_id)) {
			$xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        <Voice>
                            <MediaId>< ![CDATA[%s] ]></MediaId>
                        </Voice>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
			$resultStr = sprintf($xml, $from_user, $to_user, time(), $media_id, $flag);
			return $resultStr;
		} else {
			return '';
		}
	}
	
	/**
	 * 回复音乐消息
	 */
	public function replayMusicXml($from_user, $to_user, $title, $description, $music_url, $hq_music_url, $thumb_media_id = 'media_id', $flag = 0)
	{
		if (!empty($media_id)) {
			$xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        <Music>
                            <Title>< ![CDATA[%s] ]></Title>
                            <Description>< ![CDATA[%s] ]></Description>
                            <MusicUrl>< ![CDATA[%s] ]></MusicUrl>
                            <HQMusicUrl>< ![CDATA[%s] ]></HQMusicUrl>
                            <ThumbMediaId>< ![CDATA[%s] ]></ThumbMediaId>
                        </Music>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
			$resultStr = sprintf($xml, $from_user, $to_user, time(), $title, $description, $music_url, $hq_music_url, $thumb_media_id, $flag);
			return $resultStr;
		} else {
			return '';
		}
	}
	
	/**
	 * 回复视频消息
	 * @param string $from_user
	 * @param string $to_user
	 * @param string $media_id
	 * @param number $flag
	 * @return string
	 */
	public function replayVideoXml($from_user, $to_user, $media_id, $title, $description, $flag = 0)
	{
		if (!empty($media_id)) {
			$xml = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        <Video>
                            <MediaId>< ![CDATA[%s] ]></MediaId>
                            <Title>< ![CDATA[%s] ]></Title>
                            <Description>< ![CDATA[%s] ]></Description>
                        </Video>
                        <FuncFlag>%s</FuncFlag>
                    </xml>";
			$resultStr = sprintf($xml, $from_user, $to_user, time(), $media_id, $title, $description, $flag);
			return $resultStr;
		} else {
			return '';
		}
	}
	
	/**
	 * *************************************************微信回复消息部分 结束******************************************************************************
	 */
	
	/**
	 * 发送微信请求
	 * @param unknown $url
	 * @param string $data
	 * @param string $needToken
	 * @return string|unknown
	 */
	public function sendWeixinRequest($url, $data = '', $needToken = false)
	{
		// 第一次为空，则从文件中读取
		if (empty($this->accessToken)) {
			$this->accessToken = cache::get('accessToken-' . $this->appid);
		}
		// 为空则重新取值
		if (empty($this->accessToken) or $needToken) {
			if ($this->accountType == 'public') {
				$this->getAccessToken();
			} else {
				$this->getAccessTokenByWechatPlatform();
			}
			
			$this->accessToken = cache::get('accessToken-' . $this->appid);
		}
		$newurl = sprintf($url, $this->accessToken);
		$curl = curl_init(); // 创建一个新url资源
		curl_setopt($curl, CURLOPT_URL, $newurl);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$request_return = curl_exec($curl);
		$strjson = json_decode($request_return);
		if (!empty($strjson->errcode)) {
			switch ($strjson->errcode) {
				case 40001:
					return $this->sendWeixinRequest($url, $data, true); // 获取access_token时AppSecret错误，或者access_token无效
					break;
				case 40014:
					return $this->sendWeixinRequest($url, $data, true); // 不合法的access_token
					break;
				case 42001:
					return $this->sendWeixinRequest($url, $data, true); // access_token超时
					break;
				case 45009:
					return json_encode(array(
						"errcode" => -45009,
						"errmsg" => "接口调用超过限制：" . $strjson->errmsg
					));
					break;
				case 41001:
					return json_encode(array(
						"errcode" => -41001,
						"errmsg" => "缺少access_token参数：" . $strjson->errmsg
					));
					break;
				default:
					return json_encode(array(
						"errcode" => -41000,
						"errmsg" => $strjson->errmsg
					)); // 其他错误，抛出
					break;
			}
		} else {
			return $request_return;
		}
	}
	
	/**
	 * *************************************************消息管理 start***************************************************************************************
	 */
	
	/**
	 * 客服消息发送
	 * @return mixed|string|unknown
	 */
	public function sendWechatCustomservicesMessage($message, $open_id)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%";
		$data["touser"] = $open_id;
		$data["msgtype"] = "text";
		$data["text"] = [ "content" => $message ];
		$servicesList = $this->sendWeixinRequest($request_url, json_encode($data));
		$servicesList = json_decode($servicesList, true);
		return $servicesList;
	}
	
	/**
	 * *************************************************消息管理 end***************************************************************************************
	 */
	/**
	 * *************************************************微信客服 开始******************************************************************************
	 */
	/**
	 * 拉取微信公众号的客服人员信息（用于更新公众号客服信息）
	 * @param string $start_openid
	 */
	public function getWechatCustomservicesAllList()
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=%";
		$data["access_token"] = $this->access_token;
		$servicesList = $this->sendWeixinRequest($request_url, $data);
		$servicesList = json_decode($servicesList, true);
		return $servicesList;
	}
	
	/**
	 * 添加客服人员
	 * @return mixed|string|unknown
	 */
	public function addWechatCustomservices($kf_account, $nick_name)
	{
		
		$request_url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%";
		$data = array(
			"kf_account" => $kf_account,
			"nickname" => $nick_name
		);
		
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 邀请绑定微信客服
	 * @param $kf_account
	 * @param $wx_account
	 * @return mixed|string|unknown
	 */
	public function bingingWechatCustomservices($kf_account, $wx_account)
	{
		
		$request_url = "https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token=%";
		$data = array(
			"kf_account" => $kf_account,
			"invite_wx" => $wx_account
		);
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	
	/**
	 * 修改客服账号头像
	 * @param $kf_account
	 * @param $media = '@'.realpath($tmp_name).";type=".$type.";filename=".$file_name
	 * );
	 */
	public function uploadWechatCustomservicesHeadimg($kf_account, $media)
	{
		
		$request_url = "https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token=%&kf_account=$kf_account";
		$data = array(
			"media" => $media
		);
		$result = $this->sendWeixinRequest($request_url, $data);
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 设置客服人员信息
	 * @return mixed|string|unknown
	 */
	public function uploadWechatCustomservicesNickname($kf_account, $nick_name)
	{
		
		$request_url = "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=%";
		$data = array(
			"kf_account" => $kf_account,
			"nickname" => $nick_name
		);
		
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 删除客服账号
	 * @param $kf_account
	 * @return mixed|string|unknown
	 */
	public function deleteWechatCustomservices($kf_account)
	{
		$request_url = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=%&kf_account=$kf_account";
		$result = $this->sendWeixinRequest($request_url, array());
		$result = json_decode($result, true);
		return $result;
	}
	/**
	 * *************************************************微信回客服部分 结束******************************************************************************
	 */
	
	/**
	 * 创建标签
	 * @param unknown $data
	 */
	public function createWechatFansTag($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data, JSON_UNESCAPED_UNICODE));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 编辑标签
	 * @param unknown $data
	 */
	public function updateWechatFansTag($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data, JSON_UNESCAPED_UNICODE));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 删除标签
	 * @param unknown $data
	 */
	public function deleteWechatFansTag($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data, JSON_UNESCAPED_UNICODE));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 批量为用户打标签(标签功能目前支持公众号为用户打上最多20个标签。)
	 * @param unknown $data
	 * @return unknown
	 */
	public function batchtagging($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data, JSON_UNESCAPED_UNICODE));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 批量为用户取消标签
	 * @param unknown $data
	 * @return unknown
	 */
	public function batchuntagging($data)
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data, JSON_UNESCAPED_UNICODE));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取已存在的标签
	 */
	public function getTagList()
	{
		$request_url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=%s";
		$result = $this->sendWeixinRequest($request_url);
		$result = json_decode($result, true);
		return $result;
	}
	
	
	
	
	
	/**************************************************************微信小程序统计 start **********************************************************/
	
	
	/**
	 * 获取用户访问小程序月留存
	 * @param $param  begin_date    string        是    开始日期，为自然月第一天。格式为 yyyymmdd   end_date    string        是    结束日期，为自然月最后一天，限定查询一个月数据。格式为 yyyymmdd
	 * @return mixed|string|unknown
	 */
	public function getWechatMonthlyRetain($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyretaininfo?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取用户访问小程序周留存
	 * @param $param  begin_date    string        是    开始日期，为周一日期。格式为 yyyymmdd   end_date    string        是    结束日期，为周日日期，限定查询一周数据。格式为 yyyymmdd
	 * @return mixed|string|unknown
	 */
	public function getWeeklyRetain($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappidweeklyretaininfo?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取用户访问小程序日留存
	 * @param $param  begin_date    string        是    开始日期，为周一日期。格式为 yyyymmdd   结束日期，限定查询1天数据，允许设置的最大值为昨日。格式为 yyyymmdd
	 * @return mixed|string|unknown
	 */
	public function getDailyRetain($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailyretaininfo?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	
	/**
	 * 获取用户访问小程序数据月趋势
	 * @param $param
	 * @return mixed|string|unknown
	 */
	public function getMonthlyVisitTrend($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取用户访问小程序数据周趋势
	 * @param $param
	 * @return mixed|string|unknown
	 */
	public function getWeeklyVisitTrend($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取用户访问小程序数据日趋势
	 * @param $param
	 * @return mixed|string|unknown
	 */
	public function getDailyVisitTrend($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 时间间隔间的访问数据
	 * @param $param
	 * @return mixed|string|unknown
	 */
	public function getVisitPage($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=%s";
//	    $data = array(
//	        "begin_date" => $param["begin_date"],
//	        "end_date" => $param["end_date"],
//	    );
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**************************************************************微信小程序统计 end **********************************************************/
	
	/**************************************************************微信公众号统计 start **********************************************************/
	/**
	 * 获取用户增减数据
	 * $data = ['begin_date' => '2014-12-02', 'end_date' => '2014-12-07'];
	 */
	public function getUserSummary($data)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getusersummary?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取累计用户数据
	 * $data = ['begin_date' => '2014-12-02', 'end_date' => '2014-12-07'];
	 */
	public function getUserCumulate($data)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getusercumulate?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	
	/**
	 * 获取接口调用分析数据（按时）
	 * @param unknown $data
	 */
	public function getInterfaceSummaryHour($data)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getinterfacesummaryhour?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($data));
		$result = json_decode($result, true);
		return $result;
	}
	
	/**
	 * 获取接口分析数据（getinterfacesummary 以日为单位   区别于分时）
	 * @param $param
	 * @return mixed|string|unknown
	 */
	public function getInterFaceSummary($param)
	{
		$request_url = "https://api.weixin.qq.com/datacube/getinterfacesummary?access_token=%s";
		$result = $this->sendWeixinRequest($request_url, json_encode($param));
		$result = json_decode($result, true);
		return $result;
	}
	/**************************************************************微信公众号统计 end **********************************************************/
	
	
}