<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model;
/**
 * 会员模型
 */
class PicoocMemberModel extends Model{
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = ' ';

	protected $tableName   = 'user';

	/* 用户模型自动验证 */
	protected $_validate = array(
		array('phone_no','require',-4),
		/* 验证邮箱 */
		array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
		array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
		array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
		array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用

		/* 验证手机号码 */
		
		array('phone_no', '/^[1][3-8]\d{9}$/', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
		array('phone_no', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
		array('phone_no', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
	);

	
	
	/**
	 * 检测用户名是不是被禁止注册
	 * @param  string $username 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMember($username){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 检测邮箱是不是被禁止注册
	 * @param  string $email 邮箱
	 * @return boolean       ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyEmail($email){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 检测手机是不是被禁止注册
	 * @param  string $mobile 手机
	 * @return boolean        ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMobile($mobile){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 根据配置指定用户状态
	 * @return integer 用户状态
	 */
	protected function getStatus(){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 注册一个新用户
	 * @param  string $username 用户名
	 * @param  string $password 用户密码
	 * @param  string $email    用户邮箱
	 * @param  string $mobile   用户手机号码
	 * @return integer          注册成功-用户信息，注册失败-错误编号
	 */
	public function register( $username,$password, $email,$mobile=''){
		$this->get_picooc_db(1);
		$date = date("Y-m-d H:i:s",NOW_TIME );
		$data = array(
			'md5_password' => md5($password),
			
			'phone_no'   => $mobile,
			'password' => $password,
			'server_time' => $date
		);
		if(!empty($email)){
			$data['email'] = $email;
		}
		/* 添加用户 */
		if($this->create($data)){
			$uid = $this->add();
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	/**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login($username, $password, $type = 1){
		if(strpos($username,'@')===false){
			$type = 3;
		}else{
			$type = 2;
		}
		$map = array();
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['phone_no'] = $username;
				break;
			case 4:
				$map['id'] = $username;
				break;
			default:
				return 0; //参数错误
		}
		if(!($username&&$password)){
			return 0;
		}
		$this->get_picooc_db(1);
		/* 获取用户数据 */
		$user = $this->where($map)->find();
		if(is_array($user)){
			/* 验证用户密码 */
			if($user['md5_password']){
				if(md5($password) === $user['md5_password']){
					$this->updateLogin($user['id']); //更新用户登录信息
					return $user['id']; //登录成功，返回用户ID
				} else {
					return -2; //密码错误
				}
			}else{
				if($password === $user['password']){
					$this->updateLogin($user['id']); //更新用户登录信息
					return $user['id']; //登录成功，返回用户ID
				}else{
					return -2;
				}
			}
			
		} else {
			return -1; //用户不存在或被禁用
		}
	}
	
	/*
	 * 获取picooc的数据库连接
	 */
	public function get_picooc_db($mast=0){
		if($mast){
			$this->db(99,'DB_PICOOC_MAST',true);
		}else{
			$slaves = C('DB_PICOOC_SLAVE');
			$count = count($slaves);
			if(!$count) return 0;
			$r = $count>1?rand(1, $count):1;
			$this->db($r,$slaves[$r],true);
		}
	}
	
	/**
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info($uid, $is_username = false){
		$map = array();
		if($is_username){ //通过用户名获取
			if(strpos($uid,'@')!==false){
				$map['email'] = $uid;
			}else{
				$map['phone_no'] = $uid;
			}
			
		} else {
			$map['id'] = $uid;
		}
		$this->get_picooc_db();
		$user = $this->where($map)->field('id,phone_no,email')->find();
		if(is_array($user)){
			if($user['email']){
				$user['username'] = $user['email'];
			}
			if($user['phone_no']){
				$user['username'] = $user['phone_no'];
			}
			return array($user['id'], $user['username'], $user['email'], $user['phone_no']);
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * 检测用户信息
	 * @param  string  $field  用户名
	 * @param  integer $type   用户名类型 1-用户名，2-用户邮箱，3-用户电话
	 * @return integer         错误编号
	 */
	public function checkField($field, $type = 1){
		$data = array();
		switch ($type) {
			case 1:
				return 1;
				break;
			case 2:
				$data['email'] = $field;
				break;
			case 3:
				$data['phone_no'] = $field;
				break;
			default:
				return 0; //参数错误
		}
		$this->get_picooc_db();
		return $this->create($data) ? 1 : $this->getError();
	}

	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid){
		
	}

	/**
	 * 更新用户信息
	 * @param int $uid 用户id
	 * @param string $password 密码，用来验证
	 * @param array $data 修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateUserFields($uid, $password, $data){
		if(empty($uid) || empty($password) || empty($data)){
			$this->error = '参数错误！';
			return false;
		}

		//更新前检查用户密码
		if(!$this->verifyUser($uid, $password)){
			$this->error = '验证出错：密码不正确！';
			return false;
		}
		$this->get_picooc_db(1);
		//更新用户信息
		$data = $this->create($data);
		if($data){
			return $this->where(array('id'=>$uid))->save($data);
		}
		return false;
	}

	/**
	 * 验证用户密码
	 * @param int $uid 用户id
	 * @param string $password_in 密码
	 * @return true 验证成功，false 验证失败
	 * @author huajie <banhuajie@163.com>
	 */
	protected function verifyUser($uid, $password_in){
		$this->get_picooc_db();
		$user = $this->find($uid);
		if($user['md5_password']){
			$password = $user['md5_password'];
		}else{
			$password = md5($user['password']);
		}
		
		
		if(md5($password_in) === $password){
			return true;
		}
		return false;
	}

}
