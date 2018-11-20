<?php
namespace app\Redis\controller;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\facade\Config;

use app\Redis\controller\Redis;
 
// use app\http\controller\MyWorkerServer as MyServer;

class RedisServer extends Command
{
    // 设置处理控制器以及相关指令
    protected function configure()
    {
        // echo "configure\n";die;
        // echo "[MyWorker->configure]\n";
        $this->setName('redis') //命令
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status", 'start') //设置命令，默认start
            ->setDescription('MyWorkerServer control'); //设置控制器的描述
    }
    
    //
    protected function execute(Input $input, Output $output)
    {
        // echo "[MyWorker->execute]\n";
        // $status  = $input->getArgument('status');
        // 获取输入的指令
        $action = $input->getArgument('action');
        //DIRECTORY_SEPARATOR: 目录分隔符，是定义php的内置常量;
        if (DIRECTORY_SEPARATOR !== '\\') {
            // 判断输入的指令是否在指定的命令列表中
            if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status'])) {
                $output->writeln("Invalid argument action:{$action}, Expected start|stop|restart|reload|status .");
                exit(1);
            }
            // 定义全局变量，保存参数
            global $argv;
            // array_shift:删除数组中的第一个元素，并返回被删除元素的值
            array_shift($argv);
            array_shift($argv);
            // 把'think'和 指令 插入参数数组中
            array_unshift($argv, 'think', $action);
        } elseif ('start' != $action) { //判断指令是否为start,windows下只支持start指令
            $output->writeln("Not Support action:{$action} on Windows.");
            exit(1);
        }
        /**
         *   功能实现
         */
        // 输出描述信息
        $redis = new Redis();
        $redis->start();
    }
}
