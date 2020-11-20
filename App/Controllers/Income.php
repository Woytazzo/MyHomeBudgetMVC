<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\GeneralFunctions;
use \App\Models\AddIncome;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     *
     * @return void
     */
	 public function addAction()
   {
	   $addIncome = new AddIncome($_POST);
	   if($addIncome->money<0.01)
	   {
		   Flash::addMessage("Please insert amount", Flash::WARNING);
		  $this->newAction();
	   }
	   else
	   {
		  $addIncome->submitExpense();
		   View::renderTemplate('Success/new.html');
	   }
   }
	 
    public function newAction()
    {
		$arg['today'] = GeneralFunctions::getToday();
		$arg['categories'] = AddIncome::loadCategories($_SESSION['user_id']);
        View::renderTemplate('Income/add_income.html', $arg);
    }
	

	
}
