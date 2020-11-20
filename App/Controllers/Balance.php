<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\GeneralFunctions;
use \App\Models\CheckBalance;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Balance extends Authenticated
{
  
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }
    
    public function checkAction()
    {
		$arg['balancePeriod'] = $this->getBalancePeriod();
		$arg['dates'] = $this->getDates();
		$arg['today'] = GeneralFunctions::getToday();
		$arg['chosen_dates'] = $this->getChosenDates();
		$arg['incomesDates'] = CheckBalance::loadIncomes();
		$arg['incomesSummary'] = CheckBalance::incomesSummary();
		$arg['expensesDates'] = CheckBalance::loadExpenses();
		$arg['expensesSummary'] = CheckBalance::expensesSummary();
		$arg['balance'] = CheckBalance::getBalance();
		$arg['balanceComment'] = CheckBalance::showBalanceComment();
		$arg['incomesPiechart'] = CheckBalance::getIncomesPiechartData();
		$arg['expensesPiechart'] = CheckBalance::getExpensesPiechartData();
		
		//var_dump($arg);
        View::renderTemplate('Balance/check.html', $arg);
    }
	
	public function getChosenDates()
	{
		if(array_key_exists('startdate', $_POST))
			$dates['startdate']=$_POST['startdate'];
		if(array_key_exists('enddate', $_POST))
			$dates['enddate']=$_POST['enddate'];
		if(isset($dates))
			return $dates;
			return null;
	}
	
	public function getDatesForIrregularPeriod()
	{
		
	}
	
	public function getBalancePeriod()
	{
		if(array_key_exists('chooseBalancePeriod', $_POST))
		$period=$_POST['chooseBalancePeriod'];
		else $period = "1";
		return $period;
	}
    
	 public function getDates()
	 {
		 $period=$this->getBalancePeriod();
		 
		 if($period == 1){
			$start=date("Y-m-01");
			$end=strtotime($start. ' +1 month - 1 day');

			$end = date("Y-m-d",$end);
			//$arg['value_period']=1;
			$arg['start_date']=$start;
			$arg['end_date']=$end;
		}

		if($period == 2){
			
			$end=date("Y-m-01");
			$start=strtotime($end. ' - 1 month');
			$start = date("Y-m-d",$start);
			$end=strtotime($end. ' - 1 day');
			$end = date("Y-m-d",$end);
			//$arg['value_period']=2;
		$arg['start_date']=$start;
		$arg['end_date']=$end;
		}

		if($period == 3){
			$start=date('Y-01-01');
			$end=strtotime($start. ' + 1 year - 1 day');
			$end=date('Y-m-d', $end);
			//$arg['value_period']=3;
			$arg['start_date']=$start;
			$arg['end_date']=$end;
		}

		if($period == 4){
			if(isset($_POST['startdate'])){
					if(isset($_POST['enddate'])){
			$start=$_POST['startdate'];
		
			$end=$_POST['enddate'];
			//$arg['value_period']=3;
			$arg['start_date']=$start;
			$arg['end_date']=$end;
			}}
			//$arg['end_date']=date('Y-01-01');
			/*
			if(isset($_SESSION['start_date2']))
			$start_date_final = $_SESSION['start_date2'];

			if(isset($_SESSION['end_date2']))
			$end_date_final = $_SESSION['end_date2'];
				
			else{
			if(isset($_SESSION['start_date']))
			$start_date_final = $_SESSION['start_date'];

			if(isset($_SESSION['end_date']))
			$end_date_final = $_SESSION['end_date'];
			}
			*/
		}
if(isset($arg))
		return $arg;
	}
}