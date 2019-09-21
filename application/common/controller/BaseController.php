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

namespace app\common\controller;

use app\common\model\Address as AddressModel;
use app\common\model\Auth;
use app\common\model\User;
use think\Config;
use think\Controller;

/**
 * 系统通用控制器基类
 */
class BaseController extends Controller
{
	// 会员信息
	public $userInfo = null;
	
	public $url = '';
	
	/**
	 * 基类初始化
	 */
	public function __construct()
	{
		parent::__construct();
		
		// 初始化请求信息
		$this->initRequestInfo();
		
		if (IS_MANAGEMENT) {
			//初始化会员信息
			$this->initUserInfo();
		}
		
	}
	
	protected function initUserInfo()
	{
		$user_model = new User();
		$user = $user_model->getLoginInfo();
		$user = $user['data'];
		$this->userInfo = $user;
		$this->assign("user_info", $user);
		//当前登录uid
		defined('UID') or define('UID', $this->isLogin());
		
	}
	
	/**
	 * 判断用户是否登录
	 * @return int 0=未登录  >0返回登录人id
	 */
	protected function isLogin()
	{
		if (empty($this->userInfo)) {
			return 0;
		} else {
			return $this->userInfo['uid'];
		}
	}
	
	/**
	 * 初始化请求信息
	 */
	final private function initRequestInfo()
	{
		
		defined('IS_POST') or define('IS_POST', $this->request->isPost());                     //是否是post提交
		defined('IS_GET') or define('IS_GET', $this->request->isGet());                      //是否是get提交
		defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());                     //是否是ajax提交
		defined('IS_PJAX') or define('IS_PJAX', $this->request->isPjax());                     //是否是pjax提交
		defined('IS_MOBILE') or define('IS_MOBILE', $this->request->isMobile());                   //是否是手机端
		defined('PATH') or define('PATH', strtolower($this->request->path()));           //当前URL的pathinfo信息
		defined('SITE_ID') or define('SITE_ID', strtolower($this->request->siteid()));         //当前站点id
		defined('ADDON_NAME') or define('ADDON_NAME', strtolower($this->request->addon()));          //当前插件名称
		defined('MODULE_NAME') or define('MODULE_NAME', strtolower($this->request->module()));         //当前模块名称
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', strtolower($this->request->controller()));     //当前控制器名称
		defined('ACTION_NAME') or define('ACTION_NAME', strtolower($this->request->action()));         //当前操作名称
		defined('URL') or define('URL', CONTROLLER_NAME . '/' . ACTION_NAME);          //当前url  控制器/操作
		defined('URL_MODULE') or define('URL_MODULE', MODULE_NAME . '/' . URL);                      //模块url  模块/控制器/操作
		defined('URL_TRUE') or define('URL_TRUE', $this->request->url(true));                    //真实url  当前访问的url
		defined('DOMAIN') or define('DOMAIN', $this->request->domain());                     //当前的域名
		defined('IS_MANAGEMENT') or define('IS_MANAGEMENT', $this->isMangagement());         //是否是管理端口
		$addon = ADDON_NAME ? ADDON_NAME . '://' : '';
		$this->url = $addon . URL_MODULE;
		IS_PJAX ? config('default_ajax_return', 'html') : config('default_ajax_return', 'json');
		$this->param = $this->request->param();
		
		$this->setTitle();
	}
	
	private function isMangagement()
	{
		switch (MODULE_NAME) {
			case 'admin':
				return 1;
				break;
			case 'home':
				return 1;
				break;
			case 'sitehome':
				return 1;
				break;
			default:
				return 0;
				break;
		}
	}
	
	/**
	 * 设置标题
	 * @param string $title
	 */
	public function setTitle($title = '')
	{
		if ($title) {
			$this->assign('title', $title);
		} else {
			if (IS_MANAGEMENT) {
				$addon = ADDON_NAME ? ADDON_NAME . '://' : '';
				$url = $addon . URL_MODULE;
				$auth_model = new Auth();
				$res = $auth_model->getMenuInfo([ 'url' => $url ]);
				$title = $res['data']['title'];
				if (!$title) {
					$title = 'NiuCloud';
				}
				$this->assign('title', $title);
			} else {
				$this->assign('title', '');
			}
			
		}
	}
	/*********************************************************地理位置start********************************************************************/
	/**
	 * 通过ajax得到运费模板的地区数据
	 */
	public function getAreaList()
	{
		
		$address_model = new AddressModel();
		
		$level = input('level', 1);
		$pid = input("pid", 0);
		$condition = array(
			"level" => $level,
			"pid" => $pid
		);
		$list = $address_model->getAreaList($condition, "id, pid, name, level", "id asc");
		return $list;
	}
	
	/**
	 * 获取地理位置id
	 */
	public function getGeographicId()
	{
		$address_model = new AddressModel();
		$address = request()->post("address", ",,");
		$address_array = explode(",", $address);
		$province = $address_array[0];
		$city = $address_array[1];
		$district = $address_array[2];
		$subdistrict = $address_array[3];
		$province_list = $address_model->getAreaList([ "name" => $province, "level" => 1 ], "id", '');
		$province_id = !empty($province_list["data"]) ? $province_list["data"][0]["id"] : 0;
		$city_list = ($province_id > 0) && !empty($city) ? $address_model->getAreaList([ "name" => $city, "level" => 2, "pid" => $province_id ], "id", '') : [];
		$city_id = !empty($city_list["data"]) ? $city_list["data"][0]["id"] : 0;
		$district_list = !empty($district) && $city_id > 0 && $province_id > 0 ? $address_model->getAreaList([ "name" => $district, "level" => 3, "pid" => $city_id ], "id", '') : [];
		$district_id = !empty($district_list["data"]) ? $district_list["data"][0]["id"] : 0;
		
		$subdistrict_list = !empty($subdistrict) && $city_id > 0 && $province_id > 0 && $district_id > 0 ? $address_model->getAreaList([ "name" => $subdistrict, "level" => 4, "pid" => $district_id ], "id", '') : [];
		$subdistrict_id = !empty($subdistrict_list["data"]) ? $subdistrict_list["data"][0]["id"] : 0;
		return [ "province_id" => $province_id, "city_id" => $city_id, "district_id" => $district_id, "subdistrict_id" => $subdistrict_id ];
	}
	/*********************************************************地理位置end********************************************************************/
	
	/**
	 * 验证码
	 */
	public function captcha()
	{
		return captcha('', Config::get('captcha'));
	}
	
	/**
	 * 验证码验证
	 */
	public function checkCaptcha()
	{
		$captcha = input('captcha', '');
		
		if (empty($captcha)) return error('', '请输入验证码');
		if (!captcha_check($captcha)) return error('', '验证码错误');
		
		return success();
	}
}