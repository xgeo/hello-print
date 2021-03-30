<?php
require_once './vendor/autoload.php';
use \HelloPrint\Consumers\MainConsumer;
use \HelloPrint\Contracts\ConsumerInterface;
use \HelloPrint\Models\Request;
use \HelloPrint\Producers\MainProducer;
use \HelloPrint\Core\MessageStrategy;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

if (!function_exists('execute')) {

    /**
     * @param array $input
     * @param string $messageBody
     * @return stdClass
     */
    function execute(array $input, string $messageBody)
    {
        $isRandomize    = isset($input['randomize']) ? $input['randomize'] : false;

        $messageStrategy = new MessageStrategy(new Request());
        $messageStrategy->setRandomize($isRandomize);

        if (isset($input['append'])) {
            $messageStrategy->setAppend($input['append']);
        }

        return $messageStrategy->process($messageBody, $input['message']);
    }
}

if (!function_exists('polling'))
{
    /**
     * @param ConsumerInterface $consumer
     * @param array $input
     * @return stdClass|null
     * @throws Exception
     */
    function polling(ConsumerInterface $consumer, array $input)
    {
        $response = $consumer->subscribe($input['topic'], 50*1000);

        if ($response) {
            /** @var stdClass $kafkaMessage */
            $kafkaMessage   = $response->getKafkaMessage();

            $json           = json_decode($kafkaMessage->payload, false);

            if (is_null($kafkaMessage) || is_null($json) || empty($json->body)) {
                throw new Exception("No response");
            }

            return execute($input, $json->body);

        } else {
            throw new Exception("No response");
        }
    }
}

if (!function_exists('broadcast'))
{
    /**
     * @param MainProducer $mainProducer
     * @param $input
     * @param $id
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     * @throws \Interop\Queue\Exception
     */
    function broadcast(MainProducer $mainProducer, $input, $id)
    {
        if (isset($input['broadcast_topic']) && !is_null($id))
        {
            $mainProducer->createMessage($input['broadcast_topic'], "id={$id}");
        }
    }
}

if (!function_exists('display')) {
    /**
     * @param stdClass $pool
     */
    function display(stdClass $pool): void
    {
        if ($pool->message) {
            echo "\n{$pool->message}\n";
        }
    }
}

$input = getopt('', ["topic::", "message::", "broadcast_topic:", "randomize::", "append::"]);

while (true)
{
    try
    {
        $consumer       = MainConsumer::getInstance();
        $mainProducer   = MainProducer::getInstance();
        $pool           = polling($consumer, $input);

        display($pool);

        broadcast($mainProducer, $input, $pool->id);

    } catch (\Exception $e) {
        $created_at     = (new DateTime())->format(DATE_ISO8601);
        echo "\n[Exception] :: [{$created_at}] :: {$e->getMessage()} \n";;
    } catch (\Interop\Queue\Exception $e) {
        $created_at     = (new DateTime())->format(DATE_ISO8601);
        echo "\n[Exception] :: [{$created_at}] :: {$e->getMessage()} \n";;
    }
}
