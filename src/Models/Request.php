<?php

namespace HelloPrint\Models;
use HelloPrint\Core\DB;

class Request extends DB
{
    protected $table = 'requests';

    public int $id          = 0;
    public string $message  = "";

    public function findById(int $id)
    {
        $SQL            = $this->getSelectSQL('*', "id = :id");
        $statement      = $this->prepare($SQL);
        $statement->execute(['id' => $id]);
        return $statement->fetch(\PDO::FETCH_OBJ);
    }
}