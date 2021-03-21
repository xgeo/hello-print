<?php


class DB extends PDO
{
    protected $table;

    public function __construct()
    {
        $dsn        = getenv('DB_DSN');
        $username   = getenv('DB_USERNAME');
        $passwd     = getenv('DB_PASSWORD');
        $options    = getenv('DB_OPTIONS');
        parent::__construct($dsn, $username, $passwd, $options);
    }

    public function create($data): bool
    {
        $SQL        = $this->getInsertSQL($data);
        $statement  = $this->prepare($SQL);

        return $statement->execute(array_values($data));
    }

    private function getInsertSQL(array $data): string
    {
        return "INSERT INTO {$this->table} VALUES ({$this->getBindValues($data)})";
    }

    private function getBindValues(array $data)
    {
        $bindings = [];
        for ($i = 0; $i < sizeof($data); $i++)
        {
            array_push($bindings, "?");
        }

        return implode(', ', $bindings);
    }
}