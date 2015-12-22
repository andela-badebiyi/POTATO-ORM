<?php

namespace app\database;

use app\exceptions\TableNotFoundException;

/**
 * Interacts with the database
 * This class is used by the Model base class, its acts as an interface between
 * the model class and the database. This handles all the necessary query operations
 * and helps abstract it from the model class.
 *
 * @author Adebiyi Bodunde
 */
class Db
{
    public static $db;
    private $error;
    private $stmt;
    private $tableName;
    private $db_type;

    /**
     * The Db constructor method
     * The connection to the database is established here and the existence of
     * the required database table is verified here.
     *
     * @param [[string]] $tableName [[Name of the database table]]
     *
     * @throws [[Exception]] Throws an exception if table doesnt exist
     *
     * @return [[object]] [[Returns the Db object]]
     */
    public function __construct($tableName)
    {
        //read database configurations from file and get table name
        $config = parse_ini_file('config.ini');
        $this->tableName = $tableName;

        //get database type
        $this->db_type = $config['dbtype'];

        //connect to database
        if (static::$db === null) {
            try {
                if ($this->db_type === 'sqlite') {
                    static::$db = new \PDO('sqlite:database/'.$config['sqlite_file']);
                    static::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else {
                    static::$db = new \PDO(
                        'mysql:host='.$config['host'].';dbname='.$config['dbname'].';',
                        $config['user'],
                        $config['pass']
                    );
                }
            } catch (\PDOException $e) {
                $this->error = $e->getMessage();
            }
        }

        //confirm that table exists
        try {
            $this->stmt = static::$db->prepare('select 1 from '.$tableName);
            $result = $this->stmt->execute();
        } catch (\PDOException $e) {
            throw new TableNotFoundException('Table does not exist');
        }

        //return db instance
        return $this;
    }

    /**
     * Gets all the column names of the table.
     *
     * @return [[array]] returns an array that contains the names of all the columns in the table
     */
    public function getColumns()
    {
        // get column names
        if ($this->db_type === 'sqlite') {
            $query = static::$db->prepare("pragma table_info($this->tableName)");
            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $r) {
                $table_names[] = $r['name'];
            }
        } else {
            $query = static::$db->prepare('DESC '.$this->tableName);
            $query->execute();
            $table_names = $query->fetchAll(\PDO::FETCH_COLUMN);
        }

        return $table_names;
    }

    /**
     * Inserts a new record into the database
     * This method takes an associative array where the keys are the table fields and
     * the values are the data we want to insert inside our table in the database.
     *
     * @param [[array]] $data Associative array containing the data we wish to insert
     *
     * @return [[boolean]] returns false on failure and true on success
     */
    public function insert($data)
    {
        $query = QueryConstructor::constructInsertQuery($data, $this->tableName);
        $this->stmt = static::$db->prepare($query);

        //construct the placeholder
        foreach ($data as $key => $value) {
            $new_data[":$key"] = $value;
        }

        return $this->stmt->execute($new_data);
    }

    /**
     * Updates an existing record into the database.
     *
     * @param [[array]]  $data      Associative array containing the data we wish to update
     * @param [[string]] $condition The condition needed to locate the record we wish to update
     *
     * @return [[boolean]] returns false on failure and true on success
     */
    public function update($data, $condition)
    {
        $query = QueryConstructor::constructUpdateQuery($data, $condition, $this->tableName);
        $this->stmt = static::$db->prepare($query);

        return $this->stmt->execute();
    }

    /**
     * Finds a record in the database based on the condition provided.
     *
     * @param [[string]]  $condition Condition required to find record in database
     * @param [[Boolean]] $all       Find option, finds all record when true and find just the first when false
     *
     * @return [[array] returns an associative array of the query
     */
    public function findWhere($condition = '', $all = false)
    {
        $query = QueryConstructor::constructSelectQuery($condition, $this->tableName);
        $this->stmt = static::$db->prepare($query);

        if ($this->stmt->execute()) {
            if ($all === false) {
                $output = $this->stmt->fetch();
            } else {
                $output = $this->stmt->fetchAll();
            }

            if ($output === false) {
                return;
            } else {
                return $output;
            }
        } else {
            return;
        }
    }

    /**
     * Deletes record from database.
     *
     * @param [[string]] $condition Condition required to find record to be deleted
     *
     * @return bool returns true on success and false on failure
     */
    public function deleteRecord($condition)
    {
        $query = QueryConstructor::constructDeleteQuery($condition, $this->tableName);
        $this->stmt = static::$db->prepare($query);
        $this->stmt->execute();

        if ($this->stmt->rowCount() === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the id of the last inserted record.
     *
     * @return [[integer]] Id of the last record that was inserted
     */
    public function lastInsertedId()
    {
        return intval(static::$db->lastInsertId());
    }

    /**
     * Closes the pdo connection to the database.
     */
    public function close()
    {
        static::$db = null;
    }
}
