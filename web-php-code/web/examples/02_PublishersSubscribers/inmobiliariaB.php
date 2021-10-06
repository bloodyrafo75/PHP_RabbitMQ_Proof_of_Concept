
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$queueName = "API_Inmobiliaria_B";
$host = "rabbitmq";
$username = "basic_consumer";
$password = "1234";
$vhost = "basic_virtual_host";




$callback = function ($rabbitMsg) {
    $properties = $rabbitMsg->get_properties();
    $msgTimestamp = "";
    if (isset($properties["timestamp"])) {
        $dt = (new DateTime())->setTimestamp($properties["timestamp"]);
        $msgTimestamp = $dt->format("d/m/Y H:i:s");
    }
    echo "\nMsgId: " . $properties["message_id"];
    echo "\nTimestamp:" . $msgTimestamp;
    echo "\nMsg: " . $rabbitMsg->body ;
    echo "\n----------\n";

    $rabbitMsg->ack();
};


echo "\nWaiting for messages:";
$connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
$channel = $connection->channel();
$channel->queue_declare($queueName, false, true, false, false);
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>


