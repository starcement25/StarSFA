<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use App\Database\DbOnTheFly;

use DB;
use Session;
class LoginController extends Controller
{
    /**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname)
    {
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }

     /**
     * [showLogin This function use for display login view page ]
     *
     */
    public function showLogin()
    {
        if (!Session::get('DBNAME') && !Session::get('USERNAME')) {
            return view('login.index');
        }
        else {
			if(Session::get('DBNAME')=='acedns_EMAMIT' || Session::get('DBNAME')=='acedns_EMAMI'){
				return redirect('/reverseauctionprice');
			}
			else{
           		return redirect('/dashboard');
			}
		}
    }
    /**
     * [doLogin This function check database avilable or not then check given username and password are vaild or not]
     * @param  Request $request    [Get the value from login form]
     * @return [boolean]           [Its return access true or false]
     */
    public function doLogin(Request $request)
    {
       $db_name='acedns_'.strtoupper($request->input('nickname'));
       $nickname=$request->input('nickname');
       $username=$request->input('username');
       $password=$request->input('password');
       $dydb =$this->dydb($db_name);
       $CUTDB = $dydb->getConnection();

       $this->validate($request, [
             'nickname' => 'required',
             'username' => 'required',
             'password' => 'required',
       ]);

                 $nicknamecheck =DB::table('user_details')
                                 ->where('nick_name', '=' ,$nickname)
                                 ->first();

                 if(count($nicknamecheck)==1){
                   $response = $CUTDB->table('admin_master')
                             ->where('admin_login', '=' ,$username)->where('admin_pwd', '=', $password)
                             ->get();
                  //dd($response);exit;
                   if(count($response)==1){
                     session(['DBNAME' => $db_name]);
                     session(['USERNAME' => $username]);
                     session(['LOGONAME' => $nicknamecheck->logo]);
					 session(['nick_name' => $nickname]);
					 if(strtoupper($db_name)=='ACEDNS_EMAMIT' || strtoupper($db_name)=='ACEDNS_EMAMI')
					{
						return redirect('/reverseauctionprice');
					}
					else
					{
                      return redirect('/dashboard');
					}
                   } else {
                     return redirect('/')->withErrors(['Username Or Password Wrong']);
                   }
                 }
                 else {
                   return redirect('/')->withErrors(['You Have Enter Wrong Nickname']);;
                 }
    }
    /**
     * [showDashboard This function use to redirect Authenticate user in dashboard page]
     *
     */
    public function showDashboard()
    {
 	    return view('dashboard.index');
    }
    /**
     * [getLogout This function remove all the session value]
     *
     */
    public function getLogout()
    {
   	  Session::flush ();
   	  return redirect('/');
    }
}
