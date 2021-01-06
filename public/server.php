<?php
//禁用错误报告
ini_set("display_errors", "On");
error_reporting(0);
error_reporting(E_ALL ^ E_NOTICE);
########################
define('APPLICATION_PATH', dirname(__FILE__) . "/..");
$config = parse_ini_file("../conf/application.ini");

$http = new Swoole\Http\Server($config["bindIp"], $config["serverPort"]);
$http->set([
	//'ssl_cert_file' =>   $config["sslCertFile"],
	//'ssl_key_file' =>   $config["sslKeyFile"],
	//'open_http2_protocol' => true,
	//	'reactor_num' => intval($config["reactorNum"]),
	//'worker_num' => intval($config["workerNum"]),
	'daemonize' => intval($config["serverDaemonize"]), //是否后台运行
	//'tcp_fastopen' => true,
	//'user' => $config["serverUser"],
	//'group' => $config["serverGroup"],
	//'chroot' => $config["serverChroot"]
]);
$http->application = null;
$http->rabbit = null;
$http->on('WorkerStart', function ($serv, $worker_id) use ($http, $config) {
	//初始化yaf
	$http->application  = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
	//初始化rabbit
	$rabbit_config = array(
		'host' => $config["rabbitHost"],  //host
		'port' => intval($config["rabbitPort"]), //端口
		'username' => $config["rabbitUsr"],  //账号
		'password' => $config["rabbitPwd"],  //密码
		'vhost' => $config["rabbitVhost"] //虚拟主机
	);
	//与rabbit 建长连接
	$http->rabbit["connection"] = new AMQPConnection($rabbit_config);
	if (!$http->rabbit["connection"]->pconnect()) {
		throw new Exception('RabbitMQ创建连接失败');
	}
	//建立rabbit通道  AMQPChannel 
	$http->rabbit["channel"] = new AMQPChannel($http->rabbit["connection"]);
	Yaf_Registry::set('rabbit', $http->rabbit); //设置rabbit连接对像
	Yaf_Registry::set('serverConfig', $config); //设置配置信息
});
$http->on('request', function ($request, $response) use ($http) {
	//对Chrome 的处理
	if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
		$response->end();
		return;
	}
	$request_uri = str_replace("/index.php", "", $request->server['request_uri']);
	$yaf_request = new Yaf_Request_Http($request_uri);
	$http->application->getDispatcher()->setRequest($yaf_request);

	Yaf_Registry::set('swoole_req', $request);
	Yaf_Registry::set('swoole_res', $response);

	// yaf 会自动输出脚本内容，因此这里使用缓存区接受交给swoole response 对象返回
	ob_start();
	$http->application->getDispatcher()->disableView();
	$http->application->bootstrap()->run();
	$data = ob_get_clean();
	$response->end($data);
});

$http->start();
