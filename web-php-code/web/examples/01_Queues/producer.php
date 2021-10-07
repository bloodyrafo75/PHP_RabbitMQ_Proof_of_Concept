<?php
//Distributing tasks among workers
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queueName = "01_restaurant";
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
    'app_id' => '01_queue',
];
$vhost = "basic_virtual_host";

$connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
$channel = $connection->channel();
$channel->queue_declare($queueName, false, true, false, false);

$script = file_get_contents(__DIR__ . "/data/tapas.json");
$sentences = json_decode($script, true);
foreach ($sentences["solicitudes"] as $msg) {
    $AMQPMsg = new AMQPMessage($msg["n"], $msgProperties);
    $channel->basic_publish($AMQPMsg, '', $queueName);
    echo "\n Message sent: '" . $msg["n"] . "'";
    //sleep(3);
}

$channel->close();
$connection->close();


