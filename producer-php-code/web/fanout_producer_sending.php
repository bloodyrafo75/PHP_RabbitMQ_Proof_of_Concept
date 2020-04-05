<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$exchangeName="fanout_broadcasting";
$exchangeMode="fanout"; 
$queueName="fanout_queue_broadcasting";
$durableQueue=true;

//connect to server
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();
//Declare exchange
$channel->exchange_declare($exchangeName, $exchangeMode, false, false, false);
//Declare Queue to use as "main stream"
$channel->queue_declare($queueName, false, $durableQueue, false, false);
//binding exchange w. queue
$channel->queue_bind($queueName, $exchangeName);

$data="Hello world fanout mode".time();
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);
$channel->basic_publish($msg, $exchangeName);

echo " [x] Sent '".$data."'\n";

$channel->close();
$connection->close();
?>
