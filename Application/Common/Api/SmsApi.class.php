<?php
namespace Common\Api;
class SmsApi{
	private static $sms ;
	
	public static function init(){
		if(!isset(self::$sms)){
			self::$sms = new Sms();
		}
	}
	
	public static function send_code($phone_no){
		return self::$sms->send_code($phone_no);
	}
}


class Sms {
	private $sms;
	private $config;
	function __construct(){
		$smstype = C('SMS_DRIVER');
		$this->config = C($smstype);
		import("Common.lib.$smstype",'','.php');
		$this->sms = new $smstype($this->config);
		$this->cache = S(array('type'=>'Memcache','prefix'=>$smstype));
		@$this->check_balance();
	}
	
	
	/**
	 * 检查短信剩余量
	 */
	private function check_balance(){
		$lastcheck = intval($this->cache->lastcheck);
		$inteval = time()-$lastcheck;
		if($inteval > $this->config['check_inteval']){
			$res = $this->sms->check_sms_balance();
			if(is_numeric($res) && $res< $this->config['notice_num']){
				$this->sms->send($this->config['notice_no'],$this->config['balance_content']);
			}
			$this->cache->lastcheck = time();
		}
	}
	
	/**
	 * 发短信验证码
	 * @param unknown $phone_no
	 * @return boolean
	 */
	function send_code($phone_no){
		if(!preg_match("/^[1][3-8]\d{9}$/", $phone_no)){
			return false;
		}
		$code = rand(1000, 9999);
		$content = $this->config['content'];
		$content = sprintf($content,$code);
		$res = $this->sms->send($phone_no,$content);
		return $res?$code:false;
	}
}