<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$exchangeName="hello_queue_exchange_broadcasting";
$exchangeMode="direct"; 
$queueName="hello_queue_broadcasting";
$durableQueue=true;
//set routing
$clientToken=$_GET["client_token"];

//connect to server
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();
//Declare exchange
$channel->exchange_declare($exchangeName, $exchangeMode, false, false, false);
//Declare Queue to use as "main stream"
$channel->queue_declare($queueName, false, $durableQueue, false, false);
//binding exchange w. queue
$channel->queue_bind($queueName, $exchangeName,$clientToken);

$data="Hello world".time();
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, $exchangeName,$clientToken);

echo " [x] Sent '".$data."'\n to $clientToken (client)";

$channel->close();
$connection->close();
?>
