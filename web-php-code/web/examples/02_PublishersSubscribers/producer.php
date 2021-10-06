<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queueNameA = "API_Inmobiliaria_A";
$queueNameB = "API_Inmobiliaria_B";
$exchangeName="proveedorOfertasExchange";
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
    'app_id' => '02_pubsubs',
];
$vhost = "basic_virtual_host";

$connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
$channel = $connection->channel();
$channel->exchange_declare($exchangeName, 'fanout', false, true, false);

$channel->queue_declare($queueNameA, false, true, false, false);
$channel->queue_declare($queueNameB, false, true, false, false);

$channel->queue_bind($queueNameA, $exchangeName);
$channel->queue_bind($queueNameB, $exchangeName);



$script = file_get_contents(__DIR__ . "/data/alquileres.json");
$sentences = json_decode($script, true);
foreach ($sentences["ofertas"] as $msg) {
    $offerString = json_encode($msg);
    $AMQPMsg = new AMQPMessage($offerString, $msgProperties);
    $channel->basic_publish($AMQPMsg, $exchangeName);
    echo "\n Offer sent: '" . $offerString . "'";
    //sleep(3);
}

$channel->close();
$connection->close();


