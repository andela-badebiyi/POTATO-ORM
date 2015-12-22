<?php
namespace app\tests;

use app\MyModel;


class MyModelTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorWithValidTable()
	{
		$myModel = new MyModel;
		$this->assertInstanceOf('app\Model', $myModel);
		$this->assertObjectHasAttribute('table_name', $myModel);
		$this->assertNotNull($myModel);
	}

	/**
	 * @expectedException app\exceptions\PropertyNotFoundException
	 */
	public function testForInvalidModelFields()
	{
		$myModel = new MyModel;
		$myModel->invalidField;
	}

	public function testGetTableName()
	{
		$myModel = new MyModel;
		$this->assertNotNull($myModel->getTableName());
		$this->assertInternalType('string', $myModel->getTableName());
		$this->assertSame('guest', strtolower($myModel->getTableName()));
	}

	public function testFind()
	{
		//find record that doesn't exist
		$this->assertNull(MyModel::find(231));

		//find a record that exists
		$rec = MyModel::find(3);
		$this->assertNotNull($rec);
		$this->assertInstanceOf('app\Model', $rec);
		$this->assertInternalType('string', $rec->name);
	}

	public function testFindWhere()
	{
		//get first record 
		$record = MyModel::getAll()[0];

		//find using condition
		$rec = MyModel::findWhere("name = '".$record->name."'");

		$this->assertEquals($record->name, $rec->name);
	}

	public function testGetAll()
	{
		//get all records
		$records = MyModel::getAll();

		$this->assertNotNull($records);
		$this->assertInternalType('array', $records);
	}

	public function testSave()
	{
		//insert new record
		$record = new MyModel;
		$record->name = "Bodunde";
		$record->email = "bodunadebiyi@gmail.com";
		$record->password = "pass1234";

		//save record and store the return of the save operation which is the 
		//id and primary key of the stored record
		$record_id = $record->save();

		//retrieve record to test that it saved
		$rec = MyModel::find($record_id);
		$this->assertNotNull($rec);
		$this->assertEquals("Bodunde", $rec->name);
		$this->assertEquals("bodunadebiyi@gmail.com", $rec->email);
		$this->assertEquals("pass1234", $rec->password);

		//test update
		$new_record = MyModel::find($record_id);
		$new_record->password = "password!@#123";
		$new_record->save();

		$this->assertEquals("password!@#123", $new_record->password);
	}

	public function testDestroy()
	{
		//save record and get its id
		$record = new MyModel;
		$record->name= "John Wayne";
		$record->email = "jwayne@gmail.com";
		$record->password = "pass1234";
		$id = $record->save();

		//retrieve record
		$this->assertNotNull(MyModel::find($id));

		//delete record and check that it has been deleted
		MyModel::destroy($id);

		$this->assertNull(MyModel::find($id));
	}

}