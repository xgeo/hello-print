<?php


namespace HelloPrint\Traits;

use Enqueue\RdKafka\RdKafkaContext;
use Enqueue\RdKafka\RdKafkaTopic;

/**
 * Trait ConsumerTrait
 * @package HelloPrint\Traits
 * @property RdKafkaContext $context
 */
trait ConsumerTrait
{
    /**
     * @param string $topic
     * @param int $timeout
     * @return \Enqueue\RdKafka\RdKafkaMessage|\Interop\Queue\Message|null
     */
    public function subscribe(string $topic, int $timeout = 50)
    {
        /** @var RdKafkaTopic $topic */
        $topic = $this->context->createTopic($topic);

        return $this->context->createConsumer($topic)->receive($timeout);
    }
}