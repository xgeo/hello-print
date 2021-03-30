<?php
namespace HelloPrint\Contracts;

interface ConsumerInterface
{
    public function subscribe(string $topic, int $timeout);
}