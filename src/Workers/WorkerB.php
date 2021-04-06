<?php

namespace HelloPrint\Workers;

use HelloPrint\Core\AbstractWorker;
use HelloPrint\Core\WorkerOptions;
use HelloPrint\Enums\EventSource;
use HelloPrint\Models\Request;

/**
 * Class WorkerB
 * @package HelloPrint\Workers
 */
class WorkerB extends AbstractWorker
{

    /**
     * @param WorkerOptions|null $options
     * @return Request|null
     * @throws \Interop\Queue\Exception
     */
    public function initPulling(?WorkerOptions $options): void
    {
        $consumer = $this->consumer->subscribe($options->topic);

        while (true)
        {
            try
            {
                $request = $this->pulling($consumer, $options->ms);

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