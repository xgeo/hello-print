<?php
require_once './vendor/autoload.php';
use \HelloPrint\Workers\Broker;

$broker = new Broker();

$broker->createHiMessage();