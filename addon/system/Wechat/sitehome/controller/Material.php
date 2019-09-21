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

use util\weixin\Weixin;
use addon\system\Wechat\common\model\Wechat;

/**
 * 微信素材控制器
 */
class Material extends Base
{
	/**
	 * 素材列表--图文消息
	 */
	public function index()
	{
		return $this->fetch('material/index', [], $this->replace);
	}
	
	/**
	 * 添加图文消息
	 */
	public function addGraphicMessage()
	{
		$this->assign('material_id', '0');
		$this->assign('flag', false);
		return $this->fetch('material/edit_graphic_message', [], $this->replace);
	}
	
	/**
	 * 修改图文消息
	 */
	public function editGraphicMessage()
	{
		$this->assign('material_id', input('id', ''));
		$this->assign('flag', true);
		return $this->fetch('material/edit_graphic_message', [], $this->replace);
	}
	
	/**
	 * 预览图文
	 */
	public function previewGraphicMessage()
	{
		$id = input('id', '');
		$index = input('i', '');
		$wechat_model = new Wechat();
		$info = $wechat_model->getMaterialInfo([ 'id' => $id, 'site_id' => SITE_ID ]);
		if (!empty($info['data']['value']) && json_decode($info['data']['value'], true)) {
			$info['data']['value'] = json_decode($info['data']['value'], true);
		}
		
		$this->assign('info', $info['data']);
		$this->assign('index', $index);
		return $this->fetch('material/preview_graphic_message', [], $this->replace);
	}
	
	/**
	 * 添加微信素材
	 */
	public function add()
	{
		if (IS_AJAX) {
			$type = input('type', 1);
			$param['value'] = input('value', '');
			if ($type != 1) {
				// 图片、音频、视频、缩略图素材
				$file_path = input('path', '');
				$file_title = input('title', '');
				$file_introduction = input('introduction', '');
				//$name = input('name', ltrim(strrchr($file_path, '/'), '/'));
				$name = ltrim(strrchr($file_path, '/'), '/');
				$data = $this->fileHandling($type, $name, $file_path, $file_title, $file_introduction);
				$res = $this->uploadApi($type, $data);
				if ($res['code'] != 0) {
					return $res;
				}
				
				$value['file_path'] = $file_path;
				$value['name'] = $name;
				$value['url'] = $res['data']['url'];
				$param['value'] = json_encode($value);
				$param['media_id'] = $res['data']['media_id'];
			} else {
				$param['media_id'] = time() . 'GRAPHIC' . SITE_ID . 'MESSAGE' . rand(1, 1000);
			}
			$param['site_id'] = SITE_ID;
			$param['type'] = $type;
			
			$param['create_time'] = time();
			$param['update_time'] = time();
			$wechat_model = new Wechat();
			
			$res = $wechat_model->addMaterial($param);
			
			return $res;
		}
	}
	
	/**
	 * 文件流处理
	 */
	private function fileHandling($type, $name, $file_path, $file_title, $file_introduction)
	{
		$file_path = 'attachment/' . $file_path;
		if (class_exists('\CURLFile')) {
			$data = array(
				'media' => new \CURLFile(realpath($file_path))
			);
		} else {
			$data = array(
				'media' => '@' . realpath($file_path)
			);
		}
		
		$data['media']->postname = $name;
		// 视频素材需要标题与描述信息
		if ($type == 4) {
			$data['description'] = '{"title" :' . $file_title . ',"introduction" :' . $file_introduction . '}';
		}
		
		return $data;
	}
	
