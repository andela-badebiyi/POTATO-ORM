<?php	
include "vendor/autoload.php";
use app\Model;

class Guest extends Model
{

}

if (Guest::destroy(1) == false) {
	echo "Did NOT deleted";
} 
else {
	echo "Record deleted";
}
?>