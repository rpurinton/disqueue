<?php

include __DIR__."/vendor/autoload.php";

use Bunny\Client;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$connection = [
	"host"      => "127.0.0.1",
	"vhost"     => "/",
	"user"      => "rabbit",
	"password"  => "rabbit",
];

$bunny = new Client($connection);
$bunny->connect();

$mq = $bunny->channel();
$mq->queueDeclare("disqueue_receive");
$mq->queueDeclare("disqueue_send");

$discord = new Discord([
	"token" => trim(file_get_contents(__DIR__."/token.txt")),
	'logger' => new Logger('DiscordPHP', [new StreamHandler('php://stdout', Logger::DEBUG)])
]);

$discord->on("ready", function (Discord $discord) use ($mq) {
	$discord->on("raw", function ($data, Discord $discord) use ($mq) {
		$mq->publish(json_encode($data),[],"","disqueue_receive");
	});
});

$discord->run();
