<?php


namespace HelloPrint\Core;


use HelloPrint\Consumers\MainConsumer;
use HelloPrint\Enums\ResponseMessage;
use HelloPrint\Models\Request;
use HelloPrint\Producers\MainProducer;
use \Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

/**
 * Class AbstractWorker
 * @package HelloPrint\Workers
 */
abstract class AbstractWorker
{
    /**
     * @var MainConsumer
     */
    protected MainConsumer $consumer;

    /**
     * @var MainProducer
     */
    protected MainProducer $producer;

    /**
     * @param WorkerOptions|null $options
     */
    abstract public function initPulling(?WorkerOptions $options): void;

    /**
     * AbstractWorker constructor.
     */
    public function __construct()
    {
        $this->consumer = MainConsumer::getInstance();
        $this->producer = MainProducer::getInstance();
    }

    /**
     * @param \stdClass $json
     * @param string $topic
     * @return WorkerMessage
     */
    private function factoryMessage(\stdClass $json): WorkerMessage
    {
        return new WorkerMessage(new Request(), $json);
    }

    /**
     * @param string $topic
     * @param int $ms
     * @return WorkerMessage|null
     * @throws Exception
     */
    protected function pulling(string $topic, int $ms = 50): ?WorkerMessage
    {
        $response = $this->consumer->subscribe($topic, $ms * 1000);

        if ($response) {
            /** @var \stdClass $kafkaMessage */
            $kafkaMessage   = $response->getKafkaMessage();

            $json = json_decode($kafkaMessage->payload, false);

            if (is_null($kafkaMessage) || is_null($json) || empty($json->body)) {
                throw new Exception(ResponseMessage::NO_RESPONSE);
            }

            $jsonObject = json_decode($json->body, false);
            $jsonObject->from = $topic;

            return $this->factoryMessage($jsonObject);

        } else {
            throw new Exception(ResponseMessage::NO_RESPONSE);
        }
    }

    /**
     * @param \stdClass $request
     * @param bool $verbose
     */
    public function display(\stdClass $request, bool $verbose = true): void
    {
        if ($request->message && $verbose) {
            echo "\n{$request->message}\n";
        }
    }

    /**
     * @param string $topic
     * @param string $message
     * @throws \Interop\Queue\Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function emit(string $topic, string $message): void
    {
        $this->producer->createMessage($topic, $message);
    }

    /**
     * @param string $dump
     * @param bool|null $isVerboseActive
     */
    public function displayError(string $dump, ?bool $isVerboseActive = true)
    {
        if ($isVerboseActive) {
            $created_at = (new \DateTime())->format(DATE_ISO8601);
            echo "\n{$created_at} . {$dump} \n";;
        }
    }
}