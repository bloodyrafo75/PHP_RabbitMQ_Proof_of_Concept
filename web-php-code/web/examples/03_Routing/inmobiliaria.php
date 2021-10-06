<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$keysToRecover = array_slice($argv, 1);;

$queueName = "API_Inmobiliaria";
$host = "rabbitmq";
$username = "basic_consumer";
$password = "1234";
$vhost = "basic_virtual_host";
$exchangeName = "proveedor_selectivo_Ofertas_Exchange";



$callback = function ($rabbitMsg) {
    $properties = $rabbitMsg->get_properties();
    $msgTimestamp = "";
    if (isset($properties["timestamp"])) {
        $dt = (new DateTime())->setTimestamp($properties["timestamp"]);
        $msgTimestamp = $dt->format("d/m/Y H:i:s");
    }
    echo "\nMsgId: " . $properties["message_id"];
    echo "\nTimestamp:" . $msgTimestamp;
    echo "\nMsg: " . $rabbitMsg->body;
    echo "\n----------\n";

    $rabbitMsg->ack();
};




echo "\nWaiting for offers (" . implode("|",$keysToRecover) . "):";
$connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
$channel = $connection->channel();
$channel->exchange_declare($exchangeName, 'direct', false, true, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
foreach($keysToRecover as $keyToRecover) {
    $channel->queue_bind($queue_name, $exchangeName, $keyToRecover);
}



$channel->basic_consume($queue_name, '', false, false, false, false, $callback);
while ($channel->is_open()) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>


