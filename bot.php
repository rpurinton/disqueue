<?php

include __DIR__.'/vendor/autoload.php';

use Bunny\Client;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

$connection = [
	'host'      => '127.0.0.1',
	'vhost'     => '/',
	'user'      => 'rabbit',
	'password'  => 'rabbit',
];

$bunny = new Client($connection);
$bunny->connect();
$mq = $bunny->channel();
$mq->queueDeclare('disqueue_receive');
$mq->queueDeclare('disqueue_send');

$discord = new Discord([
	'token' => trim(file_get_contents(__DIR__."/token.txt")),
]);

$discord->on('ready', function (Discord $discord) use ($mq) {
	echo "Bot is ready!", PHP_EOL;

	$discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($mq) {
		$mq->publish($message,[],'','disqueue_receive');
		echo "{$message->author->username}: {$message->content}", PHP_EOL;
	});
});

$discord->run();
