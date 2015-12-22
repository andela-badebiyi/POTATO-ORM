<?php
namespace app\exceptions;
/**
 * Custom exception class
 * this custom exception is thrown when a user tries to create a model
 * for a table that doesn't exist in the database
 * @author Adebiyi Bodunde
 */

class TableNotFoundException extends \Exception
{
    /**
     * Overrides the parent constructor method.
     */
    public function __construct($message = null, $code = 1)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return string Returns the error message that accompanies the custom exception
     */
    public function errorMessage()
    {
        $errorMsg = "This table does not exist in the database";

        return $errorMsg;
    }

    /**
     * @return int Returns the error code that accompanies the custom exception
     */
    public function errorCode()
    {
        return 1;
    }
}