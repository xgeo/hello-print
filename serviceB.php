<?php
require_once './vendor/autoload.php';

use \HelloPrint\Core\WorkerOptions;
use \HelloPrint\Workers\WorkerB;
use \HelloPrint\Enums\EventSource;


$workerB = new WorkerB();
$workerB->initPulling(new WorkerOptions(['topic' => EventSource::TOPIC_B, 'verbose' => false]));