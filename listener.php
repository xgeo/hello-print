<?php
require_once './vendor/autoload.php';

use function HelloPrint\Core\Helpers\{ polling, display, broadcast };

$input = getopt('', ["topic::", "message::", "broadcast_topic:", "randomize::", "append::"]);

while (true)
{
    try
    {

        $pool = polling($consumer, $input);

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
