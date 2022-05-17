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
$mq = $bunny_client->channel();
$mq->queueDeclare("disqueue_receive");
$mq->queueDeclare("disqueue_send");

$mq->run(function (Message $mq_consumed_message, Channel $mq, Client $bunny_client) use (&$functions)
{
	$data = json_decode($mq_consumed_message->content);
	foreach($functions as $function) $function($data,$mq);
	$mq->ack($mq_consumed_message);
        return;
},"disqueue_receive");
