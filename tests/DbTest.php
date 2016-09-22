<?php
namespace app\tests;

use app\database\Db;
use app\database\QueryConstructor;

class DbTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException app\exceptions\TableNotFoundException
	 * @expectedExceptionMessage Table does not exist
	 * @expectedErrorCode 1
	 */
	public function testForNonExistentTable()
	{
		$DBH = new Db('house');
	}

	public function testForValidTable()
	{
		$DBH = new Db('guest');
		$this->assertInstanceOf('app\database\Db', $DBH);
		$this->assertObjectHasAttribute('error', $DBH);
		$this->assertObjectHasAttribute('stmt', $DBH);
		$this->assertObjectHasAttribute('tableName', $DBH);
		$this->assertObjectHasAttribute('db_type', $DBH);
		$this->assertNotEquals(null, $DBH);
	}

	public function testGetColumns()
	{
		$DBH = new Db('guest');
		$expected = ['id', 'name', 'email', 'password'];

		$this->assertInternalType('array', $DBH->getColumns());
		$this->assertEquals($expected, $DBH->getColumns());
	}

	public function testInsert()
	{
		$DBH = new Db('guest');
		//find the count of all the records in the database
		$current_count = count($DBH->findWhere("", true));

		//insert new record
		$new_record = ['name' => 'Jane Douy', 'email' => 'Jane Douy', 'password' => 'janey!@#123'];
		$res = $DBH->insert($new_record);

		//check that the result of the insert operation is true
		$this->assertTrue($res);
		//check that the count of all the record in the database increased by 1
		$this->assertEquals($current_count + 1, count($DBH->findWhere("", true)));
	}

	public function testFindWhere()
	{
		$DBH = new Db('guest');

		//test findAll that it returns an array of results
		$results = $DBH->findWhere("", true);
		$this->assertInternalType('array', $results);
		$this->assertNotEmpty($results);
		$this->assertNotNull($results);

		//if there are records in the table verify that it contains the correct fields
		$col = $DBH->getColumns();
		if (count($results) > 0) {
			foreach ($col as $field) {
				$this->assertArrayHasKey($field, $results[0]);
			}
		}

		//Check that finding a record that doesn't exist returns null
		$rec = $DBH->findWhere("name = 'invalid Name'");
		$this->assertNull($rec);

		$rec_2 = $DBH->findWhere("id = 1000");
		$this->assertNull($rec_2);

		//find a record that exists
		$new_record = ['name' => 'Niccolo Machiavelli', 'email' => 'niccolo@theprince.com', 'password' => 'rarasuncara'];
		$DBH->insert($new_record);
		$get_record = $DBH->findWhere("name = 'Niccolo Machiavelli'");
		$this->assertNotNull($get_record);
		$this->assertInternalType('array', $get_record);
		$this->assertEquals('niccolo@theprince.com', $get_record['email']);
	}

	public function testUpdate()
	{
		$DBH = new Db('guest');

		/* insert a new record in the database if the database is empty
		* if the database is NOT empty then just a get the first record in
		* the database 
		*/

		if (count($DBH->findWhere("", true)) == 0 ){
			//insert new record
			$new_record = ['name' => 'Anthony Nandaa', 'email' => 'anandaa@andela.com', 'password' => 'phpseniorevangelist'];
			$DBH->insert($new_record);

			//get record to confirm it inserted successfully
			$rec = $DBH->findWhere('name = "Anthony Nandaa"');
			$this->assertEquals('anandaa@andela.com', $rec['email']);

			//update record
			$new_update = ['email' => 'anthonyN@gmail.com'];
			$DBH->update($new_update, 'name = "Anthony Nandaa"');

			//confirm update
			$rec = $DBH->findWhere('name = "Anthony Nandaa"');
			$this->assertEquals('anthonyN@gmail.com', $rec['email']);
		} else {
			//get the first record in the database
			$rec = $DBH->findWhere('');
			//update record
			$new_update = ['email' => 'anthonyN@gmail.com'];
			$DBH->update($new_update, "name = '".$rec['name']."'");

			//confirm update
			$rec = $DBH->findWhere("name = '".$rec['name']."'");
			$this->assertEquals('anthonyN@gmail.com', $rec['email']);
		}
	}

	public function testDelete()
	{
		$DBH = new Db('guest');

		//insert a new record and confirm it exists		
		$new_record = ['name' => 'Test Name', 'email' => 't_name@andela.com', 'password' => 'pass123'];
		$DBH->insert($new_record);
		$this->assertNotNull($DBH->findWhere('name = "Test Name"'));

		//delete the new record
		$DBH->deleteRecord("name = 'Test Name'");
		$this->assertNull($DBH->findWhere("name = 'Test Name'"));
	}

	public function testLastInsertedId(){
		$DBH = new Db('guest');
		$this->assertInternalType('int', $DBH->lastInsertedId());
	}
}