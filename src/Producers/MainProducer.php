<?php
namespace HelloPrint\Producers;

use Enqueue\RdKafka\RdKafkaContext;
use HelloPrint\Contracts\ProducerInterface;
use HelloPrint\Core\KafkaBroker;
use HelloPrint\Traits\ProducerTrait;

class MainProducer extends KafkaBroker implements ProducerInterface
{
    use ProducerTrait;

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