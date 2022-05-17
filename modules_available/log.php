<?php
define("LOG",true);
$functions[] = function ($data, $mq)
{
	if($data->t != "")
	{
		echo("[{$data->t}] ");
		switch($data->t)
		{
			case "MESSAGE_CREATE":
				echo("{$data->d->author->username}: {$data->d->content}");
				break;
			case "TYPING_START":
				echo($data->d->member->user->username);
				break;
		}
		echo PHP_EOL;
	}
};
