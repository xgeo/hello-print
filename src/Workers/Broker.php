<?php

namespace HelloPrint\Workers;

use HelloPrint\Core\AbstractWorker;
use HelloPrint\Core\WorkerOptions;
use HelloPrint\Enums\EventSource;
use Interop\Queue\Exception;

/**
 * Class Broker
 * @package HelloPrint\Workers
 */
class Broker extends AbstractWorker
{
    /**
     * @throws Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function createHiMessage()
    {
        $this->emit(EventSource::BROKER, 'Hi,');
    }


    public function initPulling(?WorkerOptions $options): void
    {
        while (true)
        {
            try
            {
                $request = $this->pulling(EventSource::BROKER);

                if (!$request->isEmptyMessage()) {

                    $response = $request->store();

                    echo $request->getJsonBody();

                    if ($request->isFromA()) {
                        $this->emit(EventSource::TOPIC_B, $request->getJsonBody());
                    } else if ($request->isFromBroker()) {
                        $this->emit(EventSource::TOPIC_A, $request->getJsonBody());
                    }
                }

            } catch (\Exception $e) {
                self::displayError($e->getMessage(), $options->verbose);
            }
        }
    }
}