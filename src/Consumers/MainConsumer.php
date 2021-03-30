<?php
namespace HelloPrint\Consumers;

use Enqueue\RdKafka\RdKafkaContext;
use HelloPrint\Contracts\ConsumerInterface;
use HelloPrint\Core\KafkaBroker;
use HelloPrint\Traits\ConsumerTrait;

class MainConsumer extends KafkaBroker implements ConsumerInterface
{
    use ConsumerTrait;

    public static $instance;
    protected RdKafkaContext $context;

    private function __construct($context)
    {
        $this->context = $context;
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self)
        {
            $connection = self::getConnectionFactory('kafka');

            self::$instance = new self($connection->createContext());
        }

        return self::$instance;
    }
}