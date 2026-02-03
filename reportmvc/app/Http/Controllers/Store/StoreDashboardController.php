<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use DB;
use App\Helpers\StoreDashboard;


class StoreDashboardController extends Controller
{

    /**
     * [showDashboard This function use to redirect Authenticate user in dashboard page]
     *
     */
    public function showDashboard()
    {
      
      $storelists=StoreDashboard::getAllStoreList();
      return view('/store/dashboard.index',compact('storelists'));
    }

}

?>
