<?php
//Publisher confirmation.
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$exchangeName = "Publish_with_confirmation";
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
$channel->confirm_select();

$channel->set_ack_handler(
    function (AMQPMessage $message) {
        echo "\nACK Mensaje confirmado\n";
    }
);

$channel->set_nack_handler(
    function (AMQPMessage $message) {
        echo "\nNACK Problemas en el envío del mensaje\n";
    }
);

$channel->exchange_declare($exchangeName, 'direct', false, true, false);


$msg = 'Mensaje a confirmar';
$AMQPMsg = new AMQPMessage($msg, $msgProperties);
$channel->basic_publish($AMQPMsg, $exchangeName);
echo "\nMsg sent: '" . $msg . "'";

$channel->wait_for_pending_acks(5.000);


$channel->close();
$connection->close();


