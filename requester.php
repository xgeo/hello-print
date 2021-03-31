<?php
require_once './vendor/autoload.php';
use \HelloPrint\Core\Worker;
use \HelloPrint\Producers\MainProducer;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
$GLOBALS['isVerboseActive'] = true;

if (!function_exists('facadeInitialize')) {
    /**
     * @param array $input
     * @throws \Interop\Queue\Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    function facadeInitialize(array $input)
    {
        if (isset($input['verbose'])) {
            $isVerboseActive = ($input['verbose'] === 'true');
            $GLOBALS['isVerboseActive'] = $isVerboseActive;
        }

        $isPullingActive = false;
        if (isset($input['pulling'])) {
            $isPullingActive = ($input['pulling'] === "true");
        }

        $append = false;
        if (isset($input['append'])) {
            $append = ($input['append'] === 'true');
        }

        $hasMessage = false;
        if (isset($input['message'])) {
            $hasMessage = true;
        }

        if (!$append && $hasMessage) {
            MainProducer::getInstance()->createMessage($input['topic'], $input['message']);
        } else if ($isPullingActive) {
            Worker::startPulling($input);
        }
    }
}

try {
    $input = getopt('',
        ["topic::", "message::", "broadcast_topic:", "randomize::", "append::", "appendTo", "pulling::", "verbose::"]
    );

    facadeInitialize($input);

} catch (\Exception $e) {
    Worker::displayTime($e->getMessage());
} catch (\Interop\Queue\Exception $e) {
    Worker::displayTime($e->getMessage());
}
