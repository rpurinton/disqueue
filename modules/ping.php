<?php
$functions[] = function ($data, $mq)
{
	if($data->t === "MESSAGE_CREATE" && $data->d->content === "ping")
	{
		$mq->publish("pong",[],"","disqueue_send");
	}
};
