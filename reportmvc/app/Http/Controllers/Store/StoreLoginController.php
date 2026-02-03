<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Requests;
use DB;
use Session;
use App\Helpers\Commonfunctions;

class StoreLoginController extends Controller
{


       /**
       * [showLogin This function use for display login view page ]
       *
       */
      public function showLogin()
      {
        if (!Session::get('STOREUSERNAME')) {

            //return View::make("/store");
            return view('store/login.index');
        }
        else {
           return redirect('/store/dashboard');
        }
      }

      /**
       * [doLogin This function check database avilable or not then check given username and password are vaild or not]
       * @param  Request $request    [Get the value from login form]
       * @return [boolean]           [Its return access true or false]
       */
      public function doLogin(Request $request)
      {
         $username=$request->input('username');
         $password=$request->input('password');


         $this->validate($request, [
               'username' => 'required',
               'password' => 'required',
         ]);

          $response =DB::table('user_access')
                   ->where('user_name', '=' ,$username)->where('password', '=', $password)
                   ->get();
          if(count($response)==1){
              session(['STOREUSERNAME' => $username]);
              session(['CREATERID' => 1]);
              return redirect('/store/dashboard');
           } else {
              return redirect('/store')->withErrors(['Username Or Password Wrong']);
           }

      }

      /**
       * [getLogout This function remove all the session value]
       *
       */
      public function getLogout()
      {
        Session::flush ();
        return redirect('/store');
      }


}
