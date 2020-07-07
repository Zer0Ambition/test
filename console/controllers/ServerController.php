<?php
namespace console\controllers;

use frontend\daemons\ChatServer;
use yii\console\Controller;
use Yii;

class ServerController extends Controller
{
    public $server_port;

    public function actionStart()
    {
        $server = new ChatServer();
        $server->port = 8060;

        $server->on(ChatServer::EVENT_WEBSOCKET_OPEN_ERROR, function($e) use($server) {
            echo "Error opening port " . $server->port . "\n";
            $server->port += 1;
            $server->start();
        });

        $server->on(ChatServer::EVENT_WEBSOCKET_OPEN, function($e) use($server) {
            echo "Server started at port " . $server->port;
        });

        $server_port = $server->port

        $server->start();
    }
}