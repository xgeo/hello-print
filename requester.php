<?php
require_once './vendor/autoload.php';
use \HelloPrint\Core\Worker;
use \HelloPrint\Producers\MainProducer;
use \HelloPrint\Consumers\MainConsumer;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

if (!function_exists('facadeInitialize')) {
    /**
     * @param array $input
     * @throws \Interop\Queue\Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    function facadeInitialize(array $input)
    {
        $isPullingActive = false;
        if (isset($input['pulling'])) {
            $isPullingActive = ($input['pulling'] === "true");
        }

        $hasMessage = false;
        if (isset($input['message'])) {
            $hasMessage = true;
        }

        if ($hasMessage) {
            MainProducer::getInstance()->createMessage($input['topic'], $input['message']);
        }

        if ($isPullingActive) {
            Worker::startPulling($input, MainConsumer::getInstance());
        }
    }
}

try {
    $input = getopt('',
        ["topic::", "message::", "broadcast_topic:", "randomize::", "append::", "pulling::"]
    );

    facadeInitialize($input);

} catch (\Exception $e) {
    Worker::displayTime($e->getMessage());
} catch (\Interop\Queue\Exception $e) {
    Worker::displayTime($e->getMessage());
}
