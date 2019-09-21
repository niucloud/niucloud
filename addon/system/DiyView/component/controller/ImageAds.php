<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 图片广告·组件
 * 创建时间：2018年7月9日12:13:35
 *
 */
class ImageAds extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		// 获取最后一个用于循环轮播
		$this->assign("last_item", $attr['list'][ count($attr['list']) - 1 ]);
		return $this->fetch('image_ads/image_ads.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("image_ads/design.html");
	}
}