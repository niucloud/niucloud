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

namespace addon\module\Article\sitehome\controller;

use app\common\controller\BaseSiteHome;
use addon\module\Article\common\model\Article as ArticleModel;

class Article extends BaseSiteHome
{
	
	public $article_model;
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ARTICLE_CSS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/css',
			'ARTICLE_JS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/js',
			'ARTICLE_IMG' => __ROOT__ . '/addon/module/Article/sitehome/view/public/img',
		];
		$this->article_model = new ArticleModel();
		
	}
	
	/**
	 * 文章列表
	 * @return \think\mixed
	 */
	public function articleList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition['site_id'] = $this->siteId;
			$order = 'sort asc';
			$list = $this->article_model->getArticlePageList($condition, $page, $limit, $order);
			return $list;
		}
		return $this->fetch('article/article_list', [], $this->replace);
	}
	
	/**
	 * 添加文章
	 * @return \think\mixed
	 */
	public function addArticle()
	{
		if (IS_AJAX) {
			
			$title = input('title', "");
			$category_id = input('category_id', 0);
			$short_title = input('short_title', "");
			$source = input('source', "");
			$url = input('url', "");
			$author = input('author', "");
			$summary = input('summary', "");
			$content = input('content', "");
			$image = input('title_img', "");
			$keyword = input('keyword', "");
			$click = input('click', 0);
			$sort = input('sort', 0);
			$commend_flag = input('commend_flag', 0);
			$comment_flag = input('comment_flag', 0);
			$attachment_path = input('attachment_path', "");
			$tag = input('tag', "");
			$comment_count = input('comment_count', 0);
			$share_count = input('share_count', 0);
			
			$data = array(
				'site_id' => $this->siteId,
				'title' => $title,
				'category_id' => $category_id,
				'short_title' => $short_title,
				'source' => $source,
				'url' => $url,
				'author' => $author,
				'summary' => $summary,
				'content' => $content,
				'image' => $image,
				'keyword' => $keyword,
				'click' => $click,
				'sort' => $sort,
				'commend_flag' => $commend_flag,
				'comment_flag' => $comment_flag,
				'attachment_path' => $attachment_path,
				'tag' => $tag,
				'comment_count' => $comment_count,
				'share_count' => $share_count,
				'create_time' => time()
			);
			$res = $this->article_model->addArticle($data);
			return $res;
		}
		$list_tree = $this->article_model->getArticleCategoryTree($this->siteId);
		$this->assign('list_tree', $list_tree['data']);
		
		return $this->fetch('article/add_article', [], $this->replace);
	}
	
	/**
	 * 编辑文章
	 * @return \think\mixed
	 */
	public function editArticle()
	{
		if (IS_AJAX) {
			$article_id = input('article_id', 0);
			$title = input('title', "");
			$category_id = input('category_id', 0);
			$short_title = input('short_title', "");
			$source = input('source', "");
			$url = input('url', "");
			$author = input('author', "");
			$summary = input('summary', "");
			$content = input('content', "");
			$image = input('title_img', "");
			$keyword = input('keyword', "");
			$click = input('click', 0);
			$sort = input('sort', 0);
			$commend_flag = input('commend_flag', 0);
			$comment_flag = input('comment_flag', 0);
			$attachment_path = input('attachment_path', "");
			$tag = input('tag', "");
			$comment_count = input('comment_count', 0);
			$share_count = input('share_count', 0);
			
			$data = array(
				'site_id' => $this->siteId,
				'article_id' => $article_id,
				'title' => $title,
				'category_id' => $category_id,
				'short_title' => $short_title,
				'source' => $source,
				'url' => $url,
				'author' => $author,
				'summary' => $summary,
				'content' => $content,
				'image' => $image,
				'keyword' => $keyword,
				'click' => $click,
				'sort' => $sort,
				'commend_flag' => $commend_flag,
				'comment_flag' => $comment_flag,
				'attachment_path' => $attachment_path,
				'tag' => $tag,
				'comment_count' => $comment_count,
				'share_count' => $share_count,
				'modify_time' => time()
			);
			$res = $this->article_model->editArticle($data);
			return $res;
		}
		$article_id = input('article_id', "");
		$article_info = $this->article_model->getArticleInfo([ 'article_id' => $article_id ]);
		$this->assign('article_info', $article_info['data']);
		$list_tree = $this->article_model->getArticleCategoryTree($this->siteId);
		$this->assign('list_tree', $list_tree['data']);
		return $this->fetch('article/edit_article', [], $this->replace);
	}
	
	/**
	 * 删除文章
	 */
	public function deleteArticle()
	{
		if (IS_AJAX) {
			$article_id = input('article_id', "");
			$condition = array(
				'article_id' => $article_id
			);
			$res = $this->article_model->deleteArticle($condition);
			return $res;
		}
	}
	
	/**
	 * 修改排序
	 */
	public function sortArticle()
	{
		if (IS_AJAX) {
			$article_id = input('article_id', "");
			$sort = input('sort', "");
			$data = array(
				'article_id' => $article_id,
				'sort' => $sort
			);
			$res = $this->article_model->editArticle($data);
			return $res;
		}
	}
	
	/**
	 * 文章分类列表
	 */
	public function articleCategoryList()
	{
		$article_model = new ArticleModel();
		if (IS_AJAX) {

			$category_id = input('category_id', -1);
			$site_id = $this->siteId;
			$condition['site_id'] = $site_id;
			$condition['p_id'] = $category_id;

			$list = $article_model->getArticleCategoryList($condition, '*', 'sort asc');
			foreach ($list['data'] as $key => $val) {
				$where['site_id'] = $site_id;
				$where['p_id'] = $val['category_id'];
				$list['data'][ $key ]['child_num'] = $article_model->getArticleCategoryCount($where)['data'];
			}
			$res['code'] = $list['code'];
			$res['message'] = $list['message'];
			$res['data'] = [
				'count' => !empty($list['data']) ? count($list['data']) : 0,
				'list' => $list['data']
			];
			return $res;
		} else {
			return $this->fetch('article/category_list', [], $this->replace);
		}
	}

    /**
     * 文章分类
     * @return \multitype
     */
	public function getArticleCategoryList(){
        $article_model = new ArticleModel();
        $list = $article_model->getArticleCategoryList([], '*', 'sort asc');
        return $list;
    }
	/**
	 * 文章分类添加
	 */
	public function categoryAdd()
	{
		$article_model = new ArticleModel();
		$category_name = input("category_name", "");
		$sort = input("sort", 0);
		$p_id = input("p_id", 0);
		$data = array(
			"category_name" => $category_name,
			"sort" => $sort,
			"p_id" => $p_id,
			"site_id" => $this->siteId
		);
		
		$res = $article_model->addArticleCategory($data);
		return $res;
	}
	
	/**
	 * 文章分类修改
	 */
	public function categoryEdit()
	{
		$article_model = new ArticleModel();
		$category_id = input("category_id", 0);
		$category_name = input("category_name", "");
		$sort = input("sort", 0);
		$p_id = input("p_id", 0);
		$data = array(
			"category_name" => $category_name,
			"sort" => $sort,
			"p_id" => $p_id,
			"site_id" => $this->siteId,
			"category_id" => $category_id
		);
		$res = $article_model->editArticleCategory($data);
		return $res;
	}
	
	/**
	 * 文章分类删除
	 */
	public function categoryDelete()
	{
		$article_model = new ArticleModel();
		$category_ids = input('category_ids', '');
		$condition['category_id'] = [ 'in', $category_ids ];
		
		$res = $article_model->deleteArticleCategory($condition);
		return $res;
	}
	
	/**
	 * 文章分类详情
	 */
	public function categoryInfo()
	{
		$article_model = new ArticleModel();
		$category_id = input('category_id', 0);
		$condition['category_id'] = $category_id;
		$res = $article_model->getArticleCategoryInfo($condition);
		
		return $res;
	}
	
	/**
	 * 统计个数
	 */
	public function categoryCount()
	{
		$article_model = new ArticleModel();
		$category_id = input('category_id', '');
		$condition['p_id'] = $category_id;
		$res = $article_model->getArticleCategoryCount($condition);
		
		return success($res['data']);
	}
	
	/**
	 * 修改排序
	 */
	public function sortArticleCategory()
	{
		if (IS_AJAX) {
			$category_id = input('category_id', "");
			$sort = input('sort', "");
			$res = $this->article_model->editArticleCategory([ 'sort' => $sort ], [ 'category_id' => $category_id ]);
			return $res;
		}
	}
	
	public function loadStyle($StyleObj)
	{
	
	}
	
}