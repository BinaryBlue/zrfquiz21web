<?php namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;

class DashboardController extends Controller {

	public function __construct()
	{
		parent::__construct();
		
        $this->data = array(
            'pageTitle' =>  $this->config['cnf_appname'],
            'pageNote'  =>  'Welcome to Dashboard',
            
        );			
	}

	public function index( Request $request )
	{
		return view('dashboard.index',$this->data);
	}	


	


}