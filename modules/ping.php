<?php
$functions[] = function ($data, $mq)
{
	if($data->t === "MESSAGE_CREATE" && $data->d->content === "ping")
	{
		$mq->publish(make_reply($data,"pong"),[],"","disqueue_send");
	}
};

function make_reply($data, $content)
{
	$response["content"] = $content;
	$response["reply-to"] = $data->d->id;
	$response["guild_id"] = $data->d->guild_id;
	$response["channel_id"] = $data->d->channel_id;
	return json_encode($response);
}
