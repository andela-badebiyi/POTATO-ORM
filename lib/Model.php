<?php

namespace app;

use app\database\Db;
use app\exceptions\PropertyNotFoundException;

/**
 * Base Model class
 * This is an ORM that provides users with an elegant and easy way to interact with
 * a database.
 *
 * @author Adebiyi Bodunde
 */
class Model
{
    private $columns;
    protected $table_name;
    private $db;

    /**
     * Model Constructor method.
     *
     * @param [[string]] $tableName name of the required table in the database
     */
    public function __construct($tableName = null)
    {
        //get table name
        if (!isset($this->table_name)) {
            if ($tableName === null) {
                $this->table_name = get_class($this);
            } else {
                $this->table_name = $tableName;
            }
        }

        //connect to database
        $this->db = new Db(strtolower($this->table_name));

        //retrieve columns
        $this->columns = $this->db->getColumns();
    }

    /**
     * Magic method to retrieve data from table column
     * This magic method creates dynamic class properties, these properties
     * would be the name of the columns in the table.
     *
     * @param [[string]] $name [[name of dynamic class property]]
     *
     * @return [[string]]
     */
    public function __get($name)
    {
        if (in_array($name, $this->columns)) {
            return $this->$name;
        } else {
            throw new PropertyNotFoundException('Property does not exist');
        }
    }

    /**
     * Magic method to set values that would be stored in the database
     * This magic method creates dynamic class properties, these properties
     * would be the name of the columns in the table.
     *
     * @param [[string]] $name [[name of dynamic class property]]
     * @parm [[string|int]] $value [[data to be stored]]
     *
     * @return [[string]]
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->columns)) {
            $this->$name = $value;
        } else {
            throw new PropertyNotFoundException('Property does not exist');
        }
    }

    /**
     *  Retrieves the name of the table.
     *
     *  @returns [[string]] returns the name of the table
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * To find a record in the database.
     *
     * @param [[int]] $id primary key of the record we are searching for
     *
     * @return [[Model]] returns the model object of the record
     */
    public static function find($id)
    {
        $tbl_name = static::staticMethodInitializer();

        //connect to db and get table feilds
        $db = new Db(strtolower($tbl_name));
        $columns = $db->getColumns();

        //get retrieve record
        $record = $db->findWhere('id = '.$id);

        //if transaction failed return null else return object
        if ($record === false) {
            return null;
        } else {
            $obj = new self($tbl_name);
            foreach ($columns as $name) {
                $obj->$name = $record[$name];
            }

            return $obj;
        }
    }

    /**
     * Find a record using the where clause.
     *
     * @param [[string]]  $cond The condition to be used in the where clause 
     * @param [[boolean]] $opt  when set to true it finds all the records, when set to false it finds just the first
     *
     * @return [[Model]] returns a model object   
     */
    public static function findWhere($cond, $opt = false)
    {
        $tbl_name = static::staticMethodInitializer();

        //connect to db and get table fields
        $db = new Db(strtolower($tbl_name));
        $columns = $db->getColumns();

        //find record
        $records = $db->findWhere($cond, $opt);

        //check if all records are to be returned or just the first
        if ($opt) {
            foreach ($records as $record) {
                $obj = new self($tbl_name);
                foreach ($columns as $name) {
                    $obj->$name = $record[$name];
                }
                $objects[] = $obj;

                return $objects;
            }
        } else {
            $obj = new self($tbl_name);
            foreach ($columns as $name) {
                $obj->$name = $records[$name];
            }

            return $obj;
        }
    }

    /**
     * gets all the record in the database.
     *
     * @return [[array]] returns an array of the data objects of each record
     */
    public static function getAll()
    {
        $tbl_name = static::staticMethodInitializer();

        //connect to db and get table fields
        $db = new Db(strtolower($tbl_name));
        $columns = $db->getColumns();

        //pull all records for database
        $records = $db->findWhere('', true);

        //return all records
        foreach ($records as $record) {
            $obj = new self($tbl_name);
            foreach ($columns as $name) {
                $obj->$name = $record[$name];
            }
            $objects[] = $obj;
        }

        return $objects;
    }

    /**
     * Saves a record to a database.
     *
     * @return [[Boolean]] returns true on success and false on failure
     */
    public function save()
    {
        //get table fields
        $columns = $this->db->getColumns();

        //construct the array of the data to be saved
        foreach ($columns as $col) {
            if ($col === 'id' || $this->$col === null) {
                continue;
            }
            $data[$col] = $this->$col;
        }

        //if its a new record then save. If its an existing record then update
        if (!isset($this->id)) {
            $res = $this->db->insert($data);
        } else {
            $res = $this->db->update($data, 'id = '.$this->id);
        }

        //return result of the transaction {true|false}
        if ($res === true) {
            return $this->db->lastInsertedId();
        } else {
            return false;
        }
    }

    /**
     * Deletes a record from the database.
     *
     * @param [[Integer]] $id [[Description]]
     *
     * @return [[Boolean]] returns true on success and false on failure
     */
    public static function destroy($id)
    {
        $tbl_name = static::staticMethodInitializer();

        //connect to database
        $db = new Db(strtolower($tbl_name));

        //delete record
        $res = $db->deleteRecord('id = '.$id);

        //return true or false depending on success or failure
        return $res;
    }

    /**
     * Retrieves returns table name
     * Gets the correct table name for the static methods.
     *
     * @return [[string]] the table name
     */
    private static function staticMethodInitializer()
    {
        $class_name = get_called_class();
        $class_properties = get_class_vars($class_name);

        //get the name of the table
        if (isset($class_properties['table_name'])) {
            return $class_properties['table_name'];
        } else {
            return $class_name;
        }
    }
}
