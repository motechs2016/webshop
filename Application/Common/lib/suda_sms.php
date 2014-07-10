<?php
class suda_sms{
	
	
	/**
	 * 
	 * @param unknown $config 配置信息
	 * @param unknown $cache 缓存处理
	 * @return boolean
	 */
	function __construct($config){
		if(!$config){
			return  false;
		}
		
		$this->config = $config;
		$this->sn = $config['sn'];
		$this->pwd = $config['pwd'];
		$this->balancurl = $config['balance_url'];
		$this->url = $config['url'];
	}
	
	
	/**
	 * 发送短信
	 *
	 * @param unknown_type $phone
	 * @param unknown_type $code
	 * @return unknown
	 */
	function send($phone=0, $content)
	{
		if(empty($phone)||empty($content)){
			return false;
		}
		$data = array
		(
		'sn' => $this->sn,
		'pwd' => strtoupper(md5($this->sn . $this->pwd)),
		'mobile' =>$phone,
		'content' => iconv('UTF-8', 'GB2312', $content),
		);
		$re= $this->post_sms($this->url, $data);			//POST方式提交
		return $re;
	}
	
	/**
	 * 查询短信余额
	 * @return Ambigous <number, string>
	 */
	function check_sms_balance()
	{
		global $config;
		$res = file_get_contents($this->balancurl . '?sn='.$this->sn.'&pwd='.$this->pwd);
		return $res ? $res : 0;
	}
	
	private function post_sms($url, $data='')
	{
		$row = parse_url($url);
		$host = $row['host'];
		$port = $row['port'] ? $row['port'] : 80;
		$file = $row['path'];
		$post = '';
		while (list($k,$v) = each($data))
		{
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
		}
		$post = substr( $post , 0 , -1 );
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp)
		{
			return "$errstr ($errno)\n";
		}else{
			$receive = '';
			$out = "POST $file HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;
			fwrite($fp, $out);
			while (!feof($fp)) {
				$receive .= fgets($fp, 128);
			}
			fclose($fp);
			$receive = explode("\r\n\r\n",$receive);
			unset($receive[0]);
			return implode("",$receive);
		}
	}
}