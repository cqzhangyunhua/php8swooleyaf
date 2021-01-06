<?php

/**
 * @name IndexController
 * @author {&$AUTHOR&}
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BaseController extends Yaf_Controller_Abstract
{
    public $req;
    public $res;
    public $serverConfig;
    // public function __construct()
    // {
    //     echo "this is base";
    // }
    /**
     * 默认初始化方法，如果不需要，可以删除掉这个方法
     * 如果这个方法被定义，那么在Controller被构造以后，Yaf会调用这个方法
     */
    public function init()
    {
        //  echo "this is base class";
        $this->req = Yaf_Registry::get('swoole_req');
        $this->res = Yaf_Registry::get('swoole_res');
        $this->serverConfig = Yaf_Registry::get('serverConfig');
        //验证是否有secretkey
        if (!isset($this->req->get["secretKey"]) || $this->req->get["secretKey"] != $this->serverConfig["secretKey"]) {
            $this->toJsonOk("请输入正确的secretKey");
        }
    }
    public function toJsonOk($data)
    {
        echo json_encode(["code" => 200, "msg" => "ok", "data" => $data,]);
    }
    /**
     * param:$servicename
     * Date:2020-07-17 
     * Desc: getService
     * Return:   json or false
     * **/
    public  function getService($servicename = "") //改写 访问zk 判断服务器状态选择节点 ******************************  加缓存路由表 由专门来管理
    {
        if (trim($servicename) == "") {
            return false;
        }
        $config = Yaf_Registry::get("config");
        if (!isset($config["serviceurl"])) {
            return false;
        }
        $sUrl = $config["serviceurl"] . "?service=" . $servicename;
        ///$d = file_get_contents($sUrl);
        $d = $this->curl($sUrl, []);
        return $d;
    }

    /**
     * param:$url,$data,$method
     * Date:2020-07-10 
     * Desc: php curl
     * Return:   json
     * **/
    public  function curl($url, $data, $method = 'GET', $header = "Content-Type:text/plain; charset=utf-8")
    {
        try {
            $ch = curl_init();
            $headers[] = $header;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            switch ($method) {
                case 'GET':
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            $output = curl_exec($ch);
            $err = curl_error($ch);
            return $output;
        } catch (\Exception $e) {
            return false;
        }
    }
}
