<?php

namespace HelloPrint\Core;
use HelloPrint\Enums\EventSource;
use HelloPrint\Enums\ResponseMessage;
use HelloPrint\Models\Request;

/**
 * Class MessageStrategy
 * @package HelloPrint\Core
 */
class WorkerMessage
{
    /**
     * @var bool|null
     */
    public ?bool $isBrokerMessage;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var \stdClass|null
     */
    private ?\stdClass $json;

    /**
     * WorkerMessage constructor.
     * @param Request $request
     * @param \stdClass $json
     */
    public function __construct(Request $request, \stdClass $json)
    {
        $this->request = $request;
        $this->json = $json;
    }

    public function isFromA(): bool
    {
        return $this->json->from === EventSource::TOPIC_A;
    }

    public function isFromB(): bool
    {
        return $this->json->from === EventSource::TOPIC_B;
    }

    public function isFromInitializer(): bool
    {
        return $this->json->from === EventSource::INITIALIZER;
    }

    public function isFromBroker(): bool
    {
        return $this->json->from === EventSource::BROKER;
    }

    /**
     * @return bool
     */
    public function isEmptyMessage(): bool
    {
        return is_null($this->json) && is_null($this->json->message) && empty($this->json->message);
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->json->message;
    }

    /**
     * @return mixed
     */
    public function getJson(): ?\stdClass
    {
        return $this->json;
    }

    /**
     * @param string $message
     * @return string
     * @throws \Exception
     */
    public function concat(string $message): string
    {
        $msg = $this->getMessage();

        if (is_null($msg)) {
            throw new \Exception(ResponseMessage::EMPTY_MESSAGE_BODY);
        }

        return $msg . " " . $message;
    }

    /**
     * @return Request|null
     */
    public function store(): ?Request
    {
        return $this->saveMessage($this->getMessage());
    }

    /**
     * @param string|null $message
     * @return Request|null
     */
    private function saveMessage(?string $message): ?Request
    {
        $data = null;

        if (!is_null($message)) {
            $insert = [
                'message' => $message
            ];

            $data = $this->request->create($insert);
        }

        return $data;
    }
}