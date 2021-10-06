<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queueName = "API_Inmobiliaria";
$exchangeName = "proveedor_selectivo_Ofertas_Exchange";
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
$channel->exchange_declare($exchangeName, 'direct', false, true, false);



$script = file_get_contents(__DIR__ . "/data/alquileres.json");
$sentences = json_decode($script, true);
foreach ($sentences["ofertas"] as $msg) {
    $offerString = json_encode($msg);
    $AMQPMsg = new AMQPMessage($offerString, $msgProperties);
    $key = (false != strpos($msg["descripcion"], "playa")) ? "playa" : "centro";
    $channel->basic_publish($AMQPMsg, $exchangeName, $key);
    echo "\n Offer sent: '" . $offerString . "'";
    //sleep(3);
}

$channel->close();
$connection->close();


