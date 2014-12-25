<?php
error_reporting(-1);
require '../vendor/autoload.php';
$parameters = require '../config/parameters.php';
var_dump($parameters);

use Respect\Relational\Mapper;
$mapper = new Mapper(new PDO('sqlite:../data/php-translation-fest.sqlite'));
$participants = $mapper->participants->fetchAll();

var_dump($mapper, $participants);

// Create the replacements array
$replacements = array();
foreach ($participants as $participant) {
    $replacements[$participant->email] = array (
        "{vouchercode}" => $participant->vouchercode,
    );
}

// Create the mail transport configuration
$transport = Swift_SmtpTransport::newInstance(
    $parameters['mailer_host'], $parameters['mailer_port'], $parameters['mailer_security']
)
    ->setUsername($parameters['mailer_user'])
    ->setPassword($parameters['mailer_password']
);

// Create an instance of the plugin and register it
$plugin = new Swift_Plugins_DecoratorPlugin($replacements);
$mailer = Swift_Mailer::newInstance($transport);
$mailer->registerPlugin($plugin);

// Create the message
$message = Swift_Message::newInstance();
$message->setSubject('DigitalOcean Promocode - PHP TranslationFest Brasil 2014');
$message->setBody(file_get_contents('../templates/email.php'), 'text/plain');
$message->setFrom(
    $parameters['mailer_sender_email'],
    $parameters['mailer_sender_name']
);
$message->setCc(array(
    'rogerio@phpsp.org.br'   => 'Rogerio Prado de Jesus',
    'erika@digitalocean.com' => 'Erika Heidi'
));

// Send the email
foreach ($participants as $participant) {
    $message->setTo(
        $participant->email,
        $participant->fullname
    );
    $result = $mailer->send($message, $failedRecipients);
    var_dump($participant, $result, $failedRecipients);
}
