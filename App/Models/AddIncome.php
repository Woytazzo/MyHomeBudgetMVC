<?php
namespace App\Models;

use PDO;

class AddIncome extends \Core\Model
{
	 public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
		public static function loadCategories($user_id)
	{							

					
			$sql = "SELECT name, id FROM incomes_category_assigned_to_users WHERE user_id='$user_id'";
						
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
		$income_type=$_POST['income_type'];
		$comment=$_POST['comment'];
		$user_id = $_SESSION['user_id'];
		
		$sql= "INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES ('$user_id', '$income_type', '$amount_of_money', '$date_of_transaction', '$comment') ";

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
	}

}