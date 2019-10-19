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

namespace addon\module\Article\wap\controller;

use app\common\controller\BaseSite;
use addon\module\Article\common\model\Reward as RewardModel;

/**
 * 赞赏 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Reward extends BaseSite
{
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
		$this->replace = [
			'ADDON_NC_WAP_ARTICLE_CSS' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/css',
			'ADDON_NC_WAP_ARTICLE_JS' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/js',
			'ADDON_NC_WAP_ARTICLE_IMG' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/img',
		];
    }

    /**
	 * 赞赏
	 * 创建时间：2018年8月31日17:22:45
	 */
	public function reward()
	{
        $this->assign("title", "赞赏");
        return $this->fetch($this->wap_style . '/reward/reward', [], $this->replace);
	}

    /**
     * 去赞赏
     */
	public function doReward(){
        $out_trade_no = input('out_trade_no', '');
        $pay_type = input('pay_type', '');

        $reward_detail_result = api("Article.Reward.getRewardInfo",['out_trade_no'=>$out_trade_no]);
        $reward_detail = $reward_detail_result["data"];
        //判断赞赏是否已支付
        if($reward_detail["status"] == 1){
            $this->redirect(addon_url("Article://wap/reward/rewardSuccess", ['out_trade_no'=>$out_trade_no]));
        }

        $article_detail_result = api("Article.Article.getArticleDetail",['article_id'=>$reward_detail["article_id"]]);
        $article_detail = $article_detail_result["data"];
        $remark = "文章".$article_detail["title"]."的赞赏";
        $pay_money = $reward_detail["money"];
        if($pay_type == 2){
            //账户抵扣的支付业务
            $res = api("Article.Reward.rewardPay",[ 'out_trade_no'=>$out_trade_no,"access_token" => $this->access_token]);
            if($res["code"] == 0){
                $this->redirect(addon_url("Article://wap/reward/rewardSuccess", ['out_trade_no'=>$out_trade_no]));
            }
        }else{
            $notify_url = addon_url("Article://wap/reward/rewardPay", ['out_trade_no'=>$out_trade_no]);
            $param = array(
                'site_id'=> $this->siteId,
                'pay_body' => "文章".$article_detail["title"]."的赞赏",
                'pay_detail' => "文章".$article_detail["title"]."的赞赏",
                'out_trade_no' => $out_trade_no,
                'pay_scene' => "wap",
                "pay_money" => $reward_detail["money"],
                "notify_url" => $notify_url,//支付异步回调网址
                "return_url" => addon_url("Article://wap/reward/rewardSuccess", ['out_trade_no'=>$out_trade_no]),//支付同步回调网址
            );
            $pay_result = hook("payment",["pay_data" => $param]);//引导支付
            $this->redirect($pay_result["data"]);
        }
    }

    /**
     * 打赏成功
     */
    public function rewardSuccess(){
        $out_trade_no = input('out_trade_no', '');
        $reward_info_result = api("Article.Reward.getRewardInfo",["out_trade_no" => $out_trade_no]);

        $reward_info = $reward_info_result["data"];
        if($reward_info["status"] == 1){
            dump("支付成功");
        }else{
            dump("支付失败");
        }
    }
    
    /**
     * 异步回调支付支付赞赏
     * @param $params
     */
    public function rewardPay(){
        $out_trade_no = input('out_trade_no', '');
        $reward_model = new RewardModel();
        $condition = [];
        $condition['out_trade_no'] = $out_trade_no;
        $pay_info = hook("validatePay", ["out_trade_no" => $out_trade_no], [], true);
        if(!empty($pay_info["data"])){
            $data = array(
                "status" => 1,
                "pay_time" => time(),
            );
            $res = $reward_model->editReward($data, $condition);
            return $res;
        }
    }
    
    /**
     * 我的评论
     * 创建时间：2018年8月31日17:22:45
     */
    public function myReward()
    {
        $this->assign("title", "我的赞赏");
        return $this->fetch($this->wap_style . '/reward/my_reward', [], $this->replace);
    }
  
}