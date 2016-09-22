<?php

namespace app\exceptions;

/**
 * Custom exception class
 * this custom exception is thrown when a user tries to create a model
 * for a table that doesn't exist in the database.
 *
 * @author Adebiyi Bodunde
 */
class TableNotFoundException extends \Exception
{
    /**
     * Overrides the parent constructor method.
     */
    public function __construct($message = 'Table does not exist', $code = 1)
    {
        parent::__construct($message, $code);
    }

}
