<?php
namespace App\Models;

use PDO;
use \App\Config;
use \App\Flash;


class AddExpense extends \Core\Model
{
	 public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public static function loadCategories($user_id)
	{							

					
			$sql = "SELECT name, id FROM expenses_category_assigned_to_users WHERE user_id='$user_id'";
						
			$db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return($result);
	}
	
	public static function loadWaysOfPayment($user_id)
	{
			
		$sql=("SELECT name FROM payment_methods_assigned_to_users WHERE user_id='$user_id'");
		$db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return($result);
										
	}
	
	public static function submitExpense()
	{
		
		$amount_of_money=$_POST['money'];
		$amount_of_money = str_replace(',','.', $amount_of_money);
		$date_of_transaction=$_POST['date'];
		$expense_type=$_POST['expense_type'];
		$payment_method=$_POST['way-of-payment'];
		$comment=$_POST['comment'];
		$user_id = $_SESSION['user_id'];
		
		$sql= "INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES ('$user_id', '$expense_type', '$payment_method', '$amount_of_money', '$date_of_transaction', '$comment') ";

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
	}
}