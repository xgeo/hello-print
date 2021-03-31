<?php
declare(strict_types=1);

namespace HelloPrint\Core;

use HelloPrint\Consumers\MainConsumer;
use HelloPrint\Contracts\ConsumerInterface;
use HelloPrint\Models\Request;
use HelloPrint\Producers\MainProducer;

/**
 * Class Worker
 * @package HelloPrint\Core
 */
class Worker
{

    /**
     * @param array $input
     * @param string $messageBody
     * @return \stdClass
     */
    public static function execute(array $input, string $messageBody)
    {
        $isRandomize = isset($input['randomize']) ?: false;

        $messageStrategy = new MessageStrategy(new Request());
        $messageStrategy->setRandomize($isRandomize);

        if (isset($input['append'])) {
            $messageStrategy->setAppend($input['append']);
        }

        return $messageStrategy->store($messageBody, $input['message']);
    }

    /**
     * @param ConsumerInterface $consumer
     * @param array $input
     * @return \stdClass
     * @throws \Exception
     */
    public static function pulling(ConsumerInterface $consumer, array $input)
    {
        $response = $consumer->subscribe($input['topic'], 50*1000);

        if ($response) {
            /** @var \stdClass $kafkaMessage */
            $kafkaMessage   = $response->getKafkaMessage();

            $json = json_decode($kafkaMessage->payload, false);

            if (is_null($kafkaMessage) || is_null($json) || empty($json->body)) {
                throw new \Exception("No response");
            }

            return self::execute($input, $json->body);

        } else {
            throw new \Exception("No response");
        }
    }

    /**
     * @param \stdClass $request
     */
    public static function display(\stdClass $request): void
    {
        if ($request->message) {
            echo "\n{$request->message}\n";
        }
    }

    public static function broadcast(MainProducer $mainProducer, array $input, ?int $id)
    {
        if (isset($input['broadcast_topic']) && !is_null($id)) {
            $mainProducer->createMessage($input['broadcast_topic'], "{$id}");
        }
    }

    /**
     * @param array $input
     * @param MainConsumer $consumer
     */
    public static function startPulling(array $input, MainConsumer $consumer)
    {
        while (true)
        {
            try
            {
                $request = self::pulling($consumer, $input);

                self::display($request);

                self::broadcast(MainProducer::getInstance(), $input, $request->id);

            } catch (\Exception $e) {
                self::displayTime($e->getMessage());
            } catch (\Interop\Queue\Exception $e) {
                self::displayTime($e->getMessage());
            }
        }
    }

    /**
     * @param string $dump
     */
    public static function displayTime(string $dump)
    {
        $created_at = (new \DateTime())->format(DATE_ISO8601);
        echo "\n[Exception] :: [{$created_at}] :: {$dump} \n";;
    }
}