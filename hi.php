<?php
require_once './vendor/autoload.php';

use HelloPrint\Enums\EventSource;
use \HelloPrint\Workers\Broker;

$broker = new Broker();

$broker->createHiMessage();