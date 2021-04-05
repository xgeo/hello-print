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

    private ?\stdClass $details;

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

    public function isFromBroker(): bool
    {
        return $this->json->from === EventSource::BROKER;
    }

    /**
     * @return bool
     */
    public function isEmptyMessage(): bool
    {
        return empty($this->getJsonBody());
    }

    /**
     * @return string|null
     */
    public function getJsonBody(): ?string
    {
        return $this->json->body;
    }

    /**
     * @return mixed
     */
    public function getJsonBodyObject(): ?\stdClass
    {
        return json_decode($this->getJsonBody(), false);
    }

    /**
     * @param string $message
     * @return string
     * @throws \Exception
     */
    public function concat(string $message): string
    {
        $msg = $this->getJsonBody();

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
        return $this->saveMessage($this->getJsonBody());
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