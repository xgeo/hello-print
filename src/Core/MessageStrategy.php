<?php
namespace HelloPrint\Core;

use HelloPrint\Models\Request;

/**
 * Class MessageStrategy
 * @package HelloPrint\Core
 */
class MessageStrategy
{
    protected Request $request;
    private RandomNames $randomize;
    private bool $isRandomize = false;
    private string $append = "start";

    /**
     * MessageStrategy constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->randomize = new RandomNames();
    }

    /**
     * @param string $messageBody
     * @param string|null $appendToMessage
     * @return \stdClass|null
     */
    public function store(string $messageBody, ?string $appendToMessage): ?\stdClass
    {
        list($id, $message) = $this->listMessageProperties($messageBody, $appendToMessage);
        return $this->saveMessage($message ?: $appendToMessage, $id);
    }

    /**
     * @param bool $isRandomize
     */
    public function setRandomize(bool $isRandomize)
    {
        $this->isRandomize = $isRandomize;
    }

    /**
     * @param string $append
     */
    public function setAppend(string $append)
    {
        $this->append = $append;
    }

    /**
     * @param string|int|null $messageBody
     * @param string|null $appendToMessage
     * @return array
     */
    final private function listMessageProperties($messageBody, ?string $appendToMessage)
    {
        $id = null;
        $responseMessage = null;

        if (is_numeric($messageBody))
        {
            $requestEntity  = $this->request->findById((int) $messageBody);

            if ($requestEntity && !is_null($appendToMessage)) {

                $id = (int) $messageBody;

               if ($this->append === "start") {
                   $responseMessage = "{$requestEntity->message} {$appendToMessage}";
                } else {
                   $responseMessage = "{$appendToMessage} {$requestEntity->message}";
                }

            }

        } else if ($this->isRandomize) {
            $responseMessage = "{$messageBody} {$this->randomize->getName()}";
        }

        return [
            $id,
            $responseMessage
        ];
    }

    /**
     * @param string|null $message
     * @param int|null $id
     * @return \stdClass|null
     */
    private function saveMessage(?string $message, ?int $id): ?\stdClass
    {
        $data = null;

        if (!is_null($message)) {
            $insert = [
                'message' => $message
            ];

            if (is_null($id)) {
                $data = $this->request->create($insert);
            } else {
                $isUpdated = $this->request->update((int) $id, $insert);
                $data = $isUpdated ? (object) compact('id','message') : null;
            }
        }

        return $data;
    }
}