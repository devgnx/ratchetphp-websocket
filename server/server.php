<?php
session_start();

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// Make sure composer dependencies have been installed
require __DIR__ . '/vendor/autoload.php';

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 */
class MyChat implements MessageComponentInterface
{
    protected $clients;

    public function __construct() {
        $_SESSION['messages'] = [];

        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $client) {
        echo "Client attached";
        $this->clients->attach($client);

        if (!empty($_SESSION['messages'])) {
            foreach ($_SESSION['messages'] as $msg) {
                $client->send($msg);
            }
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $_SESSION['messages'][] = $msg;

        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $client) {
        echo "Client detached";
        $this->clients->detach($client);
    }

    public function onError(ConnectionInterface $client, \Exception $e) {
        $client->close();
    }
}

// Run the server application through the WebSocket protocol on port 8080
$app = new Ratchet\App('localhost', 8080, '0.0.0.0');
$app->route('/chat', new MyChat, array('*'));
$app->route('/echo', new Ratchet\Server\EchoServer, array('*'));

echo "Ratchet Websocket server started";

$app->run();
