<?php
use Orchestra\Parser\Xml\Facade as XmlParser;

$xml = XmlParser::load('test.xml');
$user = $xml->parse([
    'id' => ['uses' => 'user.id'],
    'email' => ['uses' => 'user.email'],
    'followers' => ['uses' => 'user::followers'],
]);

var_dump($user);
