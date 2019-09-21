<?php

namespace app\common\controller;


class AppletSiteHomeStyle extends BaseSiteHomeStyle
{
	
	public function loadMenu(&$baseSiteHome)
	{
		$baseSiteHome->assign('style', "sitehome@style/applet/base");
		hook("appMenu", [ 'this' => &$baseSiteHome ], [], true);
		define("LOAD_MENU", 1);
	}
	
}