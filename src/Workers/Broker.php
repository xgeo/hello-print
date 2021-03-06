<?php

namespace HelloPrint\Workers;

use HelloPrint\Core\AbstractWorker;
use HelloPrint\Core\WorkerOptions;
use HelloPrint\Enums\EventSource;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

/**
 * Class Broker
 * @package HelloPrint\Workers
 */
class Broker extends AbstractWorker
{
    /**
     * @param string $from
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function createHiMessage(string $from = EventSource::INITIALIZER)
    {
        $this->emit(EventSource::BROKER, json_encode(['message' => 'Hi,', 'from' => $from]));
    }

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

                    $this->display($request->getJson());

                    if ($request->isFromA()) {
                        $this->emit(EventSource::TOPIC_B, json_encode([
                            'message' => $request->getMessage(),
                            'from' => EventSource::BROKER
                        ]));
                    } else if ($request->isFromInitializer()) {
                        $jsonMessage = $this->save($request);
                        $this->emit(EventSource::TOPIC_A, json_encode($jsonMessage));
                    } else if ($request->isFromB()) {
                        $this->save($request);
                    }
                }
            } catch (\Exception $e) {
                self::displayError($e->getMessage(), $options->verbose);
            }
        }
    }

    /**
     * @param $request
     * @return array
     */
    final private function save($request)
    {
        $response = $request->store();

        return [
            'message' => $request->getMessage(),
            'from' => EventSource::BROKER,
            'id' => $response->id
        ];
    }
}