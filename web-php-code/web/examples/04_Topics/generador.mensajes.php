<?php

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
$channel->exchange_declare($exchangeName, 'topic', false, true, false);



$script = file_get_contents(__DIR__ . "/data/mensajes.json");
$sentences = json_decode($script, true);
foreach ($sentences["mensajes"] as $msg) {
    $AMQPMsg = new AMQPMessage($msg["mensaje"], $msgProperties);
    $key = $msg["tipo"];
    $channel->basic_publish($AMQPMsg, $exchangeName, $key);
    echo "\nMsg sent: '" . $msg["mensaje"] . "'";
    //sleep(3);
}

$channel->close();
$connection->close();


