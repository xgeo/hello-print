<?php


namespace HelloPrint\Traits;

use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaContext;
use Enqueue\RdKafka\RdKafkaTopic;
use Interop\Queue\Consumer;

/**
 * Trait ConsumerTrait
 * @package HelloPrint\Traits
 * @property RdKafkaContext $context
 */
trait ConsumerTrait
{
    /**
     * @param string $topic
     * @return RdKafkaConsumer|Consumer
     */
    public function subscribe(string $topic): Consumer
    {
        /** @var RdKafkaTopic $topic */
        $topic = $this->context->createTopic($topic);

        return $this->context->createConsumer($topic);
    }
}