<?php
$functions[] = function ($data, $mq)
{
	if($data->t === "MESSAGE_CREATE")
	{
		echo("{$data->d->author->username}: {$data->d->content}\n");
	}
};
