<?php
namespace HelloPrint\Contracts;

interface ConsumerInterface
{
    public function subscribe(string $topic);
}