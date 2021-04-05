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
        $this->emit(EventSource::BROKER, json_encode(['message' => 'Hi,', 'from' => EventSource::BROKER]));
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

                    $jsonMessage = [
                        'message' => $request->getJsonBody(),
                        'from' => EventSource::BROKER,
                        'id' => $response->id
                    ];

                    if ($request->isFromA()) {
                        $this->emit(EventSource::TOPIC_B, json_encode($jsonMessage));
                    } else if ($request->isFromBroker()) {
                        $this->emit(EventSource::TOPIC_A, json_encode($jsonMessage));
                    }
                }

            } catch (\Exception $e) {
                self::displayError($e->getMessage(), $options->verbose);
            }
        }
    }
}