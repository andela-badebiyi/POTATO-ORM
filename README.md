[![StyleCI](https://styleci.io/repos/48433248/shield)](https://styleci.io/repos/48433248)
[![Build Status](https://travis-ci.org/andela-badebiyi/checkpoint-2.svg)](https://travis-ci.org/andela-badebiyi/checkpoint-2)
[![Coverage Status](https://coveralls.io/repos/andela-badebiyi/checkpoint-2/badge.svg?branch=develop&service=github)](https://coveralls.io/github/andela-badebiyi/checkpoint-2?branch=develop)

#POTATO ORM

##Summary
This is my third checkpoint for my andela simulations. This project is a poor man's ORM called the Potato ORM that provides an easy and elegant way to interact with a database.

The `src` directory contains the base model class of our ORM. Inside the `database` directory contains the `Db` class used by the base model to connect and query the database, also there's the `QueryConstructor` class used by the `Db` class to easily construct queries and finally there's the `test.db` sqlite database file used by out test classes.

The `tests` directory contains all our phpunit tests while the `exceptions` directory holds the classes of all our custom exceptions that are generated in the Base Model and Db class.



##Usage

```php
class MyModel extends Model
{
  /**
   * Model attributes
   * name string
   * email string
   * password string
   */
  protected $table_name = "guest";
}

//saving a new record
$myModel = new MyModel;
$myModel->name = "John Doe";
$myModel->email = "j_doe@andela.com";
$myModel->password = "pass1234";

$myModel->save(); //returns false if saving failed and returns id of record if successful

//find a record using primary key
MyModel::find(1); //where 1 is the id of the record in the database

//find a record using sql where clause condition
MyModel::findWhere("name = 'John Doe', true); 
//setting second argument to true returns all records, not setting it returns just the first

//get all records
MyModel::getAll();

//updating a record
$record = MyModel::find(1);
$record->email = 'new_email@andela.com'; //update email field
$record->save();

//deleting a recod
MyModel::destroy(1) //where 1 is the id of the record
```
##Installation
From your root directory run `composer install`. This would install all the necessary dependencies

##Requirements

* [PHP](http://php.net/releases/5_4_0.php)
* [PHPUnit](https://phpunit.de/)

##Testing
Move to your root directory in your terminal and run `phpunit`

##Database Configuration
In the `database` directory locate the `config.ini` file and set your database configuration information
