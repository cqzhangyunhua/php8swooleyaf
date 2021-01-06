###  RabbitSwoole 是基于 新框架进行开发的rabbitmq消息入队服务,为解决PHP-FPM并发连rabbit问题
### 一、配置说明：
```
运行程序需要先配置conf/application.ini
Php版本要求:8.0 ,Php扩展要求: swoole(4.5.9以上)   amqp
运行程序需要先配置conf/application.ini
建方开启JIT php.ini：
    auto_globals_jit = On
    opcache.jit_buffer_size=100M
    opcache.jit=1205
启运 php public/serverRabbit.php
热重启 kill -usr1 主进程ID
动态查找主进程 t_masterid=`ps -aux|grep server.php|grep Ssl|awk '{print $2}'`
``` 
## 二、项目接口说明
### 1.项目的入队接口
```
访问地址:/v1/inputrabbit/kcwc1
方   式:GET
参   数:
        secretKey:访问服务key
        queueName:队列名称
        queueValue:队列值
返  回:JSON
    成功:{"code":200,"msg":"ok","data":{"status":1}}

```
