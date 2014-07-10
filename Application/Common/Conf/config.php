<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Admin','Install'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'P9$-N*{;a0l|/=CTxm~:6p!yQI}d,t>oOE(w_[gB', //默认数据加密KEY

	
    /* 调试配置 */
    'SHOW_PAGE_TRACE' => true,

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'webshop', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ws_', // 数据库表前缀
    
    /*PICOOC主从配置*/
    'DB_PICOOC_MAST' => 'mysql://root:@localhost:3306/picooc',
    'DB_PICOOC_SLAVE' => array(
	 	1 => 'mysql://root:@localhost:3306/picooc',
	 	2 => 'mysql://root:@localhost:3306/picooc'
	),
	'SMS_DRIVER' => 'suda_sms',
	/*短信通道配置*/
	'suda_sms'=>array(
		'check_inteval'=>0,//检查余额的间隔
		'notice_no' => '13501327047',
		'notice_num' => 500,
		'sn' => 'SDK-KEY-010-00095',
		'pwd' => 'c9@dcff@',
		'url' => 'http://sdk2.sudas.cn:8060/z_mdsmssend.aspx',
		'balance_url' => 'http://sdk2.sudas.cn:8060/z_balance.aspx',
		'content' => '%s（PICOOC验证码），为了保护您的帐号安全，验证短信请勿转发给其他人【缤刻普锐】',
		'balance_content' => '短信验证码余量已到警戒线，请尽快充值【缤刻普锐】'
	),
	
	'MEMCACHE_HOSTS' => array(
			array("ip" => "192.168.0.183", "port" => 11211)
	),
    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
);
