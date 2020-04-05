<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$exchangeName="fanout_broadcasting";
$exchangeMode="fanout"; 
$queueName="fanout_queue_broadcasting".time();
$durableQueue=true;


//connect to server
$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test');
$channel = $connection->channel();
//Declare exchange
$channel->exchange_declare($exchangeName, $exchangeMode, false, false, false);
//Declare Queue to use
$channel->queue_declare($queueName, false, $durableQueue, false, false);
//binding exchange w. queue
$channel->queue_bind($queueName, $exchangeName);


echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$noAck=false;
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '', false, $noAck, false, false, $callback);


while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
