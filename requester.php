<?php
require_once './vendor/autoload.php';

use HelloPrint\Enums\EventSource;
use \HelloPrint\Workers\Broker;
use \HelloPrint\Core\WorkerOptions;

$broker = new Broker();
$broker->createHiMessage();
$broker->initPulling(new WorkerOptions([
    'verbose' => true,
    'topic' => EventSource::BROKER
]));