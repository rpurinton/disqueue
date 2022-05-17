<?php
include __DIR__."/vendor/autoload.php";

$modules = array_diff(scandir(__DIR__."/modules_enabled"), array("..", "."));
foreach($modules as $module) require_once(__DIR__."/modules_enabled/$module");

use Bunny\Client;
use Bunny\Message;
use Bunny\Channel;

$bunny_options = [
	"host"      => "127.0.0.1",
	"vhost"     => "/",
	"user"      => "rabbit",
	"password"  => "rabbit",
];

$bunny_client = new Client($bunny_options);
$bunny_client->connect();
$mq_consumer = $bunny_client->channel();
$mq_consumer->queueDeclare("disqueue_receive");
$mq_consumer->queueDeclare("disqueue_send");

$mq_consumer->run(function (Message $mq_consumed_message, Channel $mq_consumer, Client $bunny_client) use (&$functions)
{
	$data = json_decode($mq_consumed_message->content);
	foreach($functions as $function) $function($data,$mq_consumer);
	$mq_consumer->ack($mq_consumed_message);
        return;
},"disqueue_receive");
