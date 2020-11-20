<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\GeneralFunctions;
use \App\Models\AddExpense;;

class Expense extends Authenticated
{

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

   public function addAction()
   {
	   $addExpense = new AddExpense($_POST);
	   if($addExpense->money<0.01)
	   {
		   Flash::addMessage("Please insert amount", Flash::WARNING);
		  $this->newAction();
	   }
	   else
	   {
		  $addExpense->submitExpense();
		   View::renderTemplate('Success/new.html');
	   }
   }

	public function newAction()
    {
		$arg['today'] = GeneralFunctions::getToday();
		$arg['categories'] = AddExpense::loadCategories($_SESSION['user_id']);
		$arg['payment'] = AddExpense::loadWaysOfPayment($_SESSION['user_id']);
        View::renderTemplate('Expense/add_expense.html', $arg);
    }
}
