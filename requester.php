<?php
require_once './vendor/autoload.php';
use \HelloPrint\Producers\MainProducer;

try {
    $input = getopt('', ["topic::", "message::", "append::"]);

    $started_at = (new DateTime())->format(DATE_ISO8601);
    echo "[Requester] :: [{$started_at}] :: Creating a message. \n";

    $producer = MainProducer::getInstance();
    $producer->createMessage($input['topic'], $input['message']);

    $finished_at = (new DateTime())->format(DATE_ISO8601);

    echo "[Requester] :: Message created. ({$finished_at}) \n";

} catch (\Exception $e) {
    echo "\n[Exception] :: " . $e->getMessage() . "\n";
} catch (\Interop\Queue\Exception $e) {
    echo "\n[Exception] :: " . $e->getMessage() . "\n";
}
