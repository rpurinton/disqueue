<?php

include __DIR__."/vendor/autoload.php";

$modules = array_diff(scandir(__DIR__."/modules"), array("..", "."));
foreach($modules as $module) require_once(__DIR__."/modules/$module");

use Bunny\Client;
use Bunny\Message;
use Bunny\Channel;

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

$mq->run(function (Message $mq_message, Channel $mq, Client $bunny) use (&$functions)
{
	$data = json_decode($mq_message->content);
	foreach($functions as $function) $function($data, $mq);
	$mq->ack($mq_message);
        return;
},"disqueue_receive");
