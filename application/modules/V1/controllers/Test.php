<?php

/**
 * @name IndexController
 * @author {&$AUTHOR&}
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class TestController extends BaseController
{

    /**
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/{&$APP_NAME&}/index/index/index/name/{&$AUTHOR&} 的时候, 你就会发现不同
     */
    public function indexAction($name = "Stranger")
    {
        //1. fetch query
        // $get = $this->getRequest()->getQuery("get", "default value");
        $service = json_decode($this->getService("search_user_business"), true);
        //var_dump($service);
        $data = $this->curl($service["data"] . "/v1/searchbusiness/index?k=" . urlencode("重庆"), []);
        // echo $service["data"] . "/v1/searchbusiness/index?k=" . urlencode("重庆");
        echo ($data);
        // echo "xxxxx";
    }
}
