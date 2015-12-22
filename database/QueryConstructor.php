<?php
namespace app\database;

/**
 * This Class constructs sql query strings
 * This class is used by the db class. It is used to correctly construct
 * sql query strings
 * @author Adebiyi Bodunde
 */

class QueryConstructor{
    /**
     * Constructs an sql insert query strings
     * @param  [[array]] $data  [[An associative array of the data we wish to insert into the database]]
     * @param  [[string]] $tableName [[name of the database table]]
     * @return [[string]] [[returns the constructed insert query string]]
     */
    public static function constructInsertQuery($data, $tableName)
    {
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[":$key"] = $value;
        }

        $fields = implode(", ", array_keys($data));
        $values = implode(", ", array_keys($new_data));
        $query = "Insert into ". $tableName."(".$fields.") Values(".$values.")";
        return $query;
    }
    
    /**
     * Constructs an sql select query string
     * @param  [[string]] $condition [[The condition used by the select query]]
     * @param  [[string]] $tableName [[name of the database table]]
     * @return [[string]] [[returns the constructed select query string]]
     */
    public static function constructSelectQuery($condition, $tableName)
    {
        $query = "Select * from ".$tableName;
        if ($condition !== "") {
            $query .= " where ". $condition;
        }
        return $query;
    }

    /**
     * Constructs an sql delete query string
     * @param  [[string]] $condition [[The condition used by the delete query]]
     * @param  [[string]] $tableName [[name of the database table]]
     * @return [[string]] [[returns the constructed delete query string]
     */
    public static function constructDeleteQuery($condition, $tableName)
    {
        $query = "Delete from ".$tableName." where ".$condition;
        return $query;
    }

    /**
     * Constructs an sql update query string
     * @param [[array]] $data [[An associative array of the data we wish to update in the database]
     * @param  [[string]] $condition [[The condition used by the update query]]
     * @param  [[string]] $tableName [[name of the database table]]
     * @return [[string]] [[returns the constructed update query string]
     */
    public static function constructUpdateQuery($data, $condition, $tableName)
    {
        foreach ($data as $key => $value) {
            if (gettype($value) == 'string') {
                $set_str[] = "$key = '$value'";
            } else {
                $set_str[] = "$key = $value";
            }
        }
        $set_str = implode(", ", $set_str);

        $query = "Update ".$tableName." SET ".$set_str." where ".$condition;
        return $query;
    }
}