<?php
namespace app\tests;

use app\database\QueryConstructor;


class QueryConstructorTest extends \PHPUnit_Framework_Testcase
{
	public $table_name;
	public $data;

	public function setUp()
	{
		$this->table_name = "test";
		$this->data = ['name' => 'John Doe', 'email' => 'jdoe@gmail.com', 'password' => 'pass123'];
	}
	
	public function testInsertQuery()
	{
		$expected = "Insert into test(name, email, password) Values(:name, :email, :password)";
		$this->assertSame($expected, QueryConstructor::constructInsertQuery($this->data, $this->table_name));
	}

	public function testSelectQuery()
	{
		$condition = "id = 1";
		$expected = "Select * from test where id = 1";
		$this->assertSame($expected, QueryConstructor::constructSelectQuery($condition, $this->table_name));
	}

	public function testDeleteQuery()
	{
		$condition = "id = 1";
		$expected = "Delete from test where id = 1";
		$this->assertSame($expected, QueryConstructor::constructDeleteQuery($condition, $this->table_name));
	}

	public function testUpdateQuery()
	{
		$condition = "id = 1";
		$expected = "Update test SET name = 'John Doe', email = 'jdoe@gmail.com', password = 'pass123' where id = 1";
		$this->assertSame($expected, QueryConstructor::constructUpdateQuery($this->data, $condition, $this->table_name));
		
	}

}
