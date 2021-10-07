<?php
//Receiving messages based on a pattern (topics)
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$exchangeName = "Mensajes_Systema_Exchange";
$host = "rabbitmq";
$username = "basic_producer";
$password = "1234";
$msgProperties = [
    'content_type' => 'plain/text',
    'content_encoding' => 'utf-8',
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    'expiration' => 60000 * 60,
    'message_id' => uniqid(),
    'timestamp' => (new DateTime('now'))->getTimestamp(),
    'user_id' => $username,
    'app_id' => '04_topics',
];
$vhost = "basic_virtual_host";

$connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
$channel = $connection->channel();
$channel->queue_declare('rpc_queue', false, true, false, false);

function fib($n)
{
    if ($n == 0) {
        return 0;
    }
    if ($n == 1) {
        return 1;
    }
    return fib($n-1) + fib($n-2);
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = intval($req->body);
    echo ' [.] fib(', $n, ")\n";

    $msg = new AMQPMessage(
        (string) fib($n),
        array('correlation_id' => $req->get('correlation_id'))
    );

    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    $req->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();