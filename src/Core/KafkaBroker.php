<?php
namespace HelloPrint\Core;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaContext;

/**
 * Class KafkaUtils
 * @package HelloPrint\Core
 */
class KafkaBroker
{
    /**
     * @var RdKafkaContext|\Interop\Queue\Context
     */
    protected RdKafkaContext $context;

    protected static function getConnectionFactory(string $kafkaService, int $port = 9092)
    {
        return new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => "{$kafkaService}:{$port}",
                'enable.auto.commit' => 'true'
            ],
            'topic' => [
                'auto.offset.reset' => 'beginning',
            ],
        ]);
    }

    public function getContext()
    {

    }
}