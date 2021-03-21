<?php
require_once './vendor/autoload.php';

use Enqueue\RdKafka\RdKafkaConnectionFactory;

$connectionFactory = new RdKafkaConnectionFactory([
    'global' => [
        'group.id' => uniqid('', true),
        'metadata.broker.list' => 'kafka:9092',
        'enable.auto.commit' => 'true',
    ],
    'topic' => [
        'auto.offset.reset' => 'beginning',
    ],
]);

$context = $connectionFactory->createContext();
$message = $context->createMessage('Hello world!');
$fooTopic = $context->createTopic('helloworld');

$context->createProducer()->send($fooTopic, $message);

echo 'A test message has been sent to the topic "helloworld".' . PHP_EOL;