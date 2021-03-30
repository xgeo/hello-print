<?php
declare(strict_types=1);

namespace HelloPrint\Core\Helpers;
use \HelloPrint\Contracts\ConsumerInterface;
use HelloPrint\Core\MessageStrategy;
use \HelloPrint\Models\Request;
use \HelloPrint\Producers\MainProducer;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

/**
 * Class Functions
 * @package HelloPrint\Core\Helpers
 */
class Functions
{
    /**
     * @param array $input
     * @param string $messageBody
     * @return \stdClass
     */
    final private static function execute(array $input, string $messageBody)
    {
        $isRandomize = isset($input['randomize']) ? $input['randomize'] : false;

        $messageStrategy = new MessageStrategy(new Request());
        $messageStrategy->setRandomize($isRandomize);

        if (isset($input['append'])) {
            $messageStrategy->setAppend($input['append']);
        }

        return $messageStrategy->process($messageBody, $input['message']);
    }

    /**
     * @param ConsumerInterface $consumer
     * @param array $input
     * @return \stdClass
     * @throws \Exception
     */
    public static function polling(ConsumerInterface $consumer, array $input)
    {
        $response = $consumer->subscribe($input['topic'], 50*1000);

        if ($response) {
            /** @var \stdClass $kafkaMessage */
            $kafkaMessage   = $response->getKafkaMessage();

            $json           = json_decode($kafkaMessage->payload, false);

            if (is_null($kafkaMessage) || is_null($json) || empty($json->body)) {
                throw new \Exception("No response");
            }

            return self::execute($input, $json->body);

        } else {
            throw new \Exception("No response");
        }
    }

    /**
     * @param \stdClass $pool
     */
    public static function display(\stdClass $pool): void
    {
        if ($pool->message) {
            echo "\n{$pool->message}\n";
        }
    }

    /**
     * @param MainProducer $mainProducer
     * @param $input
     * @param $id
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     * @throws \Interop\Queue\Exception
     */
    public static function broadcast(MainProducer $mainProducer, $input, $id)
    {
        if (isset($input['broadcast_topic']) && !is_null($id)) {
            $mainProducer->createMessage($input['broadcast_topic'], "id={$id}");
        }
    }
}