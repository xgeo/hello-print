<?php
declare(strict_types=1);

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

    public function initPulling(?WorkerOptions $options): void
    {
        while (true)
        {
            try
            {
                $request = $this->pulling($options->topic, $options->ms);

                if (!$request->isEmptyMessage()) {
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