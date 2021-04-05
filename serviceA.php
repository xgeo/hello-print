<?php
require_once './vendor/autoload.php';

use \HelloPrint\Core\WorkerOptions;
use \HelloPrint\Workers\WorkerA;
use \HelloPrint\Enums\EventSource;


$workerA = new WorkerA();
$workerA->initPulling(new WorkerOptions(['topic' => EventSource::TOPIC_A, 'verbose' => true]));