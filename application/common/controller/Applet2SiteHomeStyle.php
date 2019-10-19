<?php

namespace app\common\controller;


class Applet2SiteHomeStyle extends BaseSiteHomeStyle
{
	
	public function loadMenu(&$baseSiteHome)
	{
		$baseSiteHome->assign('style', "sitehome@style/applet2/base");
		hook("appMenu", [ 'this' => &$baseSiteHome ], [], true);
		define("LOAD_MENU", 1);
	}
	
}