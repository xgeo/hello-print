<?php


namespace HelloPrint\Core;


use HelloPrint\Models\Request;

class MessageStrategy
{
    protected Request $request;
    private RandomNames $randomize;
    private bool $isRandomize = false;
    private string $append = "start";

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->randomize = new RandomNames();
    }

    public function process(string $messageBody, ?string $appendToMessage): \stdClass
    {
        $object = new \stdClass();

        list ($id, $message) = $this->processMessageId($messageBody, $appendToMessage);

        if (!is_null($message)) {
            $object = $this->saveMessage($message, $id);
        }

        return $object;
    }

    public function setRandomize(bool $isRandomize)
    {
        $this->isRandomize = $isRandomize;
    }

    public function setAppend(string $append)
    {
        $this->append = $append;
    }

    private function processMessageId(string $messageBody, ?string $appendToMessage)
    {
        $id = null;

        if (!empty($messageBody) && strpos($messageBody, "id=") !== false)
        {
            $values         = explode("=", $messageBody);
            $id             = (int) $values[1];
            $requestEntity  = $this->request->findById($id);

            if ($requestEntity && !is_null($appendToMessage)) {

               if ($this->append === "start") {
                    $messageBody = "{$requestEntity->message} {$appendToMessage}";
                } else {
                    $messageBody = "{$appendToMessage} {$requestEntity->message}";
                }

            } else {
                $messageBody = null;
            }

        } else if ($this->isRandomize) {
            $messageBody = "{$messageBody} {$this->randomize->getName()}";
        }

        return [
            $id,
            $messageBody
        ];
    }

    private function saveMessage(string $message, $id = null): \stdClass
    {
        $data = [
            'message'   => $message
        ];

        if (is_null($id)) {
            $data = $this->request->create($data);
        } else {
            $this->request->update($id, $data);
        }

        return (object) $data;
    }
}