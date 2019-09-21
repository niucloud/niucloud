<?php
namespace addon\system\PayWechat\common\sdk\WxPayData;
/**
 *
 * 提交退款输入对象
 * @author widyhu
 *
 */
class WxPayTransfers extends WxPayDataBase
{
    /**
     * 设置公众号appid
     * @param unknown $value
     */
    public function SetMch_appid($value){
        $this->values['mch_appid'] = $value;
    }
    /**
     * 判断公众号appid是否存在
     * @return true 或 false
     **/
    public function IsMch_appidSet()
    {
        return array_key_exists('mch_appid', $this->values);
    }
    /**
     * 设置商户号（需要与公众号对应）
     * @param unknown $value
     */
    public function SetMchid($value){
        $this->values['mchid'] = $value;
    }
    /**
     * 判断商户号是否存在
     * @return true 或 false
     **/
    public function IsMchidSet()
    {
        return array_key_exists('mchid', $this->values);
    }
    /**
     * 设置 商户订单号
     * @param unknown $value
     */
    public function SetPartner_trade_no($value){
        $this->values['partner_trade_no'] = $value;
    }
    /**
     * 判断商户订单号是否存在
     * @return true 或 false
     **/
    public function IsPartner_trade_noSet()
    {
        return array_key_exists('partner_trade_no', $this->values);
    }
    /**
     * 校验用户姓名选项  NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
     * @param unknown $value
     */
    public function SetCheck_name($value){
        $this->values['check_name'] = $value;
    }
    /**
     * 判断    校验用户姓名选项   是否存在
     * @return true 或 false
     **/
    public function IsCheck_nameSet()
    {
        return array_key_exists('check_name', $this->values);
    }
    /**
     * 收款用户姓名   如果check_name设置为FORCE_CHECK，则必填用户真实姓名
     * @param unknown $value
     */
    public function SetRe_user_name($value){
        $this->values['re_user_name'] = $value;
    }
    /**
     * 判断    收款用户姓名   是否存在
     * @return true 或 false
     **/
    public function IsRe_user_nameSet()
    {
        return array_key_exists('re_user_name', $this->values);
    }
    /**
     * 金额    企业付款金额，单位为分
     * @param unknown $value
     */
    public function SetAmount($value){
        $this->values['amount'] = $value;
    }
    /**
     * 判断    金额   是否存在
     * @return true 或 false
     **/
    public function IsAmountSet()
    {
        return array_key_exists('amount', $this->values);
    }
    /**
     * 企业付款描述信息
     * @param unknown $value
     */
    public function SetDesc($value){
        $this->values['desc'] = $value;
    }
    /**
     * 判断    金额   是否存在
     * @return true 或 false
     **/
    public function IsDescSet()
    {
        return array_key_exists('desc', $this->values);
    }
    /**
     * Ip地址
     * @param unknown $value
     */
    public function SetSpbill_create_ip($value){
        $this->values['spbill_create_ip'] = $value;
    }
    /**
     * 判断    Ip地址   是否存在
     * @return true 或 false
     **/
    public function IsSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    /**
     * 用户openid   商户appid下，某用户的openid
     * @param unknown $value
     */
    public function SetOpenid($value){
        $this->values['openid'] = $value;
    }
    /**
     * 判断    Ip地址   是否存在
     * @return true 或 false
     **/
    public function IsOpenidSet()
    {
        return array_key_exists('openid', $this->values);
    }
    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return 值
     **/
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    /**
     * 设置微信支付分配的终端设备号，与下单一致
     * @param string $value
     **/
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号，与下单一致的值
     * @return 值
     **/
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    /**
     * 判断微信支付分配的终端设备号，与下单一致是否存在
     * @return true 或 false
     **/
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml()
    {
        if(!is_array($this->values) || count($this->values) <= 0)
        {
            throw new WxPayException("数组数据异常！");
        }
         
        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            $xml.="<".$key.">".$val."</".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
}