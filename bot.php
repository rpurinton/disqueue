<?php

include __DIR__."/vendor/autoload.php";

use Bunny\Client;
use Bunny\Async;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use React\EventLoop\Factory;

$loop = Factory::create();

$discord = new Discord([
	"loop" => $loop,
	"token" => trim(file_get_contents(__DIR__."/token.txt")),
	'logger' => new Logger('DiscordPHP', [new StreamHandler('php://stdout', Logger::DEBUG)])
]);

$bunny_options = [
	"host"      => "127.0.0.1",
	"vhost"     => "/",
	"user"      => "rabbit",
	"password"  => "rabbit",
];

(new Async\Client($loop, $bunny_options))->connect()->then(function (Async\Client $bunny_async_client)
{
	return $bunny_async_client->channel();
})->then(function (Bunny\Channel $mq_consumer) use ($discord)
{
	$mq_consumer->consume(function (Bunny\Message $mq_consumed_message, Bunny\Channel $mq_consumer, Async\Client $bunny_async_client) use ($discord)
	{
		// Here is where we want to send our message back to discord
		// But How? $discord->send(something); ???
		// In the meantime, we will just echo it to the console
		echo("{$mq_consumed_message->content}\n");
		$mq_consumer->ack($mq_consumed_message);
	},"disqueue_send");
});

$bunny_sync_client = new Client($bunny_options);
$bunny_sync_client->connect();
$mq_publisher = $bunny_sync_client->channel();
$mq_publisher->queueDeclare("disqueue_receive");
$mq_publisher->queueDeclare("disqueue_send");

$discord->on("ready", function (Discord $discord) use ($mq_publisher) {
	$discord->on("raw", function ($data, Discord $discord) use ($mq_publisher) {
		$mq_publisher->publish(json_encode($data),[],"","disqueue_receive");
	});
});

$discord->run();
