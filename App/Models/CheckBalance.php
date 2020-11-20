<?php
namespace App\Models;

use PDO;
use \App\Config;
use \App\Flash;
use \App\Controllers\Balance;


class CheckBalance extends \Core\Model
{
	 public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public static function loadIncomes()
	{
		
			$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
			
			//var_dump($start_date_final);
			//var_dump($end_date_final);
			
			$sql=("SELECT name, SUM(amount) as amount FROM (SELECT c.name, i.amount FROM incomes_category_assigned_to_users AS c INNER JOIN incomes AS i ON c.id = i.income_category_assigned_to_user_id WHERE c.user_id='$user_id' AND i.date_of_income >= '$start_date_final' AND i.date_of_income <= '$end_date_final') AS t GROUP BY name;");
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $result;
		
	}
	
	public static function incomesSummary()
	{
		
		$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
			
			//var_dump($start_date_final);
			//var_dump($end_date_final);		
		
		$sql=("SELECT SUM(amount) as sum FROM (SELECT name, SUM(amount) as amount FROM (SELECT c.name, i.amount FROM incomes_category_assigned_to_users AS c INNER JOIN incomes AS i ON c.id = i.income_category_assigned_to_user_id WHERE c.user_id='$user_id' AND i.date_of_income >= '$start_date_final' AND i.date_of_income <= '$end_date_final') AS t GROUP BY name) as PREV_TABLE;");
		$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $result;
	}
	
	public static function loadExpenses()
	{
		
			$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
			
			//var_dump($start_date_final);
			//var_dump($end_date_final);
			
			$sql=("SELECT name, SUM(amount) as amount FROM (SELECT c.name, e.amount FROM expenses_category_assigned_to_users AS c INNER JOIN expenses AS e ON c.id = e.expense_category_assigned_to_user_id WHERE c.user_id='$user_id' AND e.date_of_expense >= '$start_date_final' AND e.date_of_expense <= '$end_date_final') AS t GROUP BY name;");
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $result;
		
	}
	
	public static function expensesSummary()
	{
		
		$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
			
			//var_dump($start_date_final);
			//var_dump($end_date_final);		
		
		$sql=("SELECT SUM(amount) as sum FROM (SELECT name, SUM(amount) as amount FROM (SELECT c.name, e.amount FROM expenses_category_assigned_to_users AS c INNER JOIN expenses AS e ON c.id = e.expense_category_assigned_to_user_id WHERE c.user_id='$user_id' AND e.date_of_expense >= '$start_date_final' AND e.date_of_expense <= '$end_date_final') AS t GROUP BY name) as PREV_TABLE;");
		$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $result;
	}
	
	public static function getBalance()
	{
		$expenses = CheckBalance::expensesSummary();
		$expenses = $expenses[0];
		$expenses = $expenses["sum"];
		$expenses = intval($expenses);
		
		$incomes =  CheckBalance::incomesSummary();
		$incomes = $incomes[0];
		$incomes =  $incomes["sum"];
		$incomes =  intval($incomes);
		
		$balance = $incomes-$expenses;
		return $balance;
	}
	
	public static function showBalanceComment()
	{
		$balance=CheckBalance::getBalance();
		if($balance>2000 )
								$comment = "In this time you'ra a financial ninja! Great job!";
							if($balance<=2000 && $balance>0)
								$comment = "it's ok, but I know you can make your bilance much better!";
							if($balance>=-2000 && $balance<=0 )
								$comment = "Not good my friend... Isn't it better to be on +?";
							if($balance<-2000)
								$comment = "OMG, what a shame - definitely you have to start planning it better...?";
								
								return $comment;
	}
	
	public static function getIncomesPiechartData()
	{
			$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
		
			$sql=("SELECT name, SUM(amount) as amount FROM (SELECT c.name, i.amount FROM incomes_category_assigned_to_users AS c INNER JOIN incomes AS i ON c.id = i.income_category_assigned_to_user_id WHERE c.user_id='$user_id' AND i.date_of_income >= '$start_date_final' AND i.date_of_income <= '$end_date_final') AS t GROUP BY name;");
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
			
			$result = CheckBalance::piechartDataStringToFloat($result);
			//$result = CheckBalance::piechartDataRenameColumn($result);
		
			//$result = array_map('intval', array_column($result, 'SUM(amount)'));
			
			//var_dump($result);
			return $result;
	}
	
	public static function getExpensesPiechartData()
	{
			$user_id=$_SESSION['user_id'];
			$balance = new Balance($_SESSION['user_id']);
			$dates = $balance->getDates();

			//var_dump($dates);
			
			$start_date_final = $dates["start_date"];
			$end_date_final = $dates["end_date"];
		
			$sql=("SELECT name, SUM(amount) as amount FROM (SELECT c.name, e.amount FROM expenses_category_assigned_to_users AS c INNER JOIN expenses AS e ON c.id = e.expense_category_assigned_to_user_id WHERE c.user_id='$user_id' AND e.date_of_expense >= '$start_date_final' AND e.date_of_expense <= '$end_date_final') AS t GROUP BY name;");
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
			
			$result = CheckBalance::piechartDataStringToFloat($result);
			//$result = CheckBalance::piechartDataRenameColumn($result);
		
			//$result = array_map('intval', array_column($result, 'SUM(amount)'));
			
			//var_dump($result);
			return $result;
	}
	
	public static function piechartDataStringToFloat($result)
    {
        $arrayWithFloats = [];

        foreach ($result as $item)
        {
            $item["amount"] = (float)$item["amount"];
            $arrayWithFloats[] = $item;
        }
        
        return $arrayWithFloats;
        
    }
	/*
	public static function piechartDataRenameColumn($result)
    {
		$result = array_map(function($tag) {
			return array(
				'name' => $tag['name'],
				'sum' => $tag['SUM(amount)']
			);
		}, $result);
		return $result;
		
        
    }
*/
}