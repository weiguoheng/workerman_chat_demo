<?php

namespace App\Console\Commands;

use Workerman\Worker;
use Illuminate\Console\Command;

class SocketStartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker_man:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ws_worker = new Worker("websocket://0.0.0.0:2000");

        // 启动4个进程对外提供服务
        $ws_worker->count = 4;

        // 当收到客户端发来的数据后返回hello $data给客户端
        $ws_worker->onMessage = function ($connection, $data) {
            // 向客户端发送hello $data
            $connection->send($data);
        };

        // 运行worker
        Worker::runAll();
    }
}
