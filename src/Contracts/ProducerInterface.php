<?php


namespace HelloPrint\Contracts;

/**
 * Interface ProducerInterface
 * @package HelloPrint\Contracts
 */
interface ProducerInterface
{
    public function createMessage(string $topic, string $message);
}