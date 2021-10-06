<link rel="stylesheet" href="basic.css"/>

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$queueName = "basic_queue";
$host = "rabbitmq";
$username = "basic_consumer";
$password = "1234";
$vhost = "basic_virtual_host";

try {
    $connection = new AMQPStreamConnection($host, 5672, $username, $password, $vhost);
    $channel = $connection->channel();
    $channel->queue_declare($queueName, false, true, false, true);

    $messages = [];
    while ($result = ($channel->basic_get($queueName))) {
        $properties = $result->get_properties();
        $msgTimestamp = "";
        if (isset($properties["timestamp"])) {
            $dt = (new DateTime())->setTimestamp($properties["timestamp"]);
            $msgTimestamp = $dt->format("d/m/Y H:i:s");
        }
        $messages[] = ["ts" => $msgTimestamp, "body" => $result->body];
        $delivery_mode=($properties["delivery_mode"]==2) ? "Persistent": "NOT Persistent";
        echo "MsgId: ".$properties["message_id"];
        echo "<br/>Timestamp:" . $msgTimestamp;
        echo "<br/>Msg: <code>" . $result->body . "</code>";
        echo "<hr/>";
    }

} catch (Exception $e) {
    echo $e->getMessage();
}

?>

<p/>
<button onclick="document.location.href='./consumer.php'">Refresh</button>
<script>
    setTimeout(function () {
        document.location.href = './consumer.php';
    }, 1000);
</script>
