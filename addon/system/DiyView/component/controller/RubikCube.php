<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 魔方·组件
 * 创建时间：2018年7月9日12:13:35
 *
 */
class RubikCube extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		if (!empty($attr['diyHtml'])) {
			$attr['diyHtml'] = str_replace("&quot;", '"', $attr['diyHtml']);
		}
		$this->assign("attr", $attr);
		return $this->fetch('rubik_cube/rubik_cube.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("rubik_cube/design.html");
	}
}