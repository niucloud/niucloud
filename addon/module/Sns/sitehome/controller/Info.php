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
namespace addon\module\Sns\sitehome\controller;

use addon\module\Sns\common\model\InfoCategory;
use addon\system\DiyView\common\model\BottomNav;
use app\common\controller\BaseSiteHome;
use addon\module\Sns\common\model\Info as InfoModel;
use app\common\model\Address;

/**
 * 分类信息控制器
 * @author Administrator
 *
 */
class Info extends BaseSiteHome
{
    public $info_model;
    public $category_model;
    public $address;
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'SNS_CSS' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/css',
            'SNS_JS' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/js',
            'SNS_IMG' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/img',
        ];

        $this->info_model = new InfoModel();
        $this->category_model = new InfoCategory();
        $this->address = new Address();

    }

    /**
     * 后台信息列表
     */
    public function infoList(){

        $page_index = input('page',1);
        $p_category_id = input('p_category_id',0);
        $condition = ['site_id' => $this -> siteId];

        if(IS_AJAX){

            //二级分类ID拼装完毕
            $category_str_id = $this->info_model->getCategoryId(array('parent' => $p_category_id,'site_id'=> $this -> siteId));

            $search  = input('search');
            $category_id  = input('category_id');
            $page_size = input('limit', PAGE_LIST_ROWS);

            //根据平台查询
            //后台
//            $condition['uid'] = [ '>', 0 ];


            //根据分类查询
            if(!empty($category_id)){
                $condition['category_id'] = $category_id;
            }else if(!empty($p_category_id)){
                //根据所有二级分类查询信息
                $condition['category_id'] = [ 'in', $category_str_id ];
            }

            //根据搜索条件查询
            if(!empty($search)){
                $condition['title'] = [ 'like', '%' . $search . '%' ];
            }
            $list = $this->info_model->getInfoPageList($condition, $page_index, $page_size, 'info_id desc');
            return $list;
        }

        //获取所有分类
        $category_one = $this->info_model->getCategorySelect(['parent' => 0,'site_id'=> $this -> siteId]);

        $category_two['data'] = array();
        if($p_category_id){
            $category_two = $this->info_model->getCategorySelect(array('parent' => $p_category_id,'site_id'=> $this -> siteId));
        }

        return $this->fetch(
            'info/info_list',
            [
                $condition,'page_index' => $page_index,
                'category_data' => $category_one['data'],
                'category' => $category_two['data'],
                'p_category_id' => $p_category_id,
            ],
            $this->replace
        );
    }

    /**
     * 前台信息列表
     */
    public function memberInfoList(){

        $page_index = input('page',1);
        $condition = ['site_id' => $this -> siteId];


        if(IS_AJAX){

            $search  = input('search');
            $category_id  = input('category_id');
            $page_size = input('limit', PAGE_LIST_ROWS);

            //根据平台查询

            //前台
            $condition['member_id'] = [ '>', 0 ];

            //根据分类查询
            if(!empty($category_id)){
                $condition['category_id'] = $category_id;
            }

            //根据搜索条件查询
            if(!empty($search)){
                $condition['title'] = [ 'like', '%' . $search . '%' ];
            }

            $list = $this->info_model->getInfoPageList($condition, $page_index, $page_size, 'info_id desc');

            return $list;
        }

        //获取所有分类
        $category_data = $this->info_model->getCategorySelect(['parent' => 0,'site_id'=> $this -> siteId]);

        return $this->fetch('info/member_info_list', [$condition,'page_index' => $page_index,'category' => $category_data['data']], $this->replace);
    }

    /**
     * 信息详情imgS
     */
    public function getInfoDetails(){
        $info_id = input('info_id');

        //分类信息内容详情
        $condition = ['info_id' => $info_id];
        $info_detail = $this->info_model -> getInfoDetail($condition);

        $imgs = array();
        if(!empty($info_detail['imgs'])){
            $img_s = explode(',',$info_detail['imgs']);
        }

        foreach($img_s as $key => $val){
            $imgs[]['path'] = $val;
        }

        return success($imgs);
    }

    /**
     * ajax修改信息 imgS
     */
    public function setInfoImgs()
    {
        $info = input('');
        $info_id = $info['info_id'];
        $data = $info['data'];
        $condition = ['info_id' => $info_id];

        $imgs = array();
        foreach($data as $key => $val){
            $imgs[] = $val['path'];
        }

        $imgs_str = implode(',',$imgs);

        //获取信息imgs
        $info_detail = $this->info_model -> getInfoDetail($condition);

        //组装条件
        $condition = ['info_id' => $info_id, 'imgs' =>  $info_detail['imgs'].','.$imgs_str];

        //更新
        return $this->info_model -> updateInfo($condition);
    }

    /**
     * 编辑信息
     */
    public function editInfo(){

        $info_id = input('info_id');
        $page_index = input('page',1);

        //修改
        if(IS_AJAX){

            $param = input('');
            $attribute_id = input('attribute_id','');

            $param['circle'] = $param['city'];
            $param['img_cover'] = !empty($param['check_s']) ? $param['check_s'][0] : '';
            $param['imgs'] = !empty($param['check_s']) ? implode(",", $param['check_s']) : '';
            $param['editor_attribute_'.$attribute_id]= $param['editor'];

            $res = $this->info_model->editInfo($param);
            return $res;
        }

        //分类信息内容详情
        $condition = ['info_id' => $info_id];
        $info_detail = $this->info_model -> getInfoDetail($condition);

        //获取分类属性值
        $info_attribute_value = $this->info_model -> getInfoAttributeValue($info_id);

        //分类信息
        $category_info = $this->category_model->getInfoCategoryDetail($info_detail['category_id'], $this->siteId);
        $parent_category_info = $this->category_model->getInfoCategoryDetail($category_info['parent'], $this->siteId);

        //分类属性
        $condition = array(
            'category_id' => $info_detail['category_id'],
            'site_id'     => $this->siteId
        );

        $list = $this->category_model->getInfoCategoryAttributeList($condition, '*', 'sort desc');

        //获取省ID
        $circle = $this->address -> getArea($info_detail['circle']);

        //获取所有的省
        $province = $this ->address -> getProvinces();

        //获取当前省下的所有城市
        $city = $this ->address -> getProvinces($circle['pid']);

        //获取分类信息
        $category_list = $this->category_model->getInfoCategoryTree($this->siteId);

        foreach($list['data'] as $key=>$item){

            $arr = explode("|",$item['input_args']);
            $r = array();
            foreach ($arr as $val ){
                $t = explode(":",$val);

                $v = $t[1];
                if($t[0] == 'select_option' || $t[0] == 'radio_option' || $t[0] == 'checkbox_option'){
                    $v = explode('/', $t[1]);
                }
                $r[$t[0]]= $v;
            }
            $list['data'][$key]['args'] = $r;
        }

        $this->assign('info_attribute_value', $info_attribute_value);

        $this->assign('category_info', $category_info);

        $this->assign('parent_category_info', $parent_category_info);

        $this->assign('info_detail', $info_detail);

        $this->assign('list', $list);

        $this->assign('category_list', $category_list['data']);

        $this->assign('circle', $circle);

        $this->assign('province', $province);

        $this->assign('city', $city);

        return $this->fetch('info/edit_info', ['page_index' => $page_index], $this->replace);
    }

    /**
     * 添加信息
     */
    public function addInfo(){

        if (IS_AJAX) {
            $param = input('');
            $param['img_cover'] = !empty($param['check_s']) ? $param['check_s'][0] : '';
            $param['imgs'] = !empty($param['check_s']) ? implode(",", $param['check_s']) : '';
            $param['uid'] = UID;

            $info_id = $this->info_model->addInfo($param);

            //生成二维码
            $info_url = addon_url('sns://wap/category/info'); //获取生成二维码访问地址
            $info_url .= '?info_id='.$info_id;
            $path = 'attachment/' . SITE_ID . '/qrcode'; //组装二维码路径
            $file_name =  'sns_category_qrcode';
            $qrcode_url = qrcode($info_url, $path, $file_name); //生产二维码
            $data['qrcode'] = str_replace('attachment/', '', $qrcode_url); //二维码路径处理
            $data['info_id'] = $info_id;

            $res = $this->info_model -> updateInfo($data); //修改二维码

            return $res;
        }
        $list = $this->category_model->getInfoCategoryTree($this->siteId);
        return $this->fetch('info/add_info', ['category_list'=> $list['data']], $this->replace);
    }

    /**
     * @return mixed
     * 信息属性加载
     */
    public function infoAttribute(){

        $category_id = input('category_id', 0);

        //分类属性
        $condition = array(
            'category_id' => $category_id,
            'site_id'     => $this->siteId
        );

        $list = $this->category_model->getInfoCategoryAttributeList($condition, '*', 'sort desc');

        foreach($list['data'] as $key=>$item){

            $arr = explode("|",$item['input_args']);
            $r = array();
            foreach ($arr as $val ){
                $t = explode(":",$val);

                $v = $t[1];
                if($t[0] == 'select_option' || $t[0] == 'radio_option' || $t[0] == 'checkbox_option'){
                    $v = explode('/', $t[1]);
                }
                $r[$t[0]]= $v;
            }
            $list['data'][$key]['args'] = $r;
        }

        //分类信息详情
        $category_info = $this->category_model->getInfoCategoryDetail($category_id, $this->siteId);
        return $this->fetch('info/info_attribute',
            [
                'list' => $list,
                'category_info' => $category_info,
                'category_name' => input('category_name', '')
            ], $this->replace);
    }

    /**
     * 修改分类信息排序 sort
     */
    public function updateInfo()
    {
        $data['info_id'] = input('info_id');
        $is_top = input('is_top','');

        //排序
        if(!empty(input('sort'))){
            $data['sort'] = input('sort');
        }

        //状态
        if(!empty(input('state'))){
            $data['state'] = input('state');
        }

        //置顶
        if((int)$is_top == 2){
            $data['is_top'] = 1;
        }

        if($is_top == 1){
            $data['is_top'] = 0;
        }



        //更新
        return $this->info_model -> updateInfo($data);
    }

    /**
     * 获取二级分类
     */
    public function getCategorySelect(){
        $category_id = input('category_id');

        return $this->info_model->getCategorySelect(['parent' => $category_id,'site_id'=> $this -> siteId]);
    }

    /**
     * 获取二级分类
     */
    public function getCategory(){
        $category_id = input('category_id');

        $category = $this->info_model->getCategorySelect(['parent' => $category_id,'site_id'=> $this -> siteId]);

        return json_encode($category['data']);
    }

    /**
     * 删除信息
     */
    public function deleteInfo(){

        $condition['info_id'] = input('info_id', 0);

        return $this->info_model->deleteInfo($condition);
    }


    /**
     * 添加分类
     */
    public function addCategory(){

        if (IS_AJAX) {

            $name_arr = explode("\n",input('name'));
            $parent = input('parent', 0);

            $list = array();
            foreach ( $name_arr as $key => $name) {

                //如果名称是空的不添加
                if(!$name){
                    continue;
                }


                //如果有相同的名称不添加
                if(!empty($this -> category_model -> isCategoryName($this->siteId,$parent,$name))){
                    continue;
                }

                $data = array(
                    'icon'  => input('icon', ''),
                    'name'  => $name,
                    'site_id'   => $this->siteId,
                    'parent'    => $parent,
                    'sort'  => input('sort', 0),
                    'visible' => input('visible', 1),
                    'title_label'   => input('title_label', ''),
                    'title_placeholder'   => input('title_placeholder', ''),
                    'content_label'   => input('content_label', ''),
                    'content_placeholder'   => input('content_placeholder', ''),
                    'icon_label'   => input('icon_label', ''),
                    'price_unit'   => input('price_unit', ''),
                );

                //添加操作
                $res = $this -> category_model -> addInfoCategory($data);
                $list[] = $res['data'];
            }

            return json_encode($list);
        }

        $parent = input('parent', 0);
        //获取一级分类
        $one_category_list = $this->category_model->getInfoCategoryList(['parent' => 0, 'site_id'=> $this->siteId], 'category_id, name', 'sort desc');
        return $this->fetch('info/add_category', ['one_category_list' => $one_category_list['data'],'parent' => $parent], $this->replace);
    }


    /**
     * 编辑分类
     */
    public function editCategory(){

        if (IS_AJAX) {

            $data = array(
                'icon'  => input('icon', ''),
                'name'  => input('name', ''),
                'site_id'   => $this->siteId,
                'parent'    => input('parent', 0),
                'sort'  => input('sort', 0),
                'visible' => input('visible', 0),
                'title_label'   => input('title_label', ''),
                'title_placeholder'   => input('title_placeholder', ''),
                'content_label'   => input('content_label', ''),
                'content_placeholder'   => input('content_placeholder', ''),
                'icon_label'   => input('icon_label', ''),
                'price_unit'   => input('price_unit', ''),
                'category_id'    => input('category_id', 0)
            );

            //修改操作
            $res = $this->category_model -> editInfoCategory($data);
            return $res;
        }

        $category_id = input('category_id', 0);
        //分类详情
        $category = $this->category_model->getInfoCategoryDetail($category_id, $this->siteId);
        //获取一级分类
        $one_category_list = $this->category_model->getInfoCategoryList(['parent' => 0, 'site_id'=> $this->siteId], 'category_id, name', 'sort desc');

        return $this->fetch('info/edit_category',
            [
                'category' => $category,
                'one_category_list' => $one_category_list['data'],
            ],
            $this->replace);
    }


    /**
     * 删除分类
     */
    public function deleteCategory(){
        $category_id = input('category_id');

        $Info_model = new InfoModel();

        //删除分类
        $res = $Info_model -> deleteCategory($category_id);
        return $res;
    }

    /**
     * 设置
     * @return mixed
     */
    public function config(){
        return $this->fetch('info/config', [], $this->replace);
    }

    /**
     * 分类属性列表
     * @return mixed
     */
    public function categoryAttributeList(){

        $category_id = input('category_id', 0);
        $this->assign('category_id', $category_id);
        $order = 'sort asc';
        $condition = array(
            'category_id'=> $category_id,
            'site_id' => $this->siteId
        );
        $list = $this->category_model->getInfoCategoryAttributeList($condition, '*', $order);

        $this->assign('list', $list);

        $this->assign('category_id', $category_id);

        return $this->fetch('info/category_attribute_list', [], $this->replace);
    }

    /**
     * 添加分类属性
     */
    public function addCategoryAttribute(){

        if(IS_AJAX){

            $attribute_data = array(
                'name' =>   input('attribute_name', ''),
                'input_type' => input('input_type', 0),
                'postfix' => input('postfix', ''),
                'placeholder' => input('placeholder', ''),
                'reg' => input('reg', ''),
                'unique' => input('unique', ''),
                'required' => input('required', ''),
                'search_able' => input('search_able', ''),
                'category_id' => input('category_id', ''),
                'input_args' => input('input_args', ''),
                'site_id' => $this->siteId
            );

            $res = $this->category_model->addInfoCategoryAttribute($attribute_data);
            return $res;
        }

        $category_id = input('category_id', 0);
        $this->assign('category_id', $category_id);

        //属性类型array
        $input_type_arr = $this->category_model->getInputType();
        $this->assign('input_type_arr', $input_type_arr);

        //验证正则array
        $input_regex_arr = $this->category_model->getInputRegex();
        $this->assign('input_regex_arr', $input_regex_arr);

        return $this->fetch('info/add_category_attribute', [], $this->replace);
    }

    /**
     * 编辑分类属性
     */
    public function editCategoryAttribute(){

        $attribute_id = input('attribute_id');
        $category_id = input('category_id', 0);

        if(IS_AJAX){

            $attribute_data = array(
                'name' =>   input('attribute_name', ''),
                'input_type' => input('input_type', 0),
                'postfix' => input('postfix', ''),
                'placeholder' => input('placeholder', ''),
                'reg' => input('reg', ''),
                'unique' => input('unique', ''),
                'required' => input('required', ''),
                'search_able' => input('search_able', ''),
                'category_id' => $category_id,
                'input_args' => input('input_args', ''),
                'site_id' => $this->siteId,
                'attribute_id' => $attribute_id
            );

            //编辑属性
            $res = $this->category_model->editInfoCategoryAttribute($attribute_data);
            return $res;
        }

        $this->assign('category_id', $category_id);

        //属性详情
        $attribute_find = $this->category_model->getCategoryAttribute($attribute_id);

        $this->assign('attribute_find', $attribute_find);

        //属性类型array
        $input_type_arr = $this->category_model->getInputType();
        $this->assign('input_type_arr', $input_type_arr);

        //验证正则array
        $input_regex_arr = $this->category_model->getInputRegex();
        $this->assign('input_regex_arr', $input_regex_arr);

        return $this->fetch('info/edit_category_attribute', [], $this->replace);
    }

    /**
     * 编辑分类属性排序
     */
    public function isCategoryupdateSort()
    {
        $data['sort']  = input('sort');
        $data['attribute_id'] = input('attribute_id');

        //编辑属性
        $res = $this->category_model->editInfoCategoryAttribute($data);

        return $res;
    }

    /**
     * 编辑分类属性是否搜索
     */
    public function isCategoryAttribute()
    {
        $data['screening_show']  = input('screening_show');
        $data['attribute_id'] = input('attribute_id');

        //编辑属性
        $res = $this->category_model->editInfoCategoryAttribute($data);

        return $res;
    }

    /**
     * 删除分类属性
     */
    public function delCategoryAttribute(){

        $attributeId = input('attributeId');

        //删除分类
        $res = $this->category_model -> deleteAttribute($attributeId);

        return $res;
    }

    /**
     * 分类
     * @return mixed
     */
    public function category(){
        //获取分类列表数据
        $list = $this->category_model->getCategoryData($this -> siteId);
        return $this->fetch('info/category', ['list' => $list], $this->replace);
    }

    /**
     * 分类编辑
     * @return mixed
     */
    public function categoryManage(){

        $category_id = input('category_id', 0);

        if (IS_AJAX) {

            $data = array(
                'icon'  => input('icon', ''),
                'name'  => input('name', ''),
                'site_id'   => $this->siteId,
                'parent'    => input('parent', 0),
                'sort'  => input('sort', 0),
                'visible' => input('visible', 0),
                'title_label'   => input('title_label', ''),
                'title_placeholder'   => input('title_placeholder', ''),
                'content_label'   => input('content_label', ''),
                'content_placeholder'   => input('content_placeholder', ''),
                'icon_label'   => input('icon_label', ''),
                'price_unit'   => input('price_unit', ''),
                'category_id'    => $category_id
            );

            //修改操作
            $res = $this->category_model -> editInfoCategory($data);
            return $res;
        }

        //分类详情
        $category = $this->category_model->getInfoCategoryDetail($category_id, $this->siteId);

        //获取一级分类 'category' => $category,'one_category_list' => $one_category_list
        $p_category = $this->category_model->getInfoCategoryDetail($category['parent'], $this->siteId);

        if(!empty($p_category['name'])){
            $category['pname_name'] = $p_category['name'].'/'.$category['name'];
        }

        //获取属性列表信息
        $attributeList = $this->category_model->getInfoCategoryAttributeList(array('category_id'=> $category_id, 'site_id' => $this->siteId), '*', 'sort asc');

        //属性总数
        $category['attribute_count'] = count($attributeList['data']);

        //信息总数
        $category['category_info_count'] = $this -> info_model -> getCategoryInfoCount(array('category_id'=> $category_id, 'site_id' => $this->siteId));

        //属性类型array
        $input_type_arr = $this->category_model->getInputType();

        //验证正则array
        $input_regex_arr = $this->category_model->getInputRegex();

        $this->assign('input_type_arr', $input_type_arr);

        $this->assign('input_regex_arr', $input_regex_arr);

        $this->assign('list', $attributeList);

        $this->assign('category_id', $category_id);

        return $this->fetch('info/category_manage', ['category' => $category ,'p_category' => $p_category], $this->replace);
    }

    /**
     * 获取属性详情
     */
    public function getAttributeDetails(){

        $attribute_id = input('attribute_id');

        //属性详情
        $attribute_find = $this->category_model->getCategoryAttribute($attribute_id);

        return json_encode($attribute_find);
    }

    /**
     * 修改属性值
     */
    public function editAndAddAttribute(){

        if(IS_AJAX){

            $attribute_data = array(
                'name' =>   input('attribute_name', ''),
                'input_type' => input('input_type', 0),
                'postfix' => input('postfix', ''),
                'placeholder' => input('placeholder', ''),
                'reg' => input('reg', ''),
                'unique' => input('unique', ''),
                'required' => input('required', ''),
                'search_able' => input('search_able', ''),
                'category_id' => input('category_id', ''),
                'input_args' => input('input_args', ''),
                'site_id' => $this->siteId,
                'attribute_id' => input('attribute_id', '')
            );

            if(input('method') == 'add'){
                //添加属性
                $res = $this->category_model->addInfoCategoryAttribute($attribute_data);
            }else{
                $attribute_data['attribute_id'] = input('attribute_id', '');

                //编辑属性
                $res = $this->category_model->editInfoCategoryAttribute($attribute_data);
            }

            return $res;
        }
    }

    /**
     * 获取省、市
     */
    public function getProvinces()
    {
        $province_id = input('province_id');

        //获取当前省的市
        $city = $this ->address -> getProvinces($province_id);

        return json_encode($city);
    }

    /**
     * 获取二维码弹框
     */
    public function promote()
    {
        $qr_code = input("qr_code", '');
        $info_id = input("info_id", '');

        $info_url = addon_url('sns://wap/category/info'); //获取二维码访问地址
        $info_url .= '?info_id='.$info_id;

        $this->assign("qr_code", $qr_code);

        $this->assign("info_url", $info_url);

        return $this->fetch('info/promote');
    }
	/**
	 * 自定义底部导航
	 */
	public function bottomNavDesign()
	{
		$bottom_nav = new BottomNav();
		if (IS_AJAX) {
			
			$value = input("value", "");
			$addon_name = input("addon_name", "");
			$res = $bottom_nav->setBottomNavConfig($value, request()->siteid(), $addon_name);
			return $res;
		} else {
			
			$addon_name = input("addon_name", request()->addon());
			$this->assign("addon_name", $addon_name);
			$bottom_nav_info = $bottom_nav->getBottomNavConfig(request()->siteid(), $addon_name);
			$this->assign("bottom_nav_info", $bottom_nav_info['data']);
			
			$this->replace['DIYVIEW_JS'] = __ROOT__ . '/addon/system/DiyView/sitehome/view/public/js';
			
			return $this->fetch('info/bottom_nav_design', [], $this->replace);
		}
		
	}
}