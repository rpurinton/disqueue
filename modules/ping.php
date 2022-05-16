<?php
$functions[] = function ($data, $mq)
{
	if($data->t === "MESSAGE_CREATE" && $data->d->content === "ping")
	{
		echo("Reply: pong\n");
	}
};