	/**
	 * 上传永久素材
	 * @param number $type
	 * @param object $data
	 * @return multitype:string mixed
	 */
	public function uploadApi($type, $data)
	{
		$wechat_config_model = new Wechat();
		$wechat_config_info = $wechat_config_model->getWechatConfigInfo(SITE_ID);
		$config_info = $wechat_config_info['data']['value'];
		if (empty($config_info['appid']) || empty($config_info['appsecret'])) {
			return error('', 'WECHAT_UNCONFIGURED');
		}
		
		$wechat_api = new Weixin('public');
		$wechat_api->initWechatPublicAccount($config_info['appid'], $config_info['appsecret']);
		
		if ($type == 1) {
			$res = $wechat_api->mediaNewsUpload($data);
		} else {
			if ($type == 2) {
				$type = 'image';
			} else if ($type == 3) {
				$type = 'voice';
			} else if ($type == 4) {
				$type = 'video';
			} else if ($type == 6) {
				$type = 'thumb';
			}
			$res = $wechat_api->mediaUpload($type, $data);
		}
		return $res;
	}
	
	/**
	 * 修改微信素材
	 */
	public function edit()
	{
		if (IS_AJAX) {
			$condition['id'] = input('id', '');
			$condition['site_id'] = SITE_ID;
			
			$data['value'] = input('value', '');
			$data['update_time'] = time();
			
			$wechat_model = new Wechat();
			$res = $wechat_model->editMaterial($data, $condition);
			
			return $res;
		}
	}
	
	/**
	 * 删除微信素材
	 */
	public function del()
	{
		if (IS_AJAX) {
			$condition['id'] = input('id', '');
			$media_id = input('media_id', '');
			$wechat_model = new Wechat();
			$res = $wechat_model->deleteMaterial($condition);
			
			return $res;
		}
	}
	
	/**
	 * 微信素材列表
	 */
	public function lists()
	{
		if (IS_AJAX) {
			$type = input('type', '');
			$name = input('name', '');
			$page_index = input('page', 1);
			$list_rows = input('limit', PAGE_LIST_ROWS);
			$condition['site_id'] = SITE_ID;
			
			if (!empty($type)) {
				$condition['type'] = $type;
			}
			
			if (!empty($name)) {
				$condition['value'] = array(
					'like',
					'%"name":"%' . $name . '%","url"%'
				);
			}
			
			$wechat_model = new Wechat();
			$material_list = $wechat_model->getMaterialPageList($condition, $page_index, $list_rows);
			if (!empty($material_list['data']['list']) && is_array($material_list['data']['list'])) {
				foreach ($material_list['data']['list'] as $k => $v) {
					if (!empty($v['value']) && json_decode($v['value'])) {
						$material_list['data']['list'][ $k ]['value'] = json_decode($v['value'], true);
					}
				}
			}
			
			return $material_list;
		}
	}
	
	/**
	 * 微信素材详情
	 */
	public function detail()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$condition = array(
				'id' => input('id', '')
			);
			
			$material_info = $wechat_model->getMaterialInfo($condition);
			if (json_decode($material_info['data']['value'])) {
				$material_info['data']['value'] = json_decode($material_info['data']['value']);
			}
			
			return $material_info;
		}
	}
	
	/**
	 * 图文素材
	 */
	public function articleList()
	{
		$wechat_model = new Wechat();
		$condition = array(
			'type' => 1,
			'site_id' => SITE_ID
		);
		$material_list = $wechat_model->getMaterialList($condition, '*', 'update_time desc');
		if (!empty($material_list['data']) && is_array($material_list['data'])) {
			foreach ($material_list['data'] as $k => $v) {
				if (!empty($v['value']) && json_decode($v['value'])) {
					$material_list['data'][ $k ]['value'] = json_decode($v['value'], true);
				}
			}
		}
		$this->assign('material_list', $material_list);
		return $this->fetch('material/index', [], $this->replace);
	}
	
	/**
	 * 素材管理
	 */
	public function materialMannager()
	{
		//这这里的常量要与base中的区分，如果一致界面将无法渲染
		$replace = [
			'ADDON_WECHAT_MANAGER_CSS' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/css',
			'ADDON_WECHAT_MANAGER_JS' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/js',
			'ADDON_WECHAT_MANAGER_IMG' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/img',
		];
		return array( 'sitehome/material/material_mannager', [], $replace );
	}
}