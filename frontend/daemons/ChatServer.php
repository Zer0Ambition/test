<?php
namespace frontend\daemons;

use consik\yii2websocket\events\WSClientMessageEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use yii\helpers\Console;
use common\models\User;

class ChatServer extends WebSocketServer
{
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_CONNECTED, function( $e) {
            $e->client->name = null;
        });
    }


    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    public function commandChat(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        $result = ['message' => ''];
        $date = new \DateTime();

        if (!$client->name) {
            $result['message'] = 'You are not authorized!';
        } elseif (!empty($request['message']) && $message = trim($request['message']) ) {
            foreach ($this->clients as $chatClient) {
                $chatClient->send( json_encode([
                    'type' => 'chat',
                    'from' => $client->name,
                    'message' => $message,
                    'user_id' => $request['user_id'],
                    'date' => $date->format('H:s'),
                ]) );
            }
        } else {
            $result['message'] = 'Enter message';
        }

        $client->send( json_encode($result) );
    }

    public function commandConnect(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);

        if (!empty($request['user_id'])) {
            $current_user = User::findOne($request['user_id']);
            $client->name = $current_user->username;
            $result = ['message' => $client->name . ' just connected to the chat!'];
        }
        
        foreach ($this->clients as $chatClient) {
            $chatClient->send( json_encode($result) );
        }
    }

}