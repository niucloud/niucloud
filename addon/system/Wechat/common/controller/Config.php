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

namespace addon\system\Wechat\common\controller;

use addon\system\Wechat\common\model\Wechat;
use think\Controller;
use think\Log;
use util\weixin\Weixin;
use app\common\model\Config as ConfigModel;

/**
 * 控制器
 */
class Config extends Controller
{
	public $wechatApi;
    public $wechat;
	public $author_appid;

	public function __construct()
	{
        $this->wechat = new Wechat();
        $action = request()->action();
        if (strtolower($action) == 'relateweixin') {
            $this->getMessage();
        }
	}
	
	/**
	 * ************************************************************************微信公众号消息相关方法 开始******************************************************
	 */

    /**
     * 关联公众号微信
     */
    public function relateWeixin()
    {
        $sign = request()->get('signature', '');
        if (isset($sign)) {
            $signature = $sign;
            $timestamp = request()->get('timestamp');
            $nonce = request()->get('nonce');
            $site_id = request()->siteid();
            $wchat_config = $this->wechat->getWechatConfigInfo($site_id);

            if (!empty($wchat_config['data'])) {
                $config_info = $wchat_config['data']['value'];
                $token = $config_info['token'];
            } else {
                $token = "TOKEN";
            }

            $tmpArr = array(
                $token,
                $timestamp,
                $nonce
            );
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                $echo_str = request()->get('echostr', '');
                if (!empty($echo_str)) {
                    ob_clean();
                    echo $echo_str;
                }
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * 微信公众平台模式
     * 微信获取消息以及返回接口
     */
    public function getMessage()
    {
        $from_xml = file_get_contents('php://input');
        if (empty($from_xml)) {
            return;
        }
        $signature = input('msg_signature', '');
        $signature = input('timestamp', '');
        $nonce = input('nonce', '');
        $ticket_xml = $from_xml;

        $postObj = simplexml_load_string($ticket_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Log::write($postObj);
        if (!empty($postObj->MsgType)) {
            switch ($postObj->MsgType) {
                case "text":
                    $replay = $this->wechat->getSiteWechatKeywordsReplay((string) $postObj->FromUserName, (string) $postObj->ToUserName, request()->siteid(), (string) $postObj->Content);
                    break;
                case "event":
                    $replay = $this->MsgTypeEvent($postObj);
                    break;
                default:
                    $replay = "";
                    break;
            }
        }
        if (!empty($replay['data'])) {
//            Log::write($replay['data']);
            ob_clean();
            echo $replay['data'];
        } else {
            echo '';
        }
        exit();
    }

    /**
     * 文本消息回复格式
     *
     * @param unknown $postObj
     * @return Ambigous <void, string>
     */
    private function MsgTypeText($postObj)
    {
        $wchat_replay = $this->wechat->getWhatReplay($this->instance_id, (string) $postObj->Content);
        // 判断用户输入text
        if (!empty($wchat_replay)) { // 关键词匹配回复
            $contentStr = $wchat_replay; // 构造media数据并返回
        } elseif ($postObj->Content == "uu") {
            $contentStr = "siteId：" . SITE_ID;
        } elseif ($postObj->Content == "TESTCOMPONENT_MSG_TYPE_TEXT") {
            $contentStr = "TESTCOMPONENT_MSG_TYPE_TEXT_callback"; // 微店插件功能 关键词，预留口
        } elseif (strpos($postObj->Content, "QUERY_AUTH_CODE") !== false) {
            $get_str = str_replace("QUERY_AUTH_CODE:", "", $postObj->Content);
            $contentStr = $get_str . "_from_api"; // 微店插件功能 关键词，预留口
        } else {
            $content = $this->wechat->getDefaultReplay($this->instance_id);
            if (!empty($content)) {
                $contentStr = $content;
            } else {
                $contentStr = '';
            }
        }
        if (is_array($contentStr)) {
            $resultStr = $this->wchat->event_key_news($postObj, $contentStr);
        } elseif (!empty($contentStr)) {
            $resultStr = $this->wchat->event_key_text($postObj, $contentStr);
        } else {
            $resultStr = '';
        }
        return $resultStr;
    }

    /**
     * 事件消息回复机制
     */
    // 事件自动回复 MsgType = Event
    private function MsgTypeEvent($postObj)
    {
        $contentStr = "";
        $material_id = 0;
        $material_type = 0;
        $wexin = $this->wechat->weixinApi(request()->siteid());
//		$wexin = new Weixin('public');
//		$config_result = $this->wechat->getWechatConfigInfo(request()->siteid());
//		$config_data = $config_result["data"]["value"];
//		$wexin->initWechatPublicAccount($config_data["appid"], $config_data["appsecret"]);
        switch ($postObj->Event) {
            case "subscribe": // 关注公众号
                $Userstr = $wexin->getWechatFansInfo($postObj->FromUserName);
                if (preg_match("/^qrscene_/", $postObj->EventKey)) {
                    $source_uid = substr($postObj->EventKey, 8);
                    $_SESSION['source_shop_id'] = $this->instance_id;
                    $_SESSION['source_uid'] = $source_uid;
                } elseif (!empty($_SESSION['source_uid'])) {
                    $source_uid = $_SESSION['source_uid'];
                    $_SESSION['source_shop_id'] = $this->instance_id;
                } else {
                    $source_uid = 0;
                }
                $nickname = base64_encode($Userstr->nickname);
                $nickname_decode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $Userstr->nickname);
                $headimgurl = $Userstr->headimgurl;
                $sex = $Userstr->sex;
                $language = $Userstr->language;
                $country = $Userstr->country;
                $province = $Userstr->province;
                $city = $Userstr->city;
                $district = "无";
                $openid = $Userstr->openid;
                if (!empty($Userstr->unionid)) {
                    $unionid = $Userstr->unionid;
                } else {
                    $unionid = '';
                }
//                Log::write('user_info:');
//                Log::write($str);
//                $subscribe_date = date('Y/n/j G:i:s', (int) $postObj->CreateTime);
                $memo = $Userstr->remark;
                $data = array(
                    'site_id' => SITE_ID,
                    'nickname' => $nickname,
                    'nickname_decode' => $nickname_decode,
                    'headimgurl' => $headimgurl,
                    'sex' => $sex,
                    'language' => $language,
                    'country' => $country,
                    'province' => $province,
                    'city' => $city,
                    'district' => $district,
                    'openid' => $openid,
                    'unionid' => $unionid,
                    'groupid' => '',
                    'is_subscribe' => 1,
                    'memo' => $memo,
                    'update_date' => time()
                );
                $wechat_subscribe = $this->wechat->saveWechatFans($data); // 关注
                // 添加关注回复
                $content = $this->wechat->getWechatFollowReplay((string) $openid, (string) $postObj->ToUserName, SITE_ID);
                if (!empty($content['data'])) {
                    $contentStr = $content['data'];
                }
                // 构造media数据并返回 */
                break;
            case "unsubscribe": // 取消关注公众号
                $openid = $postObj->FromUserName;
                $weichat_unsubscribe = $this->wechat->unfollowWechat(SITE_ID, (string) $openid);
                break;
            case "VIEW": // VIEW事件 - 点击菜单跳转链接时的事件推送
                /* $this->wchat->weichat_menu_hits_view($postObj->EventKey); //菜单计数 */
                $contentStr = "";
                break;
            case "SCAN": // SCAN事件 - 用户已关注时的事件推送 - 扫描带参数二维码事件
                $openid = $postObj->FromUserName;
                $data = $postObj->EventKey;
                $user_bound = $this->wechat->userBoundParent((string) $openid, $data);
                $content = $this->wechat->getSubscribeReplay($this->instance_id);
                if (!empty($content)) {
                    $contentStr = $content;
                }
                break;
            case "CLICK": // CLICK事件 - 自定义菜单事件
                $openid = $postObj->FromUserName;

                if (strpos($postObj->EventKey, 'MATERIAL_GRAPHIC_MESSAGE_') === 0) {
                    $material_id = substr($postObj->EventKey, 25);
                    $material_type = 1;
                } else if (strpos($postObj->EventKey, 'MATERIAL_PICTURE_') === 0) {
                    $material_id = substr($postObj->EventKey, 17);
                    $material_type = 2;
                } else if (strpos($postObj->EventKey, 'MATERIAL_TEXT_MESSAGE_') === 0) {
                    $material_id = substr($postObj->EventKey, 22);
                    $material_type = 5;
                }
                $media_info = $this->wechat->getMaterialInfo([ 'id' => $material_id, 'type' => $material_type ]);
                if ($media_info['code'] == 0 && !empty($media_info['data']['value'])) {
                    $media_info['data']['value'] = json_decode($media_info['data']['value'], true);
                }
                $contentStr = $media_info['data'];// 构造media数据并返回 */
                break;
            default:
                break;
        }
        // $contentStr = $postObj->Event."from_callback";//测试接口正式部署之后注释不要删除
        switch ($material_type) {
            case 1 : //图文
                $resultStr = $wexin->replayNewsXml((string) $openid, (string) $postObj->ToUserName, $contentStr);
                break;
            case 2 : //图片
                $resultStr = $wexin->replayPictureXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['media_id']);
                break;
            case 3 : //音频
                $resultStr = $wexin->replayAudioXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['media_id']);
                break;
            case 4 : //视频
                $resultStr = $wexin->replayVideoXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['media_id'], (string) $contentStr['title'], (string) $contentStr['description']);
                break;
            case 5 : //文本
                $resultStr = $wexin->replayTextXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['value']['content']);
                break;
            case 6 : //缩略图
                $resultStr = $wexin->replayPictureXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['media_id']);
                break;
            case 8 : //音乐
                $resultStr = $wexin->replayMusicXml((string) $openid, (string) $postObj->ToUserName, (string) $contentStr['title'], (string) $contentStr['description'], (string) $contentStr['music_url'], (string) $contentStr['hq_music_url']);
                break;
            default:
                $resultStr = $wexin->replayTextXml((string) $openid, (string) $postObj->ToUserName, $contentStr);
                break;
        }
        return success($resultStr);
    }
	/**
	 * ************************************************************************微信公众号消息相关方法 结束******************************************************
	 */



    /**
     * *****************************************微信授权部分，用于应用微信公众号授权******************************************************************************
     */
    /**
     * 接入微信授权
     */
    public function index()
    {
        $from_xml = file_get_contents('php://input');
        $signature = input('msg_signature', '');
        $timestamp = input('timestamp', '');
        $nonce = input('nonce', '');

        Log::write("接受xml" . $from_xml);
        $weixin = new Weixin('platform');
        $config_model = new ConfigModel();
        $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
        if($wechat_info_result["data"]["status"] == 0)
            exit("缺少接入平台关键数据!");

        $wechat_info = json_decode($wechat_info_result['data']['value'], true);

        $weixin->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
        $res = $weixin->getComponentVerifyTicket($signature, $timestamp, $nonce, $from_xml);
        Log::write("接受ticket" . $res);
    }

    /**
     * 授权接口
     */
    public function auth()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $config_model = new ConfigModel();
        $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);

        if($wechat_info_result["data"]["status"] == 0)
            exit("缺少接入平台关键数据!");

        $wechat_info = json_decode($wechat_info_result['data']['value'], true);

        $weixin = new Weixin('platform');
        $weixin->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
        $this->redirect($weixin->authUrl($url));
    }

    /**
     * 授权成功接收页
     */
    public function callback()
    {
        $author_code = input('auth_code', '');
        $config_model = new ConfigModel();
        $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
        if($wechat_info_result["data"]["status"] == 0)
            exit("缺少接入平台关键数据!");

        $wechat_info = json_decode($wechat_info_result['data']['value'], true);
        $weixin = new Weixin('platform');
        $weixin->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
        if (!empty($author_code)) {
            // 获取微信授权成功的基本信息
            $res_data = $weixin->getQueryAuth($author_code);
            $data_info = $res_data->authorization_info;
            $arr = object_to_array($data_info);
            $datainfo = json_encode($arr);

            echo "<a href=''>点击返回主页</a>";
        }
    }

    /**
     * 微信开放平台模式(需要对消息进行加密和解密)
     * 微信获取消息以及返回接口
     */
    public function getPlatformMessage()
    {
        $from_xml = file_get_contents('php://input');
        if (empty($from_xml)) {
            return;
        }
        $signature = input('msg_signature', '');
        $timestamp = input('timestamp', '');
        $nonce = input('nonce', '');
        $config_model = new ConfigModel();
        $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
        if($wechat_info_result["data"]["status"] == 0)
            exit("缺少接入平台关键数据!");

        $wechat_info = json_decode($wechat_info_result['data']['value'], true);
        $weixin = new Weixin('platform');
        $weixin->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
        $ticket_xml = $weixin->getTicket($signature, $timestamp, $nonce, $from_xml);
        $wechat_model = new Wechat();
        //微信开放平台检测
        $url = request()->url(true);
        $url_arr = explode("/", $url);
        $count_arr = count($url_arr);
        $appid = $url_arr[ $count_arr - 2 ];
        $this->author_appid = $appid;
        Log::write("获取appid" . $this->author_appid);
        Log::write("获取xml" . $ticket_xml);
        $postObj = simplexml_load_string($ticket_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Log::write("获取leixing" . $postObj->MsgType);
        if (!empty($postObj->MsgType)) {
            switch ($postObj->MsgType) {
                case "text":
                    if ($postObj->Content == "TESTCOMPONENT_MSG_TYPE_TEXT") {
                        $replay['data'] = $weixin->replayTextXml((string) $postObj->FromUserName, (string) $postObj->ToUserName, "TESTCOMPONENT_MSG_TYPE_TEXT_callback"); // 微店插件功能 关键词，预留口
                    } elseif (strpos($postObj->Content, "QUERY_AUTH_CODE") !== false) {
                        $get_str = str_replace("QUERY_AUTH_CODE:", "", $postObj->Content);
                        $replay['data'] = $weixin->replayTextXml((string) $postObj->FromUserName, (string) $postObj->ToUserName, $get_str . "_from_api"); // 微店插件功能 关键词，预留口
                    } else {
                        $site_auth = $wechat_model->getSiteAuthByAppid($this->author_appid);
                        $replay = $wechat_model->getSiteWechatKeywordsReplay((string) $postObj->FromUserName, (string) $postObj->ToUserName, $site_auth['data']['site_id'], (string) $postObj->Content);
                    }
                    break;
                case "event":
                    $resultStr = $this->MsgTypeEvent($postObj);
                    $replay = '';
                    break;
                default:
                    $replay = "";
                    break;
            }
        }
        if (!empty($replay['data'])) {
            echo $weixin->encMsg($timestamp, $nonce, $replay['data']);
        } else {
            echo '';
        }
        exit();
    }
    /**
     * *****************************************微信授权部分，用于应用微信公众号授权******************************************************************************
     */
}