<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Session;
use App\Helpers\Dashboard;


class DashboardController extends Controller
{

    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }


    /**
     * [showDashboard This function use to redirect Authenticate user in dashboard page]
     *
     */
    public function showDashboard()
    {
      $sdbname =$this->databasename();
      $username = Session::get('USERNAME');
      $attncnt=Dashboard::attendence_count($sdbname,$username);
      $totalcusorderpricecnt=Dashboard::tot_customer_order_price($sdbname);
      $totalorder=Dashboard::tot_order($sdbname);
      return view('dashboard.index',compact('attncnt','totalcusorderpricecnt','totalorder'));
    }

}
