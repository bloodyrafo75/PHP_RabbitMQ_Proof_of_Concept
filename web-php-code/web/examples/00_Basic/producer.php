<link rel="stylesheet" href="basic.css"/>

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (isset($_POST["msg"])) {
    $queueName = "basic_queue";
    $host = "rabbitmq";
    $username = "basic_producer";
    $password = "1234";
    $msgProperties = [
        'content_type' => 'plain/text',
        'content_encoding' => 'utf-8',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'expiration' => 15000,
        'message_id' => uniqid(),
        'timestamp' => (new DateTime('now'))->getTimestamp(),
        'user_id' => $username,
        'app_id' => '00_basic',
    ];
    $vhost = "basic_virtual_host";

    $msg = $_POST["msg"];
    $connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
    $channel = $connection->channel();
    $channel->queue_declare($queueName, false, true, false, true);
    $AMQPMsg = new AMQPMessage($msg, $msgProperties);
    $channel->basic_publish($AMQPMsg, '', $queueName);


    echo "<div class='notification'><li>'<code>" . $msg . "'</code></div><p/>";


    $channel->close();
    $connection->close();
}

?>




Send to consumer:
<form id="producer" method="post" action="./producer.php">
    <textarea cols=60 rows=20
              name="msg">{"records":[{"id":"2345","status":"deceased"},{"id":"2346","status":"alive"}]}</textarea>
    <p/>
    <input type="submit" value="send.request">
</form>
