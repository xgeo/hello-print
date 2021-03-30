<?php
namespace HelloPrint\Core;

/**
 * Class DB
 * @package HelloPrint\Core
 */
class DB extends \PDO
{
    protected $table;

    /**
     * DB constructor.
     */
    public function __construct()
    {
        $dsn        = getenv('DB_DSN');
        $username   = getenv('DB_USERNAME');
        $passwd     = getenv('DB_PASSWORD');
        parent::__construct($dsn, $username, $passwd);
    }

    /**
     * @param $data
     * @return \stdClass|null
     */
    public function create($data)
    {
        $dataInserted   = null;

        $data['created_at'] = (new \DateTime())->format(DATE_ISO8601);
        $SQL                = $this->getInsertSQL($data);
        $statement          = $this->prepare($SQL);
        $isCreated          = $statement->execute($data);

        if ($isCreated) {
            $id             = $this->lastInsertId();
            $SQL            = $this->getSelectSQL('*', "id = :id");
            $statement      = $this->prepare($SQL);
            $statement->execute(['id' => $id]);
            $dataInserted   = $statement->fetch(\PDO::FETCH_ASSOC);
        }

        return $dataInserted;
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = (new \DateTime())->format(DATE_ISO8601);
        $SQL                = $this->getUpdateSQL($data, "id = :id");
        $statement          = $this->prepare($SQL);
        $data['id']         = $id;

        return $statement->execute($data);
    }

    /**
     * @param string $columns
     * @param string|null $where
     * @return string
     */
    public function getSelectSQL(string $columns = '*', string $where = null)
    {
        $sql = "SELECT {$columns} FROM {$this->table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        return $sql;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getInsertSQL(array $data): string
    {
        return "INSERT INTO {$this->table} ({$this->getBindValues($data)}) VALUES ({$this->getBindValues($data, ":")})";
    }

    /**
     * @param array $data
     * @param string $where
     * @return string
     */
    private function getUpdateSQL(array $data, string $where): string
    {
        return "UPDATE {$this->table} SET {$this->getUpdateBindValues($data, ":")} WHERE {$where}";
    }

    /**
     * @param array $data
     * @param string $bind
     * @return string
     */
    private function getUpdateBindValues(array $data, string $bind = "")
    {
        $bindings = [];

        foreach ($data as $key => $v)
        {
            $key = strtolower($key);
            array_push($bindings, "{$key} = {$bind}{$key}");
        }

        return implode(', ', $bindings);
    }

    /**
     * @param array $data
     * @param string $bind
     * @return string
     */
    private function getBindValues(array $data, string $bind = "")
    {
        $bindings = [];

        foreach ($data as $key => $v)
        {
            $key = strtolower($key);
            array_push($bindings, "{$bind}{$key}");
        }

        return implode(', ', $bindings);
    }

}