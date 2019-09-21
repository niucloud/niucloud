<?php

namespace addon\system\DiyView\api\controller;

use addon\system\DiyView\common\model\Advertisement as advModel;
use app\common\controller\BaseApi;

class Advertisement extends BaseApi
{
	
	public $adv_model;
	
	/**
	 * 广告加载返回html数据
	 * @return \think\mixed
	 */
	public function adv($params)
	{
		$this->adv_model = new advModel();
		$type = isset($params['type']) ? $params['type'] : 'html';
		$adv_type = isset($params['adv_type']) ? $params['adv_type'] : 1;//所属类型 1.pc 2.手机
		
		if ($type == 'json') {
			return $this->adv_model->getAdvInfo([ 'site_id' => $params['site_id'], 'keywords' => $params['keywords'], 'adv_type' => $adv_type ]);
		}
		$adv_info = $this->adv_model->getAdvInfo([ 'site_id' => $params['site_id'], 'keywords' => $params['keywords'], 'adv_type' => $adv_type ]);
		if (empty($adv_info['data'])) {
			return success();
		}
		$adv_info['data']['adv_img_arr'] = json_decode($adv_info['data']['adv_json'], true);
		$this->assign('adv_info', $adv_info['data']);
		$this->assign('rand_str', $this->getRandStr(5));
		$class_name_arr = explode('_', strtolower($adv_info['data']['keywords']));
		$class_name = '';
		foreach ($class_name_arr as $k => $v) {
			$class_name .= $v . '-';
		}
		$class_name = trim($class_name, '-');
		$this->assign('adv_class_name', $class_name);
		//图片轮播
		if ($adv_info['data']['adv_display'] == 2) {
			if ($adv_type == 1) {
				$data = $this->fetch("addon/module/DiyView/sitehome/view/advertisement/pc_carousel.html");
			} else {
				$data = $this->fetch("addon/system/DiyView/sitehome/view/advertisement/wap_carousel.html");
			}
			return success($data);
		}
		//多图平铺
		if ($adv_info['data']['adv_display'] == 1) {
			if ($adv_type == 1) {
				$data = $this->fetch("addon/module/DiyView/sitehome/view/advertisement/pc_flat.html");
			} else {
				$data = $this->fetch("addon/system/DiyView/sitehome/view/advertisement/wap_flat.html");
			}
			return success($data);
		}
	}
	
	/**
	 * 获取随机字符串
	 * @param number $len
	 */
	public function getRandStr($len = 10)
	{
		$str = 'qwertyuiopasdfghjklzxcvbnm';
		$str = 'nc-' . substr(str_shuffle($str), 0, $len);
		return $str;
	}
}