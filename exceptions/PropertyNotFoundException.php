<?php

namespace app\exceptions;

/**
 * Custom exception class used by the Model
 * This exception is thrown when a user tries to access
 * a property that does not exist e.g the column name of a table
 * that doesn't exist in the database.
 */
class PropertyNotFoundException extends \Exception
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
        $errorMsg = "This property doesn't exist";

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
