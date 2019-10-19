<?php
namespace addon\system\OAuthLoginQQ\common\sdk;

class QQLogin{
    
	/**
	 * 获取requestCode的api接口
	 * @var string
	 */
	protected $GetRequestCodeURL = 'https://graph.qq.com/oauth2.0/authorize';
	
	/**
	 * 获取access_token的api接口
	 */
	protected $GetAccessTokenURL = 'https://graph.qq.com/oauth2.0/token';
	
	/**
	 * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
	 * @var srting
	 */
	protected $Authorize = 'scope=get_user_info,add_share';

	/**
	 * API根路径
	 * @var string
	 */
	protected $ApiBase = 'https://graph.qq.com/';
	
	protected $Type = 'QQ';
	
	
	/**
	 * 申请应用时分配的app_key
	 * @var string
	 */
	protected $AppKey = '';
	
	/**
	 * 申请应用时分配的 app_secret
	 * @var string
	 */
	protected $AppSecret = '';
	
	/**
	 * 授权类型 response_type 目前只能为code
	 * @var string
	 */
	protected $ResponseType = 'code';
	
	/**
	 * grant_type 目前只能为 authorization_code
	 * @var string
	 */
	protected $GrantType = 'authorization_code';
	
	/**
	 * 回调页面URL  可以通过配置文件配置
	 * @var string
	 */
	protected $Callback = '';
	
	/**
	 * 授权后获取到的TOKEN信息
	 * @var array
	 */
	protected $Token = null;
	
	
	/**
	 * 构造方法，配置应用信息
	 * @param array $token
	 */
	public function __construct($app_key, $app_secret, $call_back = '', $token = null){
	    //设置SDK类型
	    if(empty($app_key) || empty($app_secret)){
	        throw new \Exception('请配置您申请的APP_KEY和APP_SECRET');
	    } else {
	        $this->AppKey    = $app_key;
	        $this->AppSecret = $app_secret;
	        $this->Callback  = $call_back;
	        $this->Token     = $token; //设置获取到的TOKEN
	    }
	}
	
	/**
	 * 请求code
	 */
	public function getRequestCodeURL(){
	    //Oauth 标准参数
	    $params = array(
	        'client_id'     => $this->AppKey,
	        'redirect_uri'  => $this->Callback,
	        'response_type' => $this->ResponseType,
	    );
	
	    //获取额外参数
	    if($this->Authorize){
	        parse_str($this->Authorize, $_param);
	        if(is_array($_param)){
	            $params = array_merge($params, $_param);
	        } else {
	            throw new \Exception('AUTHORIZE配置不正确！');
	        }
	    }
	    return $this->GetRequestCodeURL . '?' . http_build_query($params);
	}
	
	/**
	 * 获取access_token
	 * @param string $code 上一步请求到的code
	 */
	public function getAccessToken($code, $extend = null){
	    $params = array(
	        'client_id'     => $this->AppKey,
	        'client_secret' => $this->AppSecret,
	        'grant_type'    => $this->GrantType,
	        'code'          => $code,
	        'redirect_uri'  => $this->Callback,
	    );

	    $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
	    $this->Token = $this->parseToken($data, $extend);
	    return $this->Token;
	}
	
	/**
	 * 合并默认参数和额外参数
	 * @param array $params  默认参数
	 * @param array/string $param 额外参数
	 * @return array:
	 */
	protected function param($params, $param){
	    if(is_string($param))
	        parse_str($param, $param);
	    return array_merge($params, $param);
	}
	
	/**
	 * 获取指定API请求的URL
	 * @param  string $api API名称
	 * @param  string $fix api后缀
	 * @return string      请求的完整URL
	 */
	protected function url($api, $fix = ''){
	    return $this->ApiBase . $api . $fix;
	}
	
	/**
	 * 发送HTTP请求方法，目前只支持CURL发送请求
	 * @param  string $url    请求URL
	 * @param  array  $params 请求参数
	 * @param  string $method 请求方法GET/POST
	 * @return array  $data   响应数据
	 */
	protected function http($url, $params, $method = 'GET', $header = array(), $multi = false){
	    $opts = array(
	        CURLOPT_TIMEOUT        => 30,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_HTTPHEADER     => $header
	    );
	
	    /* 根据请求类型设置特定参数 */
	    switch(strtoupper($method)){
	        case 'GET':
	            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
	            break;
	        case 'POST':
	            //判断是否传输文件
	            $params = $multi ? $params : http_build_query($params);
	            $opts[CURLOPT_URL] = $url;
	            $opts[CURLOPT_POST] = 1;
	            $opts[CURLOPT_POSTFIELDS] = $params;
	            break;
	        default:
	            throw new \Exception('不支持的请求方式！');
	    }
	
	    /* 初始化并执行curl请求 */
	    $ch = curl_init();
	    curl_setopt_array($ch, $opts);
	    $data  = curl_exec($ch);
	    $error = curl_error($ch);
	    curl_close($ch);
	    if($error) throw new \Exception('请求发生错误：' . $error);
	    return  $data;
	}
	

	/**
	 * 组装接口调用参数 并调用接口
	 * @param  string $api    微博API
	 * @param  string $param  调用API的额外参数
	 * @param  string $method HTTP请求方法 默认为GET
	 * @return json
	 */
	public function call($api, $param = '', $method = 'GET', $multi = false){
		/* 腾讯QQ调用公共参数 */
		$params = array(
			'oauth_consumer_key' => $this->AppKey,
			'access_token'       => $this->Token['access_token'],
			'openid'             => $this->openid(),
			'format'             => 'json'
		);
		
		$data = $this->http($this->url($api), $this->param($params, $param), $method);
		return json_decode($data, true);
	}
	
	/**
	 * 解析access_token方法请求后的返回值 
	 * @param string $result 获取access_token的方法的返回值
	 */
	protected function parseToken($result, $extend){
		parse_str($result, $data);
		if($data['access_token'] && $data['expires_in']){
			$this->Token    = $data;
			$data['openid'] = $this->openid();
			return $data;
		} else
			throw new \Exception("获取腾讯QQ ACCESS_TOKEN 出错：{$result}");
	}
	
	/**
	 * 获取当前授权应用的openid
	 * @return string
	 */
	public function openid(){
		$data = $this->Token;
		if(isset($data['openid']))
			return $data['openid'];
		elseif($data['access_token']){
			$data = $this->http($this->url('oauth2.0/me'), array('access_token' => $data['access_token']));
			$data = json_decode(trim(substr($data, 9), " );\n"), true);
			if(isset($data['openid']))
				return $data['openid'];
			else
				throw new \Exception("获取用户openid出错：{$data['error_description']}");
		} else {
			throw new \Exception('没有获取到openid！');
		}
	}
}