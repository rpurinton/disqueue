<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

$discord = new Discord([
	'token' => trim(file_get_contents(__DIR__."/token.txt")),
]);

$discord->on('ready', function (Discord $discord) {
	echo "Bot is ready!", PHP_EOL;

	// Listen for messages.
	$discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
	    echo "{$message->author->username}: {$message->content}", PHP_EOL;
	});
});

$discord->run();
