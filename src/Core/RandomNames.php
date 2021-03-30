<?php


namespace HelloPrint\Core;


class RandomNames
{
    private array $names = [];

    public function __construct()
    {
        $listNames      = "Joao, Bram, Gabriel, Fehim, Eni, Patrick, Micha, Mirzet, Liliana, Sebastien";
        $listNames      = str_replace(" ", "", $listNames);
        $this->names    = explode(',', $listNames);
    }

    public function getName(): string
    {
        return $this->names[$this->getRandomIndex()];
    }

    private function getRandomIndex()
    {
        $indexes = array_keys($this->names);
        $last = end($indexes);

        return rand(0, $last);
    }
}