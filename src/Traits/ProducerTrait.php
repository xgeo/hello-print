<?php


namespace HelloPrint\Traits;

use Enqueue\RdKafka\RdKafkaContext;

/**
 * Trait Producer
 * @package HelloPrint\Traits
 * @property RdKafkaContext $context
 */
trait ProducerTrait
{
    /**
     * @param string $topic
     * @param string $message
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function createMessage(string $topic, string $message)
    {
        $topic = $this->context->createTopic($topic);
        $message = $this->context->createMessage($message);
        $this->context->createProducer()->send($topic, $message);
    }
}