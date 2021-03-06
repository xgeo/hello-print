<?php
namespace HelloPrint\Workers;

use Exception;
use HelloPrint\Core\AbstractWorker;
use HelloPrint\Core\RandomNames;
use HelloPrint\Core\WorkerOptions;
use HelloPrint\Enums\EventSource;

/**
 * Class Worker
 * @package HelloPrint\Core
 */
class WorkerA extends AbstractWorker
{

    /**
     * @param WorkerOptions|null $options
     * @throws \Interop\Queue\Exception
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
                    $randomName = new RandomNames();
                    $message = $request->concat($randomName->getName());
                    $from = $options->topic;
                    $this->emit(EventSource::BROKER, json_encode(compact('message', 'from')));
                }

            } catch (Exception $e) {
                self::displayError($e->getMessage(), $options->verbose);
            }
        }
    }
}