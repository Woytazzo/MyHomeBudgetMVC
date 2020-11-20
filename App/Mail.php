<?php

namespace App;

use Mailgun\Mailgun;
use App\Config;


class Mail
{
	public static function send($to, $subject, $text, $html)
	{
		
		$mg = Mailgun::create(Config::MAILGUN_API_KEY); // For US servers
//$mg = Mailgun::create(Config::MAILGUN_API_KEY, 'https://api.eu.mailgun.net'); // For EU servers
 
	$mg->messages()->send(Config::MAILGUN_DOMAIN, [
		'from'    => 'wojtek@alphabeteventz.pl',
		'to'      => $to,
		'subject' => $subject,
		'text'    => $text,
		'html'    => $html
		]);
	}
}