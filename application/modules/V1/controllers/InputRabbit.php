<?php

/**
 * InputRabbit
 * @author: 
 */
class InputRabbitController extends BaseController
{

    /**
     * Desc: 用于看车玩车老系统的push rabbit服务
     * @author: zhang yun hua
     */
    public function kcwc1Action()
    {
        //验证是否传队例名称
        if (!isset($this->req->get["queueName"])) {
            $this->toJsonOk("请输入queueName");
        }
        //验证队例值
        if (!isset($this->req->get["queueValue"])) {
            $this->toJsonOk("请输入queueValue");
        }

        // var_dump($this->req->get["queueName"]);
        // return;
        $queueName = trim($this->req->get["queueName"]);
        $queueValue = trim($this->req->get["queueValue"]);
        try {
            $exname = ""; //默认交换机则名称为空,不绑定则可
            $rabbit = Yaf_Registry::get('rabbit');
            $exchange = new AMQPExchange($rabbit["channel"]);
            $exchange->setFlags(AMQP_DURABLE); //持久化
            $exchange->setName($exname);
            $exchange->setType(AMQP_EX_TYPE_DIRECT); //direct类型
            // $exchange->declareExchange();
            $queue = new AMQPQueue($rabbit["channel"]);
            $queue->setName($queueName);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();
            //绑定
            //  $queue->bind($exname, 'test_key_1');
            $exchange->publish($queueValue, $queueName, AMQP_MANDATORY, array('delivery_mode' => 2));
        } catch (\Exception  $e) {
            throw new Exception('RabbitMQ创建连接失败');
        }

        $data = ["status" => 1]; //$this->curl($service["data"] . "/v1/searchbusiness/index?k=" . urlencode($k), []);
        $this->toJsonOk($data);
    }

    /**
     * create
     * @author: xx
     */
    public function createAction()
    { }

    /**
     * update
     * @author: xx
     */
    public function updateAction()
    { }

    /**
     * delete
     * @author: xx
     */
    public function deleteAction()
    { }
}
