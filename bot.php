<?php

include __DIR__.'/vendor/autoload.php';
use Bunny\Client;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

$connection = [
    'host'      => '127.0.0.1',
    'vhost'     => '/',    // The default vhost is /
    'user'      => 'rabbit', // The default user is guest
    'password'  => 'rabbit', // The default password is guest
];

$bunny = new Client($connection);
$bunny->connect();

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
