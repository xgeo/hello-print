<?php

namespace HelloPrint\Workers;

use HelloPrint\Core\AbstractWorker;
use HelloPrint\Core\WorkerOptions;
use HelloPrint\Enums\EventSource;
use Interop\Queue\Exception;

/**
 * Class WorkerB
 * @package HelloPrint\Workers
 */
class WorkerB extends AbstractWorker
{

    /**
     * @param WorkerOptions|null $options
     * @throws Exception
     */
    public function initPulling(?WorkerOptions $options): void
    {
        $consumer = $this->consumer->subscribe($options->topic);

        while (true)
        {
            try
            {
                $request = $this->pulling($consumer, $options);

                if (!is_null($request) && !$request->isEmptyMessage()) {
                    $message = $request->concat(" Bye.");
                    $from = $options->topic;
                    $this->emit(EventSource::BROKER, json_encode(compact('message', 'from')));
                }

            } catch (\Exception $e) {
                self::displayError($e->getMessage(), $options->verbose);
            }
        }
    }
}