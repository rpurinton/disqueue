<?php
$functions[] = function ($data, $mq)
{
	if($data->t === "MESSAGE_CREATE" && strtolower($data->d->content) === "ping")
	{
		$payload["data"] = $data;
		$payload["MessageBuilder"] = Discord\Builders\MessageBuilder::new()->setContent("PONG!");
		$mq->publish(json_encode($payload),[],"","disqueue_send");
	}
};
