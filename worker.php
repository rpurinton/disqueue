<?php

include __DIR__.'/vendor/autoload.php';

use Bunny\Client;
use Bunny\Message;
use Bunny\Channel;

$connection = [
	'host'      => '127.0.0.1',
	'vhost'     => '/',
	'user'      => 'rabbit',
	'password'  => 'rabbit',
];

$bunny = new Client($connection);
$bunny->connect();
$channel = $bunny->channel();
$channel->queueDeclare('disqueue_receive');
$channel->queueDeclare('disqueue_send');

$channel->run(function (Message $message, Channel $channel, Client $bunny)
{
	$payload = json_decode($message->content);
	echo "{$payload->author->username}: {$payload->content}", PHP_EOL;
	$channel->ack($message);
        return;
},'disqueue_receive');
